<?php 

class Cancel extends CI_Controller {

	function __construct() {
		parent::__construct();
		include APPPATH.'helpers/mcapiclass_helper.php';  
		
		$this->load->library(array('billingsubscription', 'logger'));
		$this->user_data = $this->session->userdata('user_data');
	}
	
	function index() {
	
		$this->authentication->require_authentication('admin');
		
		$data = array(
						'user_data' => $this->user_data,
						'body_id'	=> 'account'
		);
		
		//ajax page
		$this->load->view('account/cancel/index', $data);
	
	}
	
	function confirm() {
	
		$this->user_data = $this->session->userdata('user_data');

		$this->authentication->require_authentication('admin');
		
		if( !$this->input->post('cancel')) {
			
			return;
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('cancel_account_password','Password', 'required');
		if(($this->user_data['sha1_password']) != sha1($this->input->post('cancel_account_password').$this->config->item('encryption_key')))
		{
			echo '{ "result": "validation_error", "errors": {"cancel_account_password":"<p>Please provide a valid password to cancel your account.</p>"}}';
			return;
		}
	
		$organization_id = $this->user_data['organization_id'];
		
		$this->logger->log_message(array('type' => 'cancel', 'message' => 'Organization '.$organization_id.' canceled'));
		
		$this->authentication->require_authentication();
		
		$this->load->model('organization_model', 'organization');
		
            $data = array(
                'user_firstname'          => $this->user_data['first_name'],
                'user_lastname'          => $this->user_data['last_name'],
                'user_email'          => $this->user_data['email_address'],
                'org_id'          => $this->user_data['organization_id']
            );        
	//mail chimp integration
		$api_key='';
		$listId = "";
		$api = new MCAPI($api_key);

		$my_email=$this->user_data['email_address'];
		$retval = $api->listUnsubscribe( $listId,$my_email,false,false,false);

		$api = new MCAPI($api_key);
		$fname=$this->user_data['first_name'];
		$lname=$this->user_data['last_name'];
		$organization_id = $this->user_data['organization_id'];
		$company=$this->Organization_model->get_org_by_id($organization_id);
		$company=$company->result_array();
		$company=$company[0]['name'];
			$groupings = array (
					'Contact Type' => array (
					'name' => 'Contact Type',
					'groups' => 'Paper Tiger Online Cancelled',
						)
					);
		$time_now=time();
		$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'COMPA'=>$company, 'DPRCH'=>$time_now);
		$merge_vars['GROUPINGS'] = $groupings;
		$retval = $api->listSubscribe( $listId, $my_email, $merge_vars,'html', 'false', 'false' );
		
    		// start email 
    		$this->load->library('postmark');
    		$this->postmark->to('sales@thepapertiger.com');
    		$this->postmark->subject('Yearly subscription canceled.');
    		$this->postmark->message_html($this->load->view('email_templates/yearly_cancel', $data, true));
    		$this->postmark->send();			
    		// end email 
		
	    if($this->organization_data->subscription_id):	
            $this->billingsubscription->cancel_subscription( array('subscription_id' => $this->organization_data->subscription_id));
        endif;
		
		$this->organization->set_canceled($this->user_data['organization_id']);
				
		$users = $this->organization->get_users($this->user_data['organization_id']);
				
		echo '{ "result": "successful_redirect", "url": "'.site_url().'account/cancel/complete"}';
	}
	
	function complete() {
	
		$this->authentication->require_authentication('admin');
		
		$this->session->sess_destroy();
		$this->load->view('account/cancel/confirm');
	}
}

?>
