<?php

class Forgot_password extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->helper('form');
		
		$this->load->model('user_model');
	}
	
	function index() {
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email_address', 'email address', 'required|valid_email|callback_user_exists');
		$this->form_validation->set_rules('protector', 'validation', 'required|callback_check_protector');	
		
		if( $this->input->post('forgot_password') !== NULL) {
				
			if( $this->form_validation->run() == FALSE ) {
	
				$this->load->view('forgot_password/index');
			
			} else {
				
				$user = $this->user_model->get_user( $this->input->post('email_address'), 'email_address');
				
				$activation_code = md5('forgotpass'.time().'salt?');
				
				$data = array(	'activation_code'	=> $activation_code
				);
							
				$this->user_model->set_activation_code($user->row()->id, $activation_code);
				
				/* start email */
				$this->load->library('postmark');
				$this->postmark->to($this->input->post('email_address'));
				$this->postmark->subject('Paper Tiger Forgotten Password');
				$this->postmark->message_html($this->load->view('email_templates/forgot_password', $data, true));
				$this->postmark->send();			
				/* end email */
				
				$this->load->view('forgot_password/sent');

			}
		} else {
			$this->load->view('forgot_password/index');
		}		
	}
		
	function reset($email=NULL) {
		$this->load->library('form_validation');

		$authorization_code = $this->uri->segment('3');

		if(!$this->uri->segment('3'))
		{
			redirect('/');
		}
		else
		{
		
		$user = $this->user_model->get_user( $authorization_code, 'activation_code');
		
		if($user->num_rows() == 0)
		{
			redirect('/');
		}
		
		$data = array(	'authorization_code'	=> $authorization_code
		);

		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('forgot_password/reset', $data);
		} else {
			$this->user_model->activate($user->row()->id, sha1($this->input->post('password').$this->config->item('encryption_key')));
			$this->session->set_flashdata('success', 'Password was successfully reset');
			redirect('login');
		}
		}
		
		
	}
	
	function user_exists() {
		//does a user exist with this email?
		$user = $this->user_model->get_user( $this->input->post('email_address'), 'email_address');
			
		if( $user->num_rows() == 0 OR $user->row()->active == 0) 
		{
			$this->form_validation->set_message('user_exists', 'The %s could not be located in the system.');
			return FALSE;
		} 
		else 
		{
			return TRUE;
		}
			
	}
	
	function check_protector($str) {
		if($str == '9' || $str == 'nine') {
			return TRUE;
		} else {
			$this->form_validation->set_message('check_protector', 'The validation entry was not correct.');
			return FALSE;
		}
	}
	
}
