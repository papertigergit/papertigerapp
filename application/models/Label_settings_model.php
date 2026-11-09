<?php

class Label_settings_model extends MY_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	function get_all($label_id){
		global $user_data;

		$this->db->select('*');
		$this->db->from('label_settings');
		$this->db->where('label_settings.label_id', $label_id);
		$query = $this->db->get();
		return $query;
	}
}
