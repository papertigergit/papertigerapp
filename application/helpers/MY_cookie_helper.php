<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * PaperTiger: CodeIgniter 3.1's get_cookie() (Phase 4).
 *
 * In 3.1.11, get_cookie() without a second argument uses the global_xss_filtering setting (TRUE in config.php);
 * in the maintained fork it defaults to no XSS cleaning. The remember-me login (Authentication.php) reads its
 * cookies with get_cookie(), so this keeps 3.1.11's function. CodeIgniter loads this file before the system
 * cookie helper, whose own get_cookie() is then skipped (function_exists); the other cookie functions still come
 * from the system helper. Code copied unchanged from CodeIgniter 3.1.11 system/helpers/cookie_helper.php.
 */

if ( ! function_exists('get_cookie'))
{
	/**
	 * Fetch an item from the COOKIE array
	 *
	 * @param	string
	 * @param	bool
	 * @return	mixed
	 */
	function get_cookie($index, $xss_clean = NULL)
	{
		is_bool($xss_clean) OR $xss_clean = (config_item('global_xss_filtering') === TRUE);
		$prefix = isset($_COOKIE[$index]) ? '' : config_item('cookie_prefix');
		return get_instance()->input->cookie($prefix.$index, $xss_clean);
	}
}
