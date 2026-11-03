<?php

class Reset_email extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->helper('form');
		
		$this->load->model(array('user_model','organization_model'));
	}
	
	function reset() {
		$this->load->library('form_validation');

		if(!$this->uri->segment('4'))
		{
			redirect('login/logout');
		}
		else
		{
		
			$authorization_code = $this->uri->segment('4');

			$user = $this->user_model->get_user( $authorization_code, 'activation_code');
			
			if($user->num_rows() == 0)
			{
				redirect('login/logout');
			}
			else
			{
				/*
				echo "<pre>";
				print_r($authorization_code);
				echo "<br>";
				print_r($user->row()->sha1_password);
				echo "<br>";
				print_r($user->row());
				echo "</pre>";
				*/
				$data = array(	'authorization_code'	=> $authorization_code
				);

				$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
				
				if ($this->form_validation->run() == FALSE) {
					$this->load->view('reset_email', $data);
				} else  {
						if($user->row()->sha1_password === sha1($this->input->post('password').$this->config->item('encryption_key')))
						{	
							$organization_data = $this->organization_model->get_organization($user->row()->organization_id);
							// (float): keep PHP 7's numeric comparison - '' and IDs starting with a letter skip Braintree
							if((float) $organization_data->row()->subscription_id != 0){

								// let's load up Braintreezle
								require_once('./vendor/braintree/braintree_php/lib/Braintree.php');
								$this->config->load('braintree');
								$gateway = new Braintree\Gateway([
                						    'environment' => $this->config->item('environment'),
                						    'merchantId' => $this->config->item('merchantId'),
                						    'publicKey' => $this->config->item('publicKey'),
                						    'privateKey' => $this->config->item('privateKey')
            							    ]);
	
								$updateResult = $gateway->customer()->update(
								$user->row()->organization_id,
									array(
									'email' => $user->row()->new_email_address,
									)
								);
								// if braintree email is successfully changed then change email in database
								if($updateResult->success){
									$this->user_model->change_email_address($user->row()->id, null);
								}
							}else{

								$this->user_model->change_email_address($user->row()->id, null);

							}			
							
							//$this->session->set_flashdata('success', 'Your email address was successfully changed');
							//return "Nyagau";
							

							redirect('login/logout');
						}
						else {
							$activation_code=null;
							$this->user_model->set_activation_code($user->row()->id,$activation_code);
							//$this->session->set_flashdata('error', 'Wrong password, login and try again');
							redirect('login/logout');
						}
					}
			}
		}
		
		
	}
	
}
