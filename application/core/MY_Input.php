<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PaperTiger: keep CodeIgniter 3.1's global input filtering (Phase 4, decided 2026-11-09).
 *
 * config.php has global_xss_filtering = TRUE. In CodeIgniter 3.1.11 that means:
 *  1. when the request starts, $_GET, $_POST and $_COOKIE are cleaned once (invalid UTF-8 and control characters
 *     removed, key names checked) and tags are stripped from $_SERVER['PHP_SELF'];
 *  2. input->get(), post(), post_get(), get_post(), cookie(), server(), input_stream() and user_agent() pass the
 *     value through the XSS cleaner unless the caller passes FALSE.
 * The maintained fork (pocketarc/codeigniter, based on CodeIgniter 3.2) removed both. This class adds them back with
 * 3.1.11's code, and only when the parent class no longer does it itself - on 3.1.11 it changes nothing.
 *
 * Removing this class later is a deliberate behaviour change: input would be stored exactly as typed, and every
 * page that prints it would then have to escape it.
 */
class MY_Input extends CI_Input {

	/** @var bool  the parent class still has CodeIgniter 3.1's global filtering */
	protected $_pt_parent_filters;

	/** @var bool  config global_xss_filtering === TRUE */
	protected $_pt_xss;

	/** @var CI_Utf8 */
	protected $_pt_uni;

	/**
	 * @param CI_Security $security  passed by the fork's CodeIgniter.php (load_class('Input', 'core', $SEC));
	 *                               CodeIgniter 3.1.x passes nothing
	 */
	public function __construct(&$security = NULL)
	{
		if ($security === NULL)
		{
			parent::__construct();
		}
		else
		{
			parent::__construct($security);
		}

		$this->_pt_parent_filters = method_exists('CI_Input', '_sanitize_globals');
		$this->_pt_xss = (config_item('global_xss_filtering') === TRUE);

		if ( ! $this->_pt_parent_filters)
		{
			if (UTF8_ENABLED === TRUE)
			{
				$this->_pt_uni =& load_class('Utf8', 'core');
			}
			$this->_pt_sanitize_globals();
		}
	}

	// --------------------------------------------------------------------
	// 2. XSS cleaning by default (3.1.11: "is_bool($xss_clean) OR $xss_clean = $this->_enable_xss;")

	protected function _pt_xss_default($xss_clean)
	{
		return is_bool($xss_clean) ? $xss_clean : $this->_pt_xss;
	}

	public function get($index = NULL, $xss_clean = NULL)
	{
		return parent::get($index, $this->_pt_xss_default($xss_clean));
	}

	public function post($index = NULL, $xss_clean = NULL)
	{
		return parent::post($index, $this->_pt_xss_default($xss_clean));
	}

	public function post_get($index, $xss_clean = NULL)
	{
		return parent::post_get($index, $this->_pt_xss_default($xss_clean));
	}

	public function get_post($index, $xss_clean = NULL)
	{
		return parent::get_post($index, $this->_pt_xss_default($xss_clean));
	}

	public function cookie($index = NULL, $xss_clean = NULL)
	{
		return parent::cookie($index, $this->_pt_xss_default($xss_clean));
	}

	public function server($index, $xss_clean = NULL)
	{
		return parent::server($index, $this->_pt_xss_default($xss_clean));
	}

	public function input_stream($index = NULL, $xss_clean = NULL)
	{
		return parent::input_stream($index, $this->_pt_xss_default($xss_clean));
	}

	public function user_agent($xss_clean = NULL)
	{
		return parent::user_agent($this->_pt_xss_default($xss_clean));
	}

	// --------------------------------------------------------------------
	// 1. Cleaning the superglobals once per request: CI_Input::_sanitize_globals(), _clean_input_data() and
	//    _clean_input_keys() of CodeIgniter 3.1.11, unchanged except for the names (and without the PHP < 5.4
	//    magic-quotes branch, which never ran on PHP 5.4+).

	protected function _pt_sanitize_globals()
	{
		// Is $_GET data allowed? If not we'll set the $_GET to an empty array
		if (config_item('allow_get_array') === FALSE)
		{
			$_GET = array();
		}
		elseif (is_array($_GET))
		{
			foreach ($_GET as $key => $val)
			{
				$_GET[$this->_pt_clean_input_keys($key)] = $this->_pt_clean_input_data($val);
			}
		}

		// Clean $_POST Data
		if (is_array($_POST))
		{
			foreach ($_POST as $key => $val)
			{
				$_POST[$this->_pt_clean_input_keys($key)] = $this->_pt_clean_input_data($val);
			}
		}

		// Clean $_COOKIE Data
		if (is_array($_COOKIE))
		{
			// Also get rid of specially treated cookies that might be set by a server
			// or silly application, that are of no use to a CI application anyway
			// but that when present will trip our 'Disallowed Key Characters' alarm
			// http://www.ietf.org/rfc/rfc2109.txt
			// note that the key names below are single quoted strings, and are not PHP variables
			unset(
				$_COOKIE['$Version'],
				$_COOKIE['$Path'],
				$_COOKIE['$Domain']
			);

			foreach ($_COOKIE as $key => $val)
			{
				if (($cookie_key = $this->_pt_clean_input_keys($key)) !== FALSE)
				{
					$_COOKIE[$cookie_key] = $this->_pt_clean_input_data($val);
				}
				else
				{
					unset($_COOKIE[$key]);
				}
			}
		}

		// Sanitize PHP_SELF
		$_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);

		log_message('debug', 'Global POST, GET and COOKIE data sanitized');
	}

	protected function _pt_clean_input_data($str)
	{
		if (is_array($str))
		{
			$new_array = array();
			foreach (array_keys($str) as $key)
			{
				$new_array[$this->_pt_clean_input_keys($key)] = $this->_pt_clean_input_data($str[$key]);
			}
			return $new_array;
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			$str = $this->_pt_uni->clean_string($str);
		}

		// Remove control characters
		$str = remove_invisible_characters($str, FALSE);

		// Standardize newlines if needed
		if ((bool) config_item('standardize_newlines') === TRUE)
		{
			return preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $str);
		}

		return $str;
	}

	protected function _pt_clean_input_keys($str, $fatal = TRUE)
	{
		if ( ! preg_match('/^[a-z0-9:_\/|-]+$/i', $str))
		{
			if ($fatal === TRUE)
			{
				return FALSE;
			}
			else
			{
				set_status_header(503);
				echo 'Disallowed Key Characters.';
				exit(7); // EXIT_USER_INPUT
			}
		}

		// Clean UTF-8 if supported
		if (UTF8_ENABLED === TRUE)
		{
			return $this->_pt_uni->clean_string($str);
		}

		return $str;
	}
}
