<?php
class Dashboard extends CI_Controller {
    
	function __construct() {
		parent::__construct();
				
		$this->load->library('authentication', 'session');
		//get_instance()->load->helper('url_helper');

		if($this->authentication->is_authenticated()) { 

		$this->user_data = $this->session->userdata('user_data');
		
		if( $this->organization_data->subscription_id ) {
		
			require_once('./vendor/braintree/braintree_php/lib/Braintree.php');
			$this->config->load('braintree');
				
			$gateway = new Braintree\Gateway([
                            'environment' => $this->config->item('environment'),
                            'merchantId' => $this->config->item('merchantId'),
                            'publicKey' => $this->config->item('publicKey'),
                            'privateKey' => $this->config->item('privateKey')
                           ]);
			
			// Checking based off the Baintree status of canceled 
			//	is mostly just a double-check since we store this 
			//	information locally and just checked it above.
			
			$subb = $this->organization_data->subscription_id;
			$subbb = '"'.trim($subb,'"').'"';
			$subscription = $gateway->subscription()->find($this->organization_data->subscription_id);
              $this->subscription_data = $subscription;

		
/*
			if(strtolower($subscription->status) == Braintree_Subscription::CANCELED) {
				$this->session->set_flashdata('error', 'Account section may not be accessed with a canceled account.');
				redirect('/');
			}
*/
			
				
		}
	   } else {
		   redirect('/');
	   }
		
	}
	
	function index() {
		$this->authentication->require_authentication();
		
		$this->load->helper('form');
		
		$this->load->model('package_model', 'package');
		$this->load->model('organization_model', 'organization');
		$this->load->model('database_model');
		$this->load->model('location_model');
		$this->load->model('google_users_model');
		
		if($organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user != $this->user_data['id']) {
				$this->session->set_flashdata('error', 'Only administrators can access the account page.');
				redirect('/');
		}
		
		$organization_package_id = $this->organization->get_package_id($this->user_data['organization_id']);
		
		$package = $this->package->get_package($organization_package_id);
		
		$database_limit = ($package->max_databases == -1) ? "&#8734;" : $package->max_databases;
		
		$databases = $this->database_model->get_from_organization();
		
		$users = $this->organization->get_users($this->user_data['organization_id']);

      		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);

		$locations=$this->location_model->get_all_from_organization()->result_array();
		$num_filed_items=0;
		foreach($locations as $num_of_items)
		{
			$num_filed_items=$num_filed_items+$num_of_items['num_items'];
		}
		
		$data = array(
						'user_data' 			=>	$this->user_data,
						'digital_allowed'		=> $this->google_users_model->digital_allowed($package_id),
						'packages'				=>	$this->package->get_all(),
						'is_network' => $this->Network_model->is_network($package_id),
						'organization_admin' => $this->Permissions_model->get_organization_admin($this->user_data['organization_id']),
						'organization_package'	=>  $package,
						'organization'			=>	$this->organization_model->get_organization($this->user_data['organization_id'])->row(),
						'body_id'				=>	'account',
						/* 'subscription'			=>  $this->config->item('environment'), */
						'org_users'	=> $this->User_model->get_users_by_org($this->user_data['organization_id']),
						'num_databases'			=>	$databases->num_rows(),
						'num_locations'			=>	$this->location_model->get_all_from_organization()->num_rows(),
						'num_users'				=>	$this->User_model->get_user_by_organization($this->user_data['organization_id']),
						'num_filed_items'		=>	$num_filed_items,
						'google_users_data'		=> 	$this->google_users_model->get_token($this->user_data['id'])
		);
		
		// Let's get some billing info
/*
		if(($this->organization_data->subscription_id != "") && ($this->organization_data->subscription_id != "0")) {
            $payment_data = Braintree_CreditCard::find($this->subscription_data->paymentMethodToken);
            $data['cardType'] = $payment_data->cardType;
            $data['cardLastFour'] = $payment_data->last4;
		}
*/
		
		//get google credentials
/* 		$data['google_credentials']=$this->google_users_model->get_credentials($this->user_data['id']); */

		//full page
		$this->load->view('header', $data);

		$this->load->view('account/dashboard/index', $data);
	
		$this->load->view('footer');
	
	}
}
?>
