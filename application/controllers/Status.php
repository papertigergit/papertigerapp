<?php
class Status extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->helper(array('url_helper'));
		//pages for which app buttons don't need to be shown. this should be the first segment of the uri (/labels/, /imports/, etc.)
		$allowed_users = array(
			"1",
			"3",
			"6",
			"38",
			"105",
			"204"
			);
					
		if (!(in_array($this->user_data['id'], $allowed_users))) {
			
			redirect('/');
			return;
		}			

		$this->load->model(array('Database_model', 'User_model', 'Organization_model'));
		$this->load->model('package_model');
	}

	function index() {
		$this->authentication->require_authentication();
		
		$organization_package_id = $this->organization_model->get_package_id($this->user_data['organization_id']);
		$this->package = $this->package_model->get_package( $organization_package_id );
		

		$query = $this->User_model->get_organization_owners();

		$results = $query->result_array();
		
		$quantity = array();		
		
		foreach ($results as $result) {
			$number_of_databases = $this->Database_model->count_dbs_from_organization_by_id($result['id']);
			$id = $result['id'];
			$quantity[$id] = $number_of_databases;
		}
	                $package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);

		$data = array(
			'package' => $this->package,
			'owners' => $this->User_model->get_organization_owners(),		
			'package_id' => $this->Organization_model->get_package_id($this->user_data['organization_id']),
			'packages' =>	$this->Package_model->get_by_id($package_id),
			'user_data' => $this->user_data,
			'user_count' => $this->User_model->get_user_count(),
			'org_count' => $this->Organization_model->get_org_count(),
			'packages' => $this->Package_model->group_by_id(),
			'organization_admin' => $this->Permissions_model->get_organization_admin($this->user_data['organization_id']),
			'package_id' => $this->Organization_model->get_package_id($this->user_data['organization_id']),
			'is_network' => $this->Network_model->is_network($package_id)
		);

		$this->load->view('header', $data);
		$this->load->view('status', $data);
		$this->load->view('footer');


	}
}
?>
