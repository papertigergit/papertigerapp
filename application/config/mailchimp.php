<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| Mailchimp (Marketing API 3.0), used by helpers/mcapiclass_helper.php from sign-up, plan change, billing,
| cancel and the newsletter setting.
|
| mailchimp_enabled   TRUE: the pages send their Mailchimp calls, as the app always did (decision D9).
|                     FALSE: no Mailchimp calls at all (the pages work the same, nothing is sent).
|                     Development/test copies should set FALSE in config/development/mailchimp.php (untracked),
|                     so they never write to the real list.
| mailchimp_api_base  optional, e.g. a test server; default https://<data centre of the key>.api.mailchimp.com/3.0
| mailchimp_timeout   optional, seconds per request (default 10)
| mailchimp_api_key   optional, for test copies: replaces the API key the pages pass (the live key in their code)
| mailchimp_list_id   optional, for test copies: replaces the list (audience) ID the pages pass
| mailchimp_test_email optional, for test copies: every call uses this address instead of the user's own
|                     Set them in config/development/mailchimp.php to send every call to a test account.
|                     Production leaves them unset, so the pages' own key and list are used as before.
*/
$config['mailchimp_enabled'] = TRUE;
