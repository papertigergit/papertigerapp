<?php
class Assign_model extends MY_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    
    function get_all(){
	    $this->db->where('object_type',1);
	    return $this->db->get('assigned_databases');
    }
    function get_by_id($assigned_by){
	    $this->db->where('assigned_by',$assigned_by);
	    return $this->db->get('assigned_databases');
    }

    function insert($assigned_by,$assigned_to,$object_id,$object_type){
    	$data = array(
    		'assigned_by'=>$assigned_by,
    		'assigned_to'=>$assigned_to,
    		'object_id' => $object_id,
		'object_type' => $object_type,
		'ts'	=> time()
    	);
    	$this->db->insert('assigned_databases', $data);
	return $this->db->insert_id();
    }

    function get_by_email($email_address){
	return $this->db->query("select * from organizations where id IN (select organization_id from users where email_address like '%".$email_address."%');");
    }

    function get_by_org_id($org_id){
	    $this->db->where('id',$org_id);
	    return $this->db->get('organizations')->row()->id;
    }
    function assign_database($assigned_by,$from_id,$to_id,$database_id){
	    $this->db->where('id',$database_id);
	    $this->db->where('organization_id',$from_id);
	    $db_to_move_id=$this->db->get('databases')->row()->id;

	    $data = array(
	    		'organization_id' => $to_id
	    		);
	    $this->db->where('id',$db_to_move_id);
	    $updated_dbs=$this->db->update('databases',$data);
	    //log here
	    $this->insert($assigned_by,$to_id,$database_id,1);
	    return $updated_dbs;
    }

    function get_locations($database_id){
	    $this->db->where('database_id',$database_id);
	    $query=$this->db->get('locations');
	    return $query;
    }
    function assign_items($assigned_by,$organization_id,$location_id){
	    $data = array(
			'organization_id'=>$organization_id
	    		);
	    $this->db->where('location_id',$location_id);
	    //log here
	    $item_id=$this->db->update('items',$data);
	    $this->insert($assigned_by,$organization_id,$item_id,2);
	    return $item_id;
    }
}
?>
