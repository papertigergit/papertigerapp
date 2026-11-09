<?php 
class Choose_Expert extends MY_Controller{
function __construct() {

		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url','time_in_words'));
		$this->load->model(array('Item_model', 'Database_model', 'Organization_model', 'Dashboard_model','Messeges_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	
	function index() {
    // load pagination class
$this->load->library('pagination');

$config['base_url'] = base_url().'choose_expert/';
$config['total_rows'] = 200;
$config['per_page'] = 20; 

$this->pagination->initialize($config); 
    // 
		$this->authentication->require_authentication();
		global $user_data;
      		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);

		$organization_admin = $this->Permissions_model->get_organization_admin($user_data['organization_id']);

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
							'organization' => $this->Organization_model->get_organization($user_data['organization_id'])->row()->name,
							'is_dashboard' => true
		);
		
/* 		$qq = $this->db->Organization_model->get_all_experts(); */
		$this->load->view('header',$data);
		$this->load->view('choose_expert', $data);
		$this->load->view('footer');
	}
}
?>
