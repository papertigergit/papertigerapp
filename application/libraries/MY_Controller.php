<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class MY_Controller extends Controller {

	var $user_data;
	var $organization_data;
	var $subscription_data = NULL;
	var $suspended_message = FALSE;

	function MY_Controller() {	
	
/* 		lets try custom php error handling */

		// set to the user defined error handler
/* 		$old_error_handler = set_error_handler("myErrorHandler"); */
	
		parent::Controller();
		
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

}

function myErrorHandler($errno, $errstr, $errfile, $errline) {

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
