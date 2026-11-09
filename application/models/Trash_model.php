<?php
class Trash_model extends MY_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    	function get_location_permissions_by_user($user_id=null) {
	    $this->db->select('location_id');
	    $this->db->where('pto_umask >',0);
	    $this->db->where('user_id',$user_id);
	    $query=$this->db->get('location_permissions');
	    return $query;
	}
    function get_from_network_database(){
	    global $user_data;
		$user_locations=$this->get_location_permissions_by_user($user_data['id']);
		$user_locs = array();
		foreach($user_locations->result() as $loc) {
			$user_locs[] = $loc->location_id;
		}
		if($user_locations->num_rows() > 0) {
			$this->ci31_where_in('trash.previous_location_id',$user_locs);
		} else {
			$this->db->where('trash.previous_location_id',null);
		}
	    return $this->db->get_where('trash', array('database_id' => $this->session->userdata('database_id')));
    }
    
    function get_from_database(){
	    return $this->db->get_where('trash', array('database_id' => $this->session->userdata('database_id')));
    }
    
    function get_by_ids($ids) {
    	$this->ci31_where_in('id', $ids);
    	return $this->db->get('trash');
    }
    
    function delete($ids){
    	$this->ci31_where_in('id', $ids)->delete('trash');
    }
    
    function empty_trash(){
    	$this->db->trans_start();
    	$items = $this->get_from_database()->result_array();
    	$ids = array();
    	foreach($items as $item) {
    		$ids[] = $item['id'];
    	}
    	$this->delete($ids);
    	$this->db->trans_complete();
    }
    
    function restore($id, $loc_id){
    	$this->db->trans_start();
    	
    	$query = $this->db->get_where('trash', array('id' => $id));
    	$item = $query->row();
	
	$organizations=$this->Organization_model->get_organization_by_db($item->database_id)->row();
	$organization_id=$organizations->organization_id;
    	
    	$data = array(
    		'date_added'				=>	$item->date_added,
    		'name'							=>	$item->name,
    		'keywords'					=>	$item->keywords,
    		'category_id'				=>	$item->category_id,
    		'location_id'				=>	$loc_id,
    		'transfer_status_id'=>	'NULL',
    		'description'				=>	$item->description,
    		'action_date'				=>	$item->action_date,
    		'slot_num'				=>	$this->Location_model->get_first_open_slot($loc_id),
		'organization_id'			=>	$organization_id	
    	);
    	
    	$this->db->insert('items', $data); 
	$this->Location_model->restore_from_trash($loc_id);
    	
  		$this->db->delete('trash', array('id' => $id));   	
    	$this->db->trans_complete();
    }
}
?>
