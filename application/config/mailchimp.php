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
*/
$config['mailchimp_enabled'] = TRUE;
