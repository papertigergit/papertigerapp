<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
#[AllowDynamicProperties]
class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * CI_Loader
	 *
	 * @var	CI_Loader
	 */
	public $load;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */

	var $user_data;
	var $organization_data;
	var $subscription_data = NULL;
	var $suspended_message = FALSE;

	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');

		$this->load->helper(array('ssl_helper'));

		$this->user_data = $this->session->userdata('user_data');
		
		// Hey Gabe, this was throwing php errors in the if(a OR b) below 
		// because $force_ssl wasnt always defined
		$force_ssl = FALSE;
		
		// if they are signed in, lets check if their organization forces SSL.
		if( $this->authentication->is_authenticated() ) {
			$this->load->model('organization_model');
			$organization_data = $this->organization_model->get_organization($this->user_data['organization_id']);		
			if($organization_data->num_rows() > 0) {
				$force_ssl = $organization_data->row()->force_ssl;
			}			
		}
		
		//controllers that will force ssl		

		if( preg_match("/account|login|signup/i", $this->uri->uri_string()) OR $force_ssl) {
			maintain_ssl(TRUE);
		} else {
			maintain_ssl(TRUE);
		}

		//if they are logged in, lets check if they are suspended..
		if( $this->authentication->is_authenticated() ) {
					
			$this->load->config('braintree');
			
			$this->organization_data = $organization_data->row();
		
			$grace_seconds = $this->config->item('grace_period');
		
			if( $this->organization_data->is_suspended == '1') {
			
				if( ($this->organization_data->suspended_date - time()) < $grace_seconds ) {
					$this->suspended_message = "Your account is not up to date with billing. Please update your billing information so it can be processed.";
				} else {
					$this->session->set_flashdata('error', 'This account is currently suspended.');
					$this->authentication->logout();
				}
			
			}
		}

		if( $this->authentication->is_authenticated() ) {
			$this->organization_data = $organization_data->row();
				if( $this->organization_data->package_id == '4') {
					$exclude_pages=array("account","login");
					$current_page=array_search($this->uri->segment('1'),$exclude_pages);
					if(is_numeric($current_page)==false):
						$this->session->set_flashdata('error', 'Your Beta Testing account has expired. Upgrade to continue using Paper Tiger Online.');
						redirect('account');
					endif;	
				}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

	public function myErrorHandler($errno, $errstr, $errfile, $errline) {

	    switch ($errno) {
	        case E_NOTICE:
	        case E_USER_NOTICE:
	            $errors = "Notice";
	            break;
	        case E_WARNING:
	        case E_USER_WARNING:
	            $errors = "Warning";
	            break;
	        case E_ERROR:
	        case E_USER_ERROR:
	            $errors = "Fatal Error";
	            break;
	        default:
	            $errors = "Unknown";
	            break;
	        }

		var_dump($errno);

		show_error($errstr, 500);

	    return true;
	}

}
