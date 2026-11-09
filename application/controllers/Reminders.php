<?php

class Reminders extends MY_Controller{

	function __construct(){
		parent::__construct();
		
		global $user_data;
		$this->load->library(array('session', 'authentication'));
        $this->load->helper('url');
		$this->load->helper('empty_messages');
		$this->load->database();
		$this->load->model('Reminders_model');
		$this->load->model('Database_model');
		$this->load->model('Category_model');
		$user_data = $this->session->userdata('user_data');
	}
	
	
	/**
	Sends all current reminders for the selected database for actions to be performed upon.
	**/
	function index(){
	
		$this->authentication->require_authentication();
		global $user_data;


		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
	
		if($is_network->num_rows() == 0):
			$database_permissions=3;
		endif;	
		if($is_network->num_rows() > 0):
			$database_permissions_handler=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$user_data['id']);
			if($database_permissions_handler) {
				$database_permissions=$database_permissions_handler->pto_umask;
			}
		endif;	
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$database_permissions=3;
		endif;	

		if($database_permissions == 0) {
			$this->session->set_flashdata('error', 'You do not have access to this database.');
			redirect(site_url());
		} else {
	
			$reminder_status=$this->Settings_model->get_all();
			if($reminder_status->num_rows() == 0)
			{
				$reminder_days=86400;
			}
			if($reminder_status->num_rows() > 0)
			{		
			foreach($reminder_status->result() as $row)
				{   
				    $reminder_days=$row->reminder_duration;
				}   
            }

            $network_utils = array();
            $network_utils = enable_buttons();

            $database_id=$this->session->userdata('database_id');
                                    $data['admin_email']= $network_utils['admin_email'];
                                    $data['admin_name'] = $network_utils['admin_name'];
            
				    $data['buttons_on'] = $network_utils['buttons_on'];
				    $data['is_network'] = $network_utils['is_network'];

				    $data['user_data'] = $user_data;
				    $data['organization_admin'] = $organization_admin;


			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			$is_network = $this->Network_model->is_network($package_id);
			if($is_network->num_rows() == 0):
				$data['locations'] = $this->Location_model->get_from_database($this->session->userdata('database_id'));
				$data['num_reminders'] = $this->Reminders_model->get_all($reminder_days)->num_rows();
				$data['items'] = $this->Reminders_model->get_all($reminder_days);
				$data['table_data'] = $this->Reminders_model->get_all($reminder_days);
			endif;
			if($is_network->num_rows() > 0):
				$data['locations'] = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
				$data['num_reminders'] = $this->Reminders_model->get_network_all($reminder_days)->num_rows();
				$data['items'] = $this->Reminders_model->get_network_all($reminder_days);
				$data['table_data'] = $this->Reminders_model->get_network_all($reminder_days);
			endif;
			if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id']) ):
				$data['locations'] = $this->Location_model->get_from_database($this->session->userdata('database_id'));
				$data['num_reminders'] = $this->Reminders_model->get_all($reminder_days)->num_rows();
				$data['items'] = $this->Reminders_model->get_all($reminder_days);
				$data['table_data'] = $this->Reminders_model->get_all($reminder_days);
			endif;




			$data['body_id'] = 'browse';
			$this->load->view('header', $data);
			$this->load->view('reminders/index', $data);
            $this->load->view(empty_message_view($database_id), $data); //This loads the corresponding empty views!
			$this->load->view('footer', $data);
		}
	}
	
	/**
	Marks the item as completed. We have to make sure that the item is in their current database.
	**/
	function mark_completed(){
		
		$item_id = $this->uri->segment('3');
		if($this->_is_in_database($item_id) === true){
			$this->Reminders_model->mark_completed($item_id);
			$this->session->set_flashdata('success', "Action date for item has been cleared. Good job!");
			redirect('/reminders/');
		}
		else{
			$this->session->set_flashdata('error', "Oops. There was an error.");
			redirect('/reminders/');
		}	
	}
	
	/**
	Checks to see which items are being updated based on the checked items on the reminders page. If no items are selected,
	it throws an error.
	**/
	function update(){
		if($this->authentication->is_authenticated())
		{
		$data['items'] = '';
		$seg = $this->uri->segment_array();
		$num_seg = $this->uri->total_segments();
		if($num_seg > 2){
			for($i=3; $i <= $num_seg; $i++){
				$data['items'] .= '/'.$seg[$i];
			}
			
			$this->load->view('reminders/edit', $data);
		}
		else{
			$this->load->view('reminders/edit_failed');
		}
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
	
	function clear(){
			$items = array('');
			$data = array(
				'action_date' => null
			);
		
			$segs = $this->uri->segment_array();
			$num_seg = $this->uri->total_segments();
		if($this->input->post('clear_reminders') != NULL)
		{
			if($num_seg > 2){
				for($i=3; $i<=$num_seg; $i++){
					$items[$i-3] = $segs[$i];
				}
				$this->Reminders_model->clear($data, $items);
				$this->session->set_flashdata('success', 'Action date for items have been cleared. Good job!');
				redirect('/reminders/');
			}
				else{
				$this->session->set_flashdata('error',"Oops. There was an error clearing the items.");
				redirect('/reminders/');
			}
		}
		else
		{	
			$data = array(
			'segs' => $segs
		);
			$this->load->view('reminders/clear',$data);
		}
	}
	function do_clear(){
			$items = array('');
			$data = array(
			      'action_date' => null
			    );
			$items=$this->input->post('items');
		      $this->Reminders_model->clear($data, $items);
			
			$this->session->set_flashdata('success',"The items were cleared successfully.");		      
			echo '{ "result": "successful_redirect", "url": "'.site_url().'reminders/"}';
	}
	
	/**
	Updates the action date for one or more items.
	**/
	function edit(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('action_date','Action Date', 'required');
		
		if($this->form_validation->run() == FALSE) {
				echo '{ "result": "validation_error", "errors": {"name":"Please select an action date for the new item(s)"}}';
				return;
		}
		else{
			$this->load->helper('date');
			$datestring = "%m/%d/%Y";
			$items = array('');
			$data = array(
				'action_date' => strtotime($this->input->post('action_date'))
			);
			
			$segs = $this->uri->segment_array();
			$num_seg = $this->uri->total_segments();
			if($num_seg > 2){
				for($i=3; $i<=$num_seg; $i++){
					$items[$i-3] = $segs[$i];
				}
				$this->Reminders_model->update($data, $items);
				$this->session->set_flashdata('success', 'The new action date was successfully set.');
				echo '{ "result": "successful_redirect", "url": "'.site_url().'reminders/"}';
			}
			else{
				$this->session->set_flashdata('error',"Oops. There was an error updating the items.");
			}
		}
	}
	
	/**Function to ensure that the requested item ID is part of the currently selected database**/
	function _is_in_database($item_id){
		if($this->Reminders_model->get_item($item_id) == 1){
			return true;
		}
		else{
			return false;
		}
	}

}
