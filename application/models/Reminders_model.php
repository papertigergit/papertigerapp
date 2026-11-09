<?php

class Reminders_model extends MY_Model{

    	function get_location_permissions_by_user($user_id=null) {
	    $this->db->select('location_id');
	    $this->db->where('pto_umask >',0);
	    $this->db->where('user_id',$user_id);
	    $query=$this->db->get('location_permissions');
	    return $query;
	}



	function get_network_all($reminder_days){
		global $user_data;
		$datestring = "%m/%d/%Y";
		$time=time();
		//$today = strtotime(mdate($datestring, $time));
	

		$user_locations=$this->get_location_permissions_by_user($user_data['id']);
		$user_locs = array();
		foreach($user_locations->result() as $loc) {
			$user_locs[] = $loc->location_id;
		}
		if($user_locations->num_rows() > 0) {
			$this->ci31_where_in('locations.id',$user_locs);
		} else {
			$this->db->where('locations.id',null);
		}
		$this->db->select('*');
		$this->db->from('locations');
		$this->db->join('items', 'items.location_id = locations.id');
		$this->db->where('locations.database_id', $this->session->userdata('database_id'));
		$this->db->where('items.action_date <=', $time+$reminder_days);
		$query = $this->db->get();
		
		return $query;
	}
	
	function get_all($reminder_days){
		$datestring = "%m/%d/%Y";
		$time=time();
		//$today = strtotime(mdate($datestring, $time));
	
		$this->db->select('*');
		$this->db->from('locations');
		$this->db->join('items', 'items.location_id = locations.id');
		$this->db->where('locations.database_id', $this->session->userdata('database_id'));
		$this->db->where('items.action_date <=', $time+$reminder_days);
		$query = $this->db->get();
		
		return $query;
	}
	
	function mark_completed($item_id){
		
		$data = array('action_date' => null);
		
		$this->db->where('id', $item_id);
		$this->db->update('items', $data);
	}
	
	function get_item($item_id){

		$this->db->select('*');
		$this->db->from('locations');
		$this->db->join('items', 'items.location_id = locations.id');
		$this->db->where('locations.database_id', $this->session->userdata('database_id'));
		$this->db->where('items.id', $item_id);
		
		$query = $this->db->get();
		
		return $query->num_rows();
	}
	
	function update($data, $items){
		$this->ci31_where_in('id',$items);
		$this->db->update('items', $data);
	}
	
	function clear($data, $items){
		$this->ci31_where_in('id',$items);
		$this->db->update('items', $data);
	}

}
