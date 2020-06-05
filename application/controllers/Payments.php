<?php
class Payments extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url'));
		$this->load->model(array('Item_model', 'Database_model', 'Organization_model', 'Dashboard_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;
      		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$data = array(
							'user_data' => $user_data, 
							'error_msg'=>$this->session->flashdata('error_message'),
							'databases' => $this->Database_model->get_from_organization(),
							'logs_databases' => $this->Dashboard_model->get_databases_logs(),
							'is_network' => $this->Network_model->is_network($package_id),
							'logs_locations' => $this->Dashboard_model->get_locations_logs(),
							'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
	      						'packages' => $this->Package_model->get_by_id($package_id),
							'logs_categories' => $this->Dashboard_model->get_categories_logs(),
							'logs_items' => $this->Dashboard_model->get_items_logs(),
							'organization' => $this->Organization_model->get_organization($user_data['organization_id'])->row()->name,
							'payment_logs' => $this->Logging_model->get_signup_logs(),
							'is_dashboard' => true
							
		);

		$this->load->view('header', $data);
		$this->load->view('payments', $data);
		$this->load->view('footer');
	}
	function search() {
		$this->authentication->require_authentication();
		global $user_data;

		$stop_date='';
		$start_date='';
		if($this->input->post('date_payments')){
			$date_range=explode("-",$this->input->post('date_payments'));
			if(sizeof($date_range)==1){
				$date_range[1]=$date_range[0];
			}
			$start_date=(explode("/",$date_range[0]));
			$start_date[2]=intval($start_date[2]);
			$start_date=mktime(0,0,0,$start_date[0],$start_date[1],$start_date[2]);

			$stop_date=(explode("/",$date_range[1]));
			$stop_date=mktime(23,59,59,$stop_date[0],$stop_date[1],$stop_date[2]);

		}


      		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$data = array(
							'user_data' => $user_data, 
							'error_msg'=>$this->session->flashdata('error_message'),
							'databases' => $this->Database_model->get_from_organization(),
							'logs_databases' => $this->Dashboard_model->get_databases_logs(),
							'logs_locations' => $this->Dashboard_model->get_locations_logs(),
	      						'packages' => $this->Package_model->get_by_id($package_id),
							'is_network' => $this->network_model->is_network($package_id),
							'organization_admin' => $this->permissions_model->get_organization_admin($user_data['organization_id']),
							'logs_categories' => $this->Dashboard_model->get_categories_logs(),
							'logs_items' => $this->Dashboard_model->get_items_logs(),
							'organization' => $this->Organization_model->get_organization($user_data['organization_id'])->row()->name,
							'payment_logs' => $this->Logging_model->get_signup_logs($start_date,$stop_date),
							'start_date'	=> $start_date,
							'stop_date'	=> $stop_date,
							'is_dashboard' => true
							
		);

		$this->load->view('header', $data);
		$this->load->view('payments', $data);
		$this->load->view('footer');
	}
}
?>
