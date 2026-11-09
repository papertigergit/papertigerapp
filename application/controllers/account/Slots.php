<?php
class Slots extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->user_data = $this->session->userdata('user_data');
		
		$this->load->model('package_model');
		$this->load->model('organization_model');
		
		$organization_package_id = $this->organization_model->get_package_id($this->user_data['organization_id']);
		$this->package = $this->package_model->get_package( $organization_package_id );
	}
	
	function manage() {
		$this->authentication->require_authentication('admin');
		
		$this->config->load('billing');
		$this->load->library('billingsubscription');
		$this->load->library('form_validation');
		
		if($this->package->allow_extra_users != 1)
		{
			$this->load->view('facebox_error',array('error'=>'<strong>An error occured.</strong><br/><br/>Your package does not allow adding extra users.'));
			return;
		}
		
		if($this->input->post('manage_user_slots') !== NULL) {			
			$this->form_validation->set_rules('manage_user_slots', 'Number of user slots', 'required|is_integer_no_zero');
			
			if ($this->form_validation->run() == FALSE) {
				echo '{ "result": "validation_error", "errors": {"manage_user_slots":"'.form_error('manage_user_slots').'"}}';
			} else {
/* 				Calculate new price for their subscription and let ARB know  */
			
				$new_price = $this->package->price + ($this->config->item('cost_extra_user') * ($this->input->post('num_users_input') - $this->package->max_users));
				$organization	= $this->organization_model->get_organization($this->user_data['organization_id']);
						
				$billing_data =	array(	'subscription_id'	=> $organization->row()->subscription_id,
										'amount'			=> $new_price
				);
				
				if( $this->billingsubscription->update_subscription($billing_data) ) {

					$this->organization_model->update($organization->row()->id, array('extra_user_slots' => ($this->input->post('num_users_input') - $this->package->max_users)));
				
					$action = 'changed user slots from '.$organization->row()->extra_user_slots.' to '.($this->input->post('num_users_input') - $this->package->max_users);
					$this->db->insert('logs', array('date' => time(), 'organization_id' => $this->user_data['organization_id'], 'user_id' => $this->user_data['id'], 'type' => 'slots', 'action' => $action));			
		
					$this->session->set_flashdata('user', 'User slots successfully updated.');
					echo '{ "result": "successful_input", "grid_callback": "/people/add_callback"}';
		
				} else {
					$this->load->view('facebox_error',array('error'=>'<strong>An error occured.</strong><br/><br/>There was a problem updating your user slots.'));
					return;
				}
			}

			
		}else {
			$data = array(	'organization'	=> $this->organization_model->get_organization($this->user_data['organization_id'])->row(),
							'package'		=> $this->package
			);
	
			$this->load->view('account/slots/add', $data);
		}
	}
}
?>