<?php
class  Manage extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model(array('Storage_model'));
		$this->load->helper(array('url','form'));
		$this->load->library('curl');
	}
	    function index() {
		//Manage Storage tokens
		$this->authentication->require_authentication();
		$storage_auth=$this->Storage_model->get_by_storage_auth_code($this->user_data['storage_id']);
		$data = array (
			'user_data'	=> $this->user_data,
			'storage_auth' 	=> $storage_auth 
			);
		$this->load->view('account/manage',$data);
	    }
	    function add() {
		$this->authentication->require_authentication();
		$submit=$this->input->post('submit');
		if($submit){

			$oauth_consumer_key=$this->config->item('dropbox_consumer_key'); 
			$api_version=$this->config->item('dropbox_api_version');

			$dropbox_email=$this->input->post('dropbox_email');
			$dropbox_password=$this->input->post('dropbox_password');
			if($dropbox_email && $dropbox_password) {
				    $url = 'https://api.dropbox.com/'.$api_version.'/token';
				    $dropbox_parameters = array (
							'email' => $dropbox_email,
							'oauth_consumer_key' => $oauth_consumer_key,
							'password' => $dropbox_password
						);
				    $result = $this->curl->simple_post($url,$dropbox_parameters); 
				    if($result) {
					    $result_decoded=json_decode($result);
					    $dropbox_token=$result_decoded->token;
					    $dropbox_secret=$result_decoded->secret;
					    
					    $user_id=$this->user_data['id'];
					    $insert_token=$this->Storage_model->insert_key($user_id,$dropbox_email,$dropbox_token,$dropbox_secret);
					    print_r($insert_token);

				    } else {
					    return null;
				    }
			} else {
				$data = array (
					'user_data'	=> $this->user_data
					);
				$this->load->view('account/manage/add',$data);
			}
		} else {
			$data = array (
				'user_data'	=> $this->user_data
				);
			$this->load->view('account/manage/add',$data);
		}
		
	    }
	    function delete() {
		$this->authentication->require_authentication();
		//revoke storage auth access
		$auth_id=$this->uri->segment('4');
		if($auth_id){
			$storage_auth=$this->Storage_model->get_by_storage_auth_code($this->user_data['storage_id']);
			$revoke_key=$this->Storage_model->delete_key($auth_id,$storage_auth->row()->username);
			print_r($revoke_key);
			
		}
	    }
}
?>
