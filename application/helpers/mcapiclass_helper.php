<?php
/*
 * Mailchimp client used by sign-up, plan change, billing, cancel and the newsletter setting
 * (controllers include this file and call listSubscribe / listUnsubscribe).
 *
 * This class used to speak Mailchimp API 1.2. Mailchimp switched API 1.x off in April 2019, so every call has
 * failed since then. The class keeps its name and the list methods with the same arguments, and now sends them
 * to the Marketing API 3.0. The other 1.2 methods (campaigns, reports, ...) were never called and are gone.
 *
 * Nothing is sent unless config/mailchimp.php sets mailchimp_enabled = TRUE; while it is off every method returns
 * FALSE with errorCode -98. As before, failures never throw: methods return FALSE and set errorCode/errorMessage
 * (HTTP status from Mailchimp, -99 = could not connect, or the 1.x code where the check is made here:
 * 212 unsubscribed/cleaned address, 214 already subscribed, 232 unknown address, 270 unknown interest group).
 *
 * 1.x behaviour kept: string flags such as 'false' (as the controllers pass them) mean false; update_existing=false
 * leaves an existing member untouched (214); an address that unsubscribed or bounced is never re-subscribed by the
 * API (212); GROUPINGS ("name" + comma separated "groups", "\," escapes a comma) become 3.0 interests, replacing the
 * member's groups in that grouping unless replace_interests is false. send_welcome, send_goodbye and send_notify
 * have no per-call equivalent in 3.0 (they are list settings there) and are ignored.
 */
class MCAPI {
    public $version = '3.0';
    public $errorMessage = '';
    public $errorCode = '';
    public $api_key;
    public $secure = true;      // kept for callers; 3.0 is HTTPS only
    public $timeout = 10;       // seconds per request (1.2 waited up to 300)
    public $enabled = false;
    public $apiBase;            // https://<dc>.api.mailchimp.com/3.0, or config mailchimp_api_base

    private $categories = array();

    function __construct($username_or_apikey, $password = null, $secure = false) {
        $this->api_key = (string) $username_or_apikey;
        $parts = explode('-', $this->api_key, 2);
        $dc = (isset($parts[1]) && preg_match('/^[a-z]{2,}[0-9]+$/', $parts[1])) ? $parts[1] : 'us1';
        $this->apiBase = 'https://' . $dc . '.api.mailchimp.com/3.0';
        if (function_exists('get_instance') && is_object($CI = get_instance())) {
            $CI->config->load('mailchimp', FALSE, TRUE);
            $this->enabled = (bool) $CI->config->item('mailchimp_enabled');
            if ($CI->config->item('mailchimp_api_base')) {
                $this->apiBase = rtrim($CI->config->item('mailchimp_api_base'), '/');
            }
            if ($CI->config->item('mailchimp_timeout')) {
                $this->timeout = (int) $CI->config->item('mailchimp_timeout');
            }
        }
    }

    function setTimeout($seconds) {
        if (is_int($seconds)) {
            $this->timeout = $seconds;
            return true;
        }
    }

    function getTimeout() {
        return $this->timeout;
    }

    function useSecure($val) {
        $this->secure = ($val === true);
    }

    /**
     * Subscribe an address, or update it when $update_existing is true. Returns true on success.
     */
    function listSubscribe($id, $email_address, $merge_vars, $email_type = 'html', $double_optin = true, $update_existing = false, $replace_interests = true, $send_welcome = false) {
        if (!$this->start($email_address)) {
            return false;
        }
        $email = trim($email_address);
        $merge = is_array($merge_vars) ? $merge_vars : array();
        $groupings = isset($merge['GROUPINGS']) ? $merge['GROUPINGS'] : null;
        unset($merge['GROUPINGS']);
        $interests = $this->interests($id, $groupings, self::flag($replace_interests));
        if ($interests === false) {
            return false;
        }

        $path = $this->member_path($id, $email);
        $member = $this->request('GET', $path . '?fields=status');
        if ($member === false && $this->errorCode !== 404) {
            return false;
        }
        $status = ($member !== false && isset($member['status'])) ? $member['status'] : null;
        $is_new = ($status === null || $status === 'archived' || $status === 'transactional');
        if ($status === 'unsubscribed' || $status === 'cleaned') {
            return $this->fail(212, $email . ' has unsubscribed or bounced and cannot be re-subscribed by the API');
        }
        if (!$is_new && !self::flag($update_existing)) {
            return $this->fail(214, $email . ' is already subscribed to list ' . $id);
        }

        $body = array('email_address' => $email, 'email_type' => ($email_type === 'text') ? 'text' : 'html');
        if ($is_new) {
            $body['status'] = self::flag($double_optin) ? 'pending' : 'subscribed';
        }
        if (isset($merge['OPTINIP'])) {
            $body['ip_opt'] = (string) $merge['OPTINIP'];
            unset($merge['OPTINIP']);
        }
        if ($merge) {
            $body['merge_fields'] = $merge;
        }
        if ($interests) {
            $body['interests'] = $interests;
        }
        return $this->request('PUT', $path, $body) !== false;
    }

    /**
     * Unsubscribe an address (or archive it when $delete_member is true). Returns true on success.
     */
    function listUnsubscribe($id, $email_address, $delete_member = false, $send_goodbye = true, $send_notify = true) {
        if (!$this->start($email_address)) {
            return false;
        }
        $email = trim($email_address);
        $path = $this->member_path($id, $email);
        $r = self::flag($delete_member) ? $this->request('DELETE', $path) : $this->request('PATCH', $path, array('status' => 'unsubscribed'));
        if ($r === false && $this->errorCode === 404) {
            return $this->fail(232, 'There is no record of ' . $email . ' in list ' . $id);
        }
        return $r !== false;
    }

    /**
     * Change an existing member's merge fields, groups ("GROUPINGS"), address ("EMAIL") or email type.
     */
    function listUpdateMember($id, $email_address, $merge_vars, $email_type = '', $replace_interests = true) {
        if (!$this->start($email_address)) {
            return false;
        }
        $merge = is_array($merge_vars) ? $merge_vars : array();
        $interests = $this->interests($id, isset($merge['GROUPINGS']) ? $merge['GROUPINGS'] : null, self::flag($replace_interests));
        if ($interests === false) {
            return false;
        }
        unset($merge['GROUPINGS']);
        $body = array();
        if (isset($merge['EMAIL'])) {
            $body['email_address'] = (string) $merge['EMAIL'];
            unset($merge['EMAIL']);
        }
        if ($email_type === 'html' || $email_type === 'text') {
            $body['email_type'] = $email_type;
        }
        if ($merge) {
            $body['merge_fields'] = $merge;
        }
        if ($interests) {
            $body['interests'] = $interests;
        }
        $r = $this->request('PATCH', $this->member_path($id, trim($email_address)), $body);
        if ($r === false && $this->errorCode === 404) {
            return $this->fail(232, 'There is no record of ' . trim($email_address) . ' in list ' . $id);
        }
        return $r !== false;
    }

    // ---- internals ----

    private function start($email_address) {
        $this->errorCode = '';
        $this->errorMessage = '';
        if (!$this->enabled) {
            return $this->fail(-98, 'Mailchimp calls are switched off (config/mailchimp.php mailchimp_enabled)');
        }
        if (trim((string) $email_address) === '') {
            return $this->fail(502, 'Invalid Email Address: empty');
        }
        return true;
    }

    private function fail($code, $message) {
        $this->errorCode = $code;
        $this->errorMessage = $message;
        return false;
    }

    private function member_path($list, $email) {
        return '/lists/' . rawurlencode($list) . '/members/' . md5(strtolower($email));
    }

    /** 'false', '0', '', 'no', 'off' and false/0/null are false; anything else is true. */
    private static function flag($value) {
        if (is_string($value)) {
            return !in_array(strtolower(trim($value)), array('', '0', 'false', 'no', 'off'), true);
        }
        return (bool) $value;
    }

    /** 1.x GROUPINGS -> 3.0 interests array (interest id => true/false), array() if none, false on error. */
    private function interests($list, $groupings, $replace) {
        $out = array();
        if (!is_array($groupings)) {
            return $out;
        }
        foreach ($groupings as $grouping) {
            if (!is_array($grouping) || !isset($grouping['groups'])) {
                continue;
            }
            $label = isset($grouping['name']) ? (string) $grouping['name'] : (isset($grouping['id']) ? (string) $grouping['id'] : '');
            $known = $this->category($list, $grouping, $label);
            if ($known === false) {
                return false;
            }
            if ($replace) {
                foreach ($known as $interest_id) {
                    $out[$interest_id] = false;
                }
            }
            foreach (preg_split('/(?<!\\\\),/', (string) $grouping['groups']) as $name) {
                $name = trim(str_replace('\\,', ',', $name));
                if ($name === '') {
                    continue;
                }
                if (!isset($known[$name])) {
                    return $this->fail(270, '"' . $name . '" is not a valid Interest Group for the grouping "' . $label . '" on list ' . $list);
                }
                $out[$known[$name]] = true;
            }
        }
        return $out;
    }

    /** Interest names => ids of the interest category named (or with the id) given in a 1.x grouping. */
    private function category($list, $grouping, $label) {
        if (!isset($this->categories[$list])) {
            $r = $this->request('GET', '/lists/' . rawurlencode($list) . '/interest-categories?count=1000&fields=categories.id,categories.title');
            if ($r === false) {
                return false;
            }
            $this->categories[$list] = isset($r['categories']) ? $r['categories'] : array();
        }
        foreach ($this->categories[$list] as &$category) {
            $match = isset($grouping['name']) ? ($category['title'] === (string) $grouping['name']) : ($category['id'] === (string) $grouping['id']);
            if (!$match) {
                continue;
            }
            if (!isset($category['interest_ids'])) {
                $r = $this->request('GET', '/lists/' . rawurlencode($list) . '/interest-categories/' . rawurlencode($category['id']) . '/interests?count=1000&fields=interests.id,interests.name');
                if ($r === false) {
                    return false;
                }
                $category['interest_ids'] = array();
                foreach (isset($r['interests']) ? $r['interests'] : array() as $interest) {
                    $category['interest_ids'][$interest['name']] = $interest['id'];
                }
            }
            return $category['interest_ids'];
        }
        return $this->fail(270, 'There is no grouping "' . $label . '" on list ' . $list);
    }

    /** One API call. Returns the decoded reply (array() when empty) or false with errorCode/errorMessage set. */
    private function request($method, $path, $body = null) {
        $this->errorCode = '';
        $this->errorMessage = '';
        $ch = curl_init($this->apiBase . $path);
        $headers = array('Accept: application/json');
        $options = array(
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => 'papertiger:' . $this->api_key,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_USERAGENT => 'PaperTiger-MCAPI/' . $this->version,
        );
        if ($body !== null) {
            $json = json_encode($body, JSON_INVALID_UTF8_SUBSTITUTE);
            if ($json === false) {
                curl_close($ch);
                return $this->fail(-99, 'Could not encode the request: ' . json_last_error_msg());
            }
            $options[CURLOPT_POSTFIELDS] = $json;
            $headers[] = 'Content-Type: application/json';
        }
        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $options);
        $raw = curl_exec($ch);
        if ($raw === false) {
            $message = 'Could not connect (ERR ' . curl_errno($ch) . ': ' . curl_error($ch) . ')';
            curl_close($ch);
            return $this->fail(-99, $message);
        }
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        $data = ($raw === '') ? array() : json_decode($raw, true);
        if ($status >= 200 && $status < 300) {
            return is_array($data) ? $data : array();
        }
        if (is_array($data) && isset($data['title'])) {
            return $this->fail($status, $data['title'] . (isset($data['detail']) ? ': ' . $data['detail'] : ''));
        }
        return $this->fail($status, 'HTTP ' . $status . ': ' . substr(trim(strip_tags((string) $raw)), 0, 200));
    }
}
