<?php
class Ssl extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->user_data = $this->session->userdata('user_data');
			
		$this->output->enable_profiler(1);
	}
	
	function manage() {
	
		$this->load->model('organization_model');
	
		if($this->input->post('force_ssl') == "on") {
			$this->organization_model->update($this->user_data['organization_id'], array('force_ssl' => 1), 'force ssl option enabled');
		} else {
			$this->organization_model->update($this->user_data['organization_id'], array('force_ssl' => 0), 'force ssl option disabled');
		}
		
		$this->session->set_flashdata('success', 'Secure Connection (SSL) preference successfully updated');
		redirect('account');
	}
}
?>