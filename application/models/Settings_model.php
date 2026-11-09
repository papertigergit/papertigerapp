<?php

class Settings_model extends MY_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	function get_message_status($msg_num)
	{
		$this->db->where('user_id',$this->user_data['id']);
		$this->db->where('organization_id',$this->user_data['organization_id']);
		$this->db->where('hide_msg',$msg_num);
		$query=$this->db->get('user_settings');
		return $query;
	}
	function get_all(){
		global $user_data;

		$this->db->select('*');
		$this->db->from('user_settings');
		$this->db->where('user_settings.user_id', $user_data['id']);
		$this->db->where('user_settings.database_id', $this->session->userdata('database_id'));
		$this->db->where('user_settings.organization_id', $user_data['organization_id']);
		$query = $this->db->get();
		return $query;
	}
	function insert($confirmations_value, $session_reminder, $reminder_duration,$remind_me,$show_notes,$digital_search) {
		global $user_data;

	        $time_now=time();

		$data = array(
			'user_id' 			=> $user_data['id'],
			'database_id'			=> $this->session->userdata('database_id'),
			'organization_id' 		=> $user_data['organization_id'],
			'confirmations_value' 		=> $confirmations_value,
			'session_reminder'		=> $session_reminder,
			'session_time'			=> $time_now,
			'reminder_duration'		=> $reminder_duration,
			'reminders_enabled'		=> $remind_me,
			'show_notes'			=> $show_notes,
			'digital_search'		=> $digital_search
    			);
    
    
   		 $this->db->trans_start();
   		 $this->db->insert('user_settings', $data);
   		 $this->db->trans_complete();
  	}
	
	function update_confirmations($confirmations_value) {
		global $user_data;
		$data = array(
			'user_id' 			=> $user_data['id'],
			'database_id'			=> $this->session->userdata('database_id'),
			'organization_id' 		=> $user_data['organization_id'],
			'confirmations_value' 		=> $confirmations_value
    			);
		
		$this->db->where('user_id', $user_data['id']);
		$this->db->where('database_id', $this->session->userdata('database_id'));
		$this->db->where('organization_id', $user_data['organization_id']);
		$this->db->update('user_settings', $data);
	}
	function update_notes($show_notes) {
		global $user_data;
		$data = array(
			'user_id' 			=> $user_data['id'],
			'database_id'			=> $this->session->userdata('database_id'),
			'organization_id' 		=> $user_data['organization_id'],
			'show_notes' 			=> $show_notes
    			);
		
		$this->db->where('user_id', $user_data['id']);
		$this->db->where('database_id', $this->session->userdata('database_id'));
		$this->db->where('organization_id', $user_data['organization_id']);
		$this->db->update('user_settings', $data);
	}
	function update_digital($digital_search) {
		global $user_data;
		$data = array(
			'digital_search' 			=> $digital_search
    			);
		
		$this->db->where('user_id', $user_data['id']);
		$this->db->where('database_id', $this->session->userdata('database_id'));
		$this->db->where('organization_id', $user_data['organization_id']);
		$this->db->update('user_settings', $data);
	}
	function update_session($session_reminder) {
		global $user_data;

	        $time_now=time();

		$data = array(
			'user_id' 			=> $user_data['id'],
			'database_id'			=> $this->session->userdata('database_id'),
			'organization_id' 		=> $user_data['organization_id'],
			'session_reminder'		=> $session_reminder,
			'session_time'			=> $time_now
    			);
		
		$this->db->where('user_id', $user_data['id']);
		$this->db->where('database_id', $this->session->userdata('database_id'));
		$this->db->where('organization_id', $user_data['organization_id']);
		$this->db->update('user_settings', $data);
	}

	function hide_message($msg) {

		$entry_exists=$this->db->get_where('user_settings', array('user_id' => $this->user_data['id']));
		if($entry_exists->num_rows()==0):
			global $user_data;

			$time_now=time();

			$data = array(
				'user_id' 			=> $user_data['id'],
				'database_id'			=> 0,
				'organization_id' 		=> $user_data['organization_id'],
				'confirmations_value' 		=> 1, 
				'session_reminder'		=> 1,
				'session_time'			=> $time_now,
				'reminder_duration'		=> 604800,
				'reminders_enabled'		=> 1,
				'show_notes'			=> 0,
				'hide_msg'			=> $msg
				);
			 $query=$this->db->insert('user_settings', $data);
		endif;	 
		if($entry_exists->num_rows() > 0):
			global $user_data;
			$data = array('hide_msg' => $msg);
			$this->db->where('user_id',$user_data['id']);
			$this->db->where('organization_id',$user_data['organization_id']);
			$query=$this->db->update('user_settings',$data);
		endif;	
		return $query;

	}

	function update_duration($reminder_duration) {
		global $user_data;
		$data = array(
			'user_id' 			=> $user_data['id'],
			'database_id'			=> $this->session->userdata('database_id'),
			'organization_id' 		=> $user_data['organization_id'],
			'reminder_duration'		=> $reminder_duration
    			);
		
		$this->db->where('user_id', $user_data['id']);
		$this->db->where('database_id', $this->session->userdata('database_id'));
		$this->db->where('organization_id', $user_data['organization_id']);
		$this->db->update('user_settings', $data);
	}
	function update_reminders($remind_me) {
		global $user_data;
		$data = array(
			'user_id' 			=> $user_data['id'],
			'database_id'			=> $this->session->userdata('database_id'),
			'organization_id' 		=> $user_data['organization_id'],
			'reminders_enabled'		=> $remind_me
    			);
		
		$this->db->where('user_id', $user_data['id']);
		$this->db->where('database_id', $this->session->userdata('database_id'));
		$this->db->where('organization_id', $user_data['organization_id']);
		$this->db->update('user_settings', $data);
	}
	function update_timezone($timezone,$db_id) {
		$data = array(
			'timezone' 			=> $timezone
    			);
		$this->db->where('id', $db_id);
		$this->db->update('databases', $data);
	}
  
}
