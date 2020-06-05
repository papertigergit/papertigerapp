<?php
class First_login extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url','time_in_words'));
		$this->load->model(array('Item_model', 'Database_model', 'Organization_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;
      		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);

		$organization_admin = $this->Permissions_model->get_organization_admin($user_data['organization_id']);

		if($is_network->num_rows() == 0):
			$databases = $this->Database_model->get_from_organization();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin->admin_user != $this->user_data['id']) ):
			$databases = $this->Database_model->get_from_network_organization();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin->admin_user == $this->user_data['id']) ):
			$databases = $this->Database_model->get_from_organization();
		endif;

		$data = array(
							'user_data' => $user_data, 
							'users'	=> $this->User_model->get_user_by_organization($this->user_data['organization_id']),	
							'organization_admin' => $organization_admin,
							'is_network' => $is_network,
                            'packages' => $this->Package_model->get_by_id($package_id),
							'organization' => $this->Organization_model->get_organization($user_data['organization_id'])->row()->name,
		);
		
		$this->load->view('/first_login/network_admin', $data);
	}
}
?>
