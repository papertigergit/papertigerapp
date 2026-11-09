<?php
class Dashboard extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url','time_in_words'));
		$this->load->model(array('Item_model', 'Database_model', 'Organization_model', 'Dashboard_model','Messeges_model','Affiliates_model'));
		global $user_data;
		$this->user_data = $this->session->userdata('user_data');
	}
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;
      		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);

		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id']);

		if($is_network->num_rows() == 0):
			$databases = $this->Database_model->get_from_organization();
			$logs_databases=$this->Dashboard_model->get_databases_logs();
			$logs_locations=$this->Dashboard_model->get_locations_logs();
			$logs_categories=$this->Dashboard_model->get_categories_logs();
			$logs_items=$this->Dashboard_model->get_items_logs();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin->admin_user != $this->user_data['id']) ):
			$databases = $this->Database_model->get_from_network_organization();
			$logs_databases=$this->Dashboard_model->get_database_network_logs();
			$logs_locations=$this->Dashboard_model->get_location_network_logs();
			$logs_categories=$this->Dashboard_model->get_category_network_logs();
			$logs_items=$this->Dashboard_model->get_item_network_logs();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin->admin_user == $this->user_data['id']) ):
			$databases = $this->Database_model->get_from_organization();
			$logs_databases=$this->Dashboard_model->get_databases_logs();
			$logs_locations=$this->Dashboard_model->get_locations_logs();
			$logs_categories=$this->Dashboard_model->get_categories_logs();
			$logs_items=$this->Dashboard_model->get_items_logs();
		endif;


      		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$packages = $this->Package_model->get_by_id($package_id);
		$this_package=(array($packages->result()));
		$show_messages=null;
		$messages=$this->Messeges_model->get_all();
		if($messages->num_rows() > 0):
		if ($this_package[0][0]->active_msg !=0 ) 
		{
			$msg=$this->Messeges_model->get_by_id($this_package[0][0]->active_msg);
		if($msg):
			$show_messages = $this->Settings_model->get_message_status($msg->id);
		endif;
		}
		endif;


		$data = array(
							'user_data' => $user_data, 
							'body_id'=>'browse',
							'error_msg'=>$this->session->flashdata('error_message'),
							'users'	=> $this->User_model->get_user_by_organization($this->user_data['organization_id']),	
							'logs_databases' => $logs_databases, 
							'logs_locations' => $logs_locations,
							'logs_categories' => $logs_categories,
							'logs_items' => $logs_items, 
							'show_messages' => $show_messages,
							'databases' => $databases,
							'organization_admin' => $organization_admin,
							'is_network' => $is_network,
	      						'packages' => $this->Package_model->get_by_id($package_id),
							'organization' => $this->Organization_model->get_organization($this->user_data['organization_id'])->row()->name,
							'is_dashboard' => true
		);
    // setup methods for automatically allowing users with paid affiliates
    $org_id = $this->user_data['organization_id'];
    $expert_id = $this->Organization_model->get_org_expert($org_id); 
    $has_expert = $this->Organization_model->get_has_expert($org_id);
    $user_expert = $this->Organization_model->assign_expert($org_id);
      if ($expert_id == NULL){ 
        $data_2 = array(
        'expert_id' => $user_expert,
        'has_expert' => '1'
      );
    $user_organization = $org_id;
		$this->Organization_model->set_approved_for_org($data_2, $user_organization);
    }
		
/* 		$qq = $this->db->Organization_model->get_all_experts(); */
		$this->load->view('header', $data);
		$this->load->view('dashboard', $data);
		$this->load->view('footer');
	}
	
	function hide_message() {
		$this->authentication->require_authentication();
		global $user_data;

      		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$packages = $this->Package_model->get_by_id($package_id);
		$this_package=(array($packages->result()));
		$messages=$this->Messeges_model->get_all();
		if($messages->num_rows() > 0):
		if ($this_package[0][0]->active_msg !=0 ) 
		{
			$msg=$this->Messeges_model->get_by_id($this_package[0][0]->active_msg);
		if($msg):
			$hide_msg=$this->Settings_model->hide_message($msg->id);
		endif;
		}
		endif;


	}
	function remove_expert(){
		$this->load->view('remove_expert');
	}
	function remove_confirm(){
		$expert_code = $this->Organization_model->get_org_by_id($this->user_data['organization_id']);
		$expert_id = $expert_code->row()->expert_id;
		$data = array(
			'expert_id' => '0',
			'has_expert' => '0'
		);
    $user_organization = $this->user_data['id'];
		$first_name = $this->user_data['first_name'];
$last_name = $this->user_data['last_name'];
		$user_email = $this->user_data['email_address'];
		$subject = "Paper Tiger Online Expert Request Withdrawn"; 
		$message = ( $first_name . " " . $last_name . "has withdrawn request for your services." . $first_name ." "  . " has been removed from your approved list"); 
    $old_expert_email = $this->Organization_model->get_this_org_expert_email($expert_id);
		$this->Organization_model->remove_org_expert($data, $this->user_data['organization_id']);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
	
		$this->email->from('leads@thepapertiger.com', 'Paper Tiger Online Leads');
		$this->email->to($old_expert_email);
		$this->email->subject($subject);		
		$this->email->message($message);
		$this->email->send();
		
		redirect('dashboard');
	}

	function change_expert(){
		$query = $this->Organization_model->get_all_experts();
		$data = array('organizers' => $query);
		$this->load->view('change_expert', $data);
	}
	function choose_expert(){
	$query = $this->Organization_model->get_all_experts();
	foreach ($query->result() as $row){		
	} 
	
	$data = array('organizers' => $query);
 $this->load->view('header', $data);
	$this->load->view('choose_expert', $data);
  $this->load->view('footer');
	}
}
?>
