<?php
require_once(APPPATH.'libraries/Google/Auth/OAuth2.php');
require_once(APPPATH.'../vendor/autoload.php');
class Organization extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->model(array('organization_model','google_users_model'));
		$this->load->library('authentication', 'session');
		$this->user_data = $this->session->userdata('user_data');
		$this->load->helper(array('url'));
		include APPPATH.'helpers/mcapiclass_helper.php';  
			
	}
	
	function update() {
	
	
		if($this->input->post('rename_organization') !==NULL ) {
			if($this->user_data['organization_name'] != $this->input->post('organization_name'))
			{
				if($this->input->post('organization_name') != '')
				{
					$this->organization_model->update($this->user_data['organization_id'], array('name' => $this->input->post('organization_name')), 'force ssl option enabled');
					$text="Organization name successfully updated to '".$this->input->post('organization_name')."'.";
					$this->session->set_flashdata('success', $text);
					redirect('account');
				}
			}
			if($this->input->post('organization_name') == '')
			{
					$this->session->set_flashdata('error', 'Organization name must be provided.');
					redirect('account');
			}
			if($this->input->post('organization_name') == $this->user_data['organization_name'])
			{
					redirect('account');
			}
		}
		

	}
	function revoke_google_docs() {
		if($this->authentication->is_authenticated())
		{
			$revoke=$this->google_users_model->revoke_token($this->user_data['id']);
			if($revoke) {
				$this->session->set_flashdata('success','Your Digital Tiger has been disconnected from your Google Drive. <br>Your Google Drive files are safe.');
				redirect('account');
			}

		} else {
			redirect('login/logout');
		}        
	   }
	function google_docs() {
       
		if($this->authentication->is_authenticated())
		{   
            
            $client_id = '553842701572-trhaovtktvh60nvk9jkc7iem9t39i41g.apps.googleusercontent.com';
            $client_secret = 'kWfCHAih7DWwYU19JJr9H5eh';
            $redirect_uri = 'https://app.thepapertiger.com/account/organization/google_docs';
            $client = new Google_Client();
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $client->addScope("https://www.googleapis.com/auth/drive.readonly");
            $client->setAccessType('offline');
                  
            parse_str(array_pop(explode('?',$_SERVER['REQUEST_URI'],3)),$_GET);
            if (isset($_GET['code'])) { 
                  $secret_token=$this->google_users_model->get_token($this->user_data['id']);
			      $client->authenticate($_GET['code']);
                  $secret = $_GET['code'];
                  $token = $client->getAccessToken();
                  $jsontoken = json_decode($token, true);
                  $cleantoken = $jsontoken['access_token'];
                  $cleanrefresh = $jsontoken['refresh_token'];
                  $client->setAccessToken($token);
                  $create_token=$this->google_users_model->create_token($this->user_data['id'],$cleantoken);
                  $keep_secret=$this->google_users_model->create_secret_token($this->user_data['id'],$cleantoken,$cleanrefresh);
                
            $google_users=$this->google_users_model->get_token($this->user_data['id']);
            $google_users_data=$google_users->result_array();
            if ($google_users_data[0]['oauth_token'] != NULL && $google_users_data[0]['oauth_token_secret'] == NULL){
                $this->session->set_flashdata('error', 'Please <a href="https://security.google.com/settings/security/permissions">Click Here to Revoke Digital Tiger Access To Your Google Drive</a><br> Then Reconnect.');
                $revoke=$this->google_users_model->revoke_token($this->user_data['id']);
                redirect('account');
            } 
            if($keep_secret){
			           $this->session->set_flashdata('success', "Google Drive Account Linked Successfully.");
            }
			redirect('account');
}
else
{
    //For Guest user, get google login url
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
}
			
		} else {
			redirect('login/logout');
		}        
	   }

	function newsletter() {
	
		$this->load->model('organization_model');
		if($this->input->post('update_newsletter') !==NULL ) {
				$update_value=0;
				$api_key='1ddf218cb55d3d51f6be533b72fc81ae-us1';
				$listId = "a31d451a83";
				$api = new MCAPI($api_key);
			if($this->input->post('newsletter') == "on" ) {
				//add user from mailchimp newsletter
				$fname=$this->user_data['first_name'];
				$lname=$this->user_data['last_name'];
				$my_email=$this->user_data['email_address'];
				$company=$this->user_data['organization_name'];
				$groupings = array (
						'Contact Type' => array (
						'name' => 'Contact Type',
					)
						);
				$time_now=time();
				$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'COMPA'=>$company, 'DPRCH'=>$time_now);
				$retval = $api->listSubscribe( $listId, $my_email, $merge_vars,'html', 'false', 'true' );

				$update_value=1;
				$text="You have been successfully subscribed to the Newsletter ";
				$this->session->set_flashdata('success', $text);
			}
		if($this->input->post('newsletter') ==NULL ) {

				$my_email=$this->user_data['email_address'];
				$retval = $api->listUnsubscribe( $listId,$my_email,false,false, false);
		}

			$this->organization_model->update($this->user_data['organization_id'], array('newsletter' => $update_value), 'Subscribe to newsletters');
			redirect('account');
		}
		

	}
	function change_admin() {
		$this->authentication->require_authentication('admin');
		$new_admin=$this->input->post('account_owner');
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id']);
		if($new_admin == $organization_admin->admin_user):
			$this->session->set_flashdata('error', 'You are already the account owner.');
			redirect('account');
		endif;	

		if($this->user_data['id'] != $organization_admin->admin_user){
			$this->session->set_flashdata('error', 'Only account administrators can change account ownership.');
			redirect('/');
		}
		$get_new_admin_info=$this->User_model->get_user($new_admin)->row();
		if($get_new_admin_info):
			if($get_new_admin_info->active != 1):
				$this->session->set_flashdata('error', 'Only activated users accounts can be account administrators.');
				redirect('account');
			endif;	
			if($get_new_admin_info->organization_id != $this->user_data['organization_id']):
				$this->session->set_flashdata('error', 'The user does not belong to this organization.');
				redirect('account');
			endif;	
		endif;	
		$admin_email=$this->User_model->get_user($organization_admin->admin_user)->row()->email_address;
		$data = array ('get_new_admin_info' => $get_new_admin_info );

		$update_id=$this->Organization_model->set_admin($this->user_data['organization_id'],$new_admin);
		if($update_id):
			$this->load->library('postmark');
			$this->postmark->to($admin_email);
			$this->postmark->subject('Change of administrators for your Paper Tiger account');
			$this->postmark->message_html($this->load->view('email_templates/change_account_owner', $data, true));
			$this->postmark->send();			

			//now grant former admin all permissions to existing databases and locations
			
			$databases=$this->Database_model->get_from_organization();
			foreach($databases->result() as $key){
				$this->Permissions_model->update_database_permissions($this->user_data['organization_id'],$organization_admin->admin_user,$key->id,1,1);
				$location_ids=$this->Location_model->get_by_database($key->id);
				foreach($location_ids->result() as $loc_key){
					$this->Permissions_model->update_location_permissions($this->user_data['organization_id'],$organization_admin->admin_user,$loc_key->id,1,1);
				}
			}
			//exit(0);
		
				$this->session->set_flashdata('success', 'Account owner changed successfully, you are no longer the account owner.');
				redirect('/');
		endif;		
		if(!$update_id):
				$this->session->set_flashdata('error', 'An error occured, account owner not changed.');
				redirect('account');
		endif;	
	}
}
?>
