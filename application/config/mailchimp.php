<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| Mailchimp (Marketing API 3.0), used by helpers/mcapiclass_helper.php from sign-up, plan change, billing,
| cancel and the newsletter setting.
|
| mailchimp_enabled   FALSE: no Mailchimp calls at all (the pages work as before, nothing is sent).
|                     Switch on only after the API key and list have been checked against a test list.
| mailchimp_api_base  optional, e.g. a test server; default https://<data centre of the key>.api.mailchimp.com/3.0
| mailchimp_timeout   optional, seconds per request (default 10)
*/
$config['mailchimp_enabled'] = FALSE;
