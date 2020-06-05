<?php

class Login extends CI_Controller {

	function __construct() {
		parent::__construct();
		$using_ie6 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== FALSE);
		if($using_ie6 === true){
			$data['using_ie6'] = $using_ie6;
			$this->load->view('login',$data);
		}
		$this->load->library(array('session', 'authentication'));
		$this->load->helper(['url','security_helper']);
		$this->load->model(array('authentication_model','user_model'));
	}
	
	function index() {
		if( $this->authentication->is_authenticated() ) {
			redirect('/dashboard');
			return;
		}
	
		 if($this->input->post('email') !== NULL) {
			
		 	 if(isset($failed_attempts) && $failed_attempts >= 5) {
		 	 	echo '{ "result": "validation_error", "email_error_text": "", "password_error_text": "", "login_error_text": "<span class=\'alert new_alert\'>There have been too many Sign In attempts. Please wait a few minutes and try again.</span>"}';
		 	 	return;
		 	 }
		
		 	$this->load->library(array('form_validation'));
		 	$this->form_validation->set_error_delimiters("<span class='alert new_alert'>","</span>");
		 	$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|xss_clean');
		 	$this->form_validation->set_rules('password', 'Password', 'required|trim');
		 	$this->form_validation->set_message('required', 'Required field');
			
		 	if ($this->form_validation->run() == FALSE) {
		 		echo '{ "result": "validation_error", "email_error_text": "'.form_error('email').'", "password_error_text": "'.form_error('password').'", "login_error_text": ""}';
		 	} else {
		 		$auth = $this->authentication->authenticate($this->input->post('email'),sha1($this->input->post('password').$this->config->item('encryption_key')), $this->input->post('remember_me'));
				if($auth !== TRUE)
		 		{
		 			$auth = $this->authentication->authenticate($this->input->post('email'),md5($this->input->post('password')), $this->input->post('remember_me'));
		 			if($auth === TRUE) {
		 				$this_user_details=$this->User_model->get_user($this->input->post('email'),"email_address")->result_array();
		 				$id=$this->User_model->change_password($this_user_details[0]['id'],sha1($this->input->post('password').$this->config->item('encryption_key')));
		 			}
		 		}
		 		if($auth === TRUE){
		 			$this_user_details=$this->User_model->get_user($this->input->post('email'),"email_address")->result_array();
		 			$updated_user_id=$this->User_model->last_login($this_user_details[0]['id']);
		 			echo '{ "result": "successful_authentication"}';
		 		} elseif ($auth == 'trial_expired') {
		 			$this->session->set_flashdata('trial_expired', true);
		 			echo '{ "result": "trial_expired"}';
		 		} else {
		 			echo '{ "result": "validation_error", "email_error_text": "", "password_error_text": "", "login_error_text": "<span class=\'alert new_alert\'>'.$auth.'</span>"}';
		 		}
		 	}
		 } else {
			$this->load->view('login');
		 }
	}

	
	function logout(){
		$this->authentication->logout();
	}
}
?>
