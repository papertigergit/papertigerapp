<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PaperTiger: keep CodeIgniter 3.1's view-variable sharing (Phase 4).
 *
 * In CodeIgniter 3.1.11, every variable passed to $this->load->view() is remembered and available to every view
 * loaded later in the same request (CI_Loader::_ci_load(): "$this->_ci_cached_vars = array_merge(...)"). The app
 * relies on it: controllers pass $data to the header and load the footer without data, and the footer prints
 * $user_data. The maintained fork keeps a view's variables to that view (and the views it loads), so the footer
 * lost them. view() below does 3.1.11's merge first and then lets the fork render the view as usual.
 */
class MY_Loader extends CI_Loader {

	public function view($view, $vars = array(), $return = FALSE)
	{
		$this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $this->_ci_prepare_view_vars($vars));
		return parent::view($view, $vars, $return);
	}
}
