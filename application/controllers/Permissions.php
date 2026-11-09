<?php
class Permissions extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url'));
		$this->load->model(array('Item_model', 'Database_model', 'Organization_model', 'Dashboard_model','Network_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	
	function index() {
		$this->authentication->require_authentication();
		$organization_admin=$this->Permissions_model->get_organization_admin($this->user_data['organization_id']);

		if($organization_admin->admin_user != $this->user_data['id']) {
			$this->session->set_flashdata('error', 'Only organization administrators can access this page.');
			redirect(site_url());
		} else {

		if($this->input->post('submit') != NULL) {
			global $user_data;
			$databases = $this->Database_model->get_from_organization();
			foreach($databases->result() as $dbs) {
				$users	= $this->User_model->get_user_by_organization($user_data['organization_id']);	
				foreach($users->result() as $user) {
					$db_read="db_r_".$user->id."_".$dbs->id;
					$db_write="db_w_".$user->id."_".$dbs->id;
					if($this->input->post($db_read) == ''){
						$db_read=0;
					}
					if($this->input->post($db_read) == "yes"){
						$db_read=1;
					}
					if($this->input->post($db_write) == ''){
						$db_write=0;
					}
					if($this->input->post($db_write) == "yes"){
						$db_write=1;
					}
						$update_id=$this->Permissions_model->update_database_permissions($user_data['organization_id'],$user->id,$dbs->id,$db_read,$db_write);
				}
				$locations = $this->Location_model->get_by_database($dbs->id);
				foreach($locations->result() as $locs) {
					$users	= $this->User_model->get_user_by_organization($user_data['organization_id']);	
					foreach($users->result() as $user) {
						$loc_read="loc_r_".$user->id."_".$locs->id;
						$loc_write="loc_w_".$user->id."_".$locs->id;
							if($this->input->post($loc_read) == ''){
								$loc_read=0;
							}
							if($this->input->post($loc_read) == "yes"){
								$loc_read=1;
							}
							if($this->input->post($loc_write) == ''){
								$loc_write=0;
							}
							if($this->input->post($loc_write) == "yes"){
								$loc_write=1;
							}
						$update_id=$this->Permissions_model->update_location_permissions($user_data['organization_id'],$user->id,$locs->id,$loc_read,$loc_write);
					}
				}
			}
			$this->session->set_flashdata('success', 'User permissions updated successfully.');
			redirect('permissions');
			
		} else {
		global $user_data;
      		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_users=$this->User_model->get_user_by_organization($user_data['organization_id']); //Getting not active and active users
		$data = array(
							'user_data' => $user_data, 
							'databases' => $this->Database_model->get_from_organization(),
	      						'packages' => $this->Package_model->get_by_id($package_id),
							'organization' => $this->Organization_model->get_organization($user_data['organization_id'])->row()->name,
							'is_network' => $is_network,
						        'is_network' => $this->Network_model->is_network($package_id),                        
							'organization_admin' => $this->Permissions_model->get_organization_admin($this->user_data['organization_id']),
							'organization_users' => $organization_users,
							'users'	=> $this->Organization_model->get_users_name_asc($user_data['organization_id'])	
		);
		$this->load->view('header', $data);
		$this->load->view('permissions', $data);
		$this->load->view('footer');
	       }
	     }  
	}
}
?>
