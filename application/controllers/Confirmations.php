<?php
class Confirmations extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'empty_messages'));
		$this->load->model(array('Location_model', 'Transfers_model', 'Category_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;


		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
	
		if($is_network->num_rows() > 0):
			$db_perms=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id']);
			$database_permissions=null;
			if($db_perms):
				$database_permissions=$db_perms->pto_umask;
			endif;	
			
				if($organization_admin == $this->user_data['id']):
					$database_permissions=3;
				endif;	
		endif;	
		if($is_network->num_rows() == 0):
			$database_permissions=3;
		endif;	

		if($database_permissions == 0) {
			$this->session->set_flashdata('error', 'You do not have access to this database.');
			redirect(site_url());
		} else {
		

      		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);
		if($is_network->num_rows() == 0):
			$table_data = $this->Transfers_model->get_from_database();
		endif;
		if($is_network->num_rows() > 0):
			$table_data = $this->Transfers_model->get_from_network_database();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$table_data = $this->Transfers_model->get_from_database();
		endif;




			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			$is_network = $this->Network_model->is_network($package_id);
			if($is_network->num_rows() == 0):
				$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
			endif;
			if($is_network->num_rows() > 0):
				$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
			endif;
			if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
				$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
			endif;

            $database_id=$this->session->userdata('database_id');

            $network_utils = array();
            $network_utils = enable_buttons();

			$data = array(
				'user_data' => $user_data,
				'table_data'=>$table_data,
                                'body_id'=>'browse',
                                'buttons_on' => $network_utils['buttons_on'],
                                'is_network' => $is_network,
                                'admin_email' => $network_utils['admin_email'],
                                'admin_name' => $network_utils['admin_name'],
                                'locations'=>$locations
			);
			//full page
			$this->load->view('header', $data);
            $this->load->view('confirmations_grid', $data);
            $this->load->view(empty_message_view($database_id), $data); //This loads the corresponding empty views!
			$this->load->view('footer');
		}
	}
	function cancel_single(){
		$this->authentication->require_authentication();
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
			$this->Transfers_model->cancel($arr);
			$this->session->set_flashdata('success', 'The transfer was successfully cancelled.');
			redirect('/confirmations/');
	}
	
	function cancel(){
		$this->authentication->require_authentication();
		$num_segs=$this->uri->total_segments();
		if($num_segs > 2){
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
			if($num_segs > 2) {
				for($i=3; $i <=$num_segs; $i++) {
					$items[$i-3] = $num_segs[$i];
				}
				$data = array(
					'segs' => $arr
				);
			}
			$this->load->view('confirmations/cancel',$data);
		}
		else {
			$this->load->view('confirmations/cancel_failed');
		}
	}
	function accept_single(){
		$this->authentication->require_authentication();
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		foreach($arr as $item) {
			$item_exists=$this->Transfers_model->get_by_id($item);
			if(!$item_exists):
				$this->session->set_flashdata('error', 'The transfer you have attempted to complete has already been confirmed by another user.');
				redirect('/confirmations/');
			endif;	
		}
			$this->session->set_flashdata('success', 'The transfer was successfully accepted.');
			$this->Logging_model->accept($arr);
			$this->Transfers_model->accept($arr);
			redirect('/confirmations/');
	}
	
	function accept(){
		$this->authentication->require_authentication();
		$num_segs=$this->uri->total_segments();
		if($num_segs > 2){
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
				for($i=3; $i <=$num_segs; $i++) {
					$items[$i-3] = $num_segs[$i];
				}
				$data = array(
					'segs' => $arr
				);
			$this->load->view('confirmations/accept',$data);
		}
		else {
			$this->load->view('confirmations/accept_failed');
		}
	}
	function do_accept(){
		$arr=$this->input->post('arr');
		foreach($arr as $item) {
			$item_exists=$this->Transfers_model->get_by_id($item);
			if(!$item_exists):
				$this->session->set_flashdata('error', 'The transfers you have attempted to complete has already been confirmed by another user.');
				redirect('/confirmations/');
			endif;	
		}
		$this->authentication->require_authentication();
		$this->Logging_model->accept($arr);
		$this->Transfers_model->accept($arr);
                $this->session->set_flashdata('success',"The transfers were successfully accepted.");    
                echo '{ "result": "successful_redirect", "url": "'.site_url().'confirmations/"}';
	}
	function do_cancel(){
		$arr=$this->input->post('arr');
		$this->authentication->require_authentication();
		$this->Transfers_model->cancel($arr);
                $this->session->set_flashdata('success',"The transfers were cancelled successfully.");    
                echo '{ "result": "successful_redirect", "url": "'.site_url().'confirmations/"}';
	}
}
?>
