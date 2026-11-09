<?php
class Authorizepost extends CI_Controller {   // machine-to-machine callback from Authorize.net: no user, no MY_Controller account checks
	function __construct() {
		parent::__construct();

		$code = 'orMvSpZbIi';
				
		if($this->uri->segment('3') != $code) {
			show_404();
			return;
		}
		
		$this->load->library('logger');
		
		$this->load->model('organization_model');
	}

	function index() {
		// Get the subscription ID if it is available. 
		// Otherwise $subscription_id will be set to zero.
		$subscription_id = (int) $this->input->post('x_subscription_id');
		 
		// Check to see if we got a valid subscription ID.
		// If so, do something with it.
		if ($subscription_id)
		{
			$this->logger->log_message(array('type' => 'silentpost', 'message' => 'silent post url hit for id '.$subscription_id));
			
			$organization = $this->organization_model->get_organization($subscription_id, 'subscription_id');
			
			if($organization->num_rows() == 0)
			{
				$this->logger->log_message(array('type' => 'silentpost', 'message' => 'no organization matches subscription id '.$subscription_id));
				return;
			}
			
		    // Get the response code. 1 is success, 2 is decline, 3 is error
		    $response_code = (int) $this->input->post('x_response_code');
		 
		    // Get the reason code. 8 is expired card.
		    $reason_code = (int) $this->input->post('x_response_reason_code');
		 
		    if ($response_code == 1)
		    {
		        // Approved!
				$this->logger->log_message(array('type' => 'silentpost', 'message' => 'transaction successful. - authorization code - '.$this->input->post('x_auth_code').' - transaction id - '.$this->input->post('x_trans_id')));
				
				//now lets set the active flag for the account to true.
				$data = array( 'subscription_id' 	=> $subscription_id,
								'is_suspended'		=> 0,
								'suspended_date'	=> 0
				);
				
				$this->organization_model->toggle_active_status($data);
				
		        // Some useful fields might include:
		        // $authorization_code = $this->input->post('x_auth_code');
		        // $avs_verify_result  = $this->input->post('x_avs_code');
		        // $transaction_id     = $this->input->post('x_trans_id');
		        // $customer_id        = $this->input->post('x_cust_id');
		    }
		    else if ($response_code == 2)
		    {
		        // Declined
				$this->logger->log_message(array('type' => 'silentpost', 'message' => 'credit card declined for payment'));
				
				//now lets set the active flag for the account to true.
				$data = array( 'subscription_id' 	=> $subscription_id,
								'is_suspended'		=> 1,
								'suspended_date'	=> time()
				);
				
				$this->organization_model->toggle_active_status($data);
		    }
		    else if ($response_code == 3 && $reason_code == 8)
		    {
		        // An expired card
				$this->logger->log_message(array('type' => 'silentpost', 'message' => 'credit card is expired'));
				
				//now lets set the active flag for the account to true.
				$data = array( 'subscription_id' 	=> $subscription_id,
								'is_suspended'		=> 1,
								'suspended_date'	=> time()
				);
		    }
		    else 
		    {
		        // Other error
		        $this->logger->log_message(array('type' => 'silentpost', 'message' => 'some error that cannot be determined'));
		        
				//now lets set the active flag for the account to true.
				$data = array( 'subscription_id' 	=> $subscription_id,
								'is_suspended'		=> 1,
								'suspended_date'	=> time()
				);
		    }
		}
		else
		{
			$this->logger->log_message(array('type' => 'silentpost', 'message' => 'silent post url hit, but not did not include a subscription'));
		}

	}

}
?>