<?php
class Dashboard_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }

    function get_databases_logs(){
	    $user_data = $this->session->userdata('user_data');
	    $this->db->order_by("id", "desc"); 
	    $this->db->limit(15);
	    $db_log_ids=$this->db->get_where('logs_databases', array('organization_id' => $user_data['organization_id']))->result_array();
	   return $db_log_ids;
	}
    function get_database_perms($user_id,$organization_id)
    {
	    $this->db->where('user_id',$user_id);
	    $this->db->where('organization_id',$organization_id);
	    $this->db->where('pto_umask >',0);
	    $query=$this->db->get('database_permissions');
	    return $query;
    }
    function get_location_perms($user_id,$organization_id)
    {
	    $this->db->where('user_id',$user_id);
	    $this->db->where('organization_id',$organization_id);
	    $this->db->where('pto_umask >',0);
	    $query=$this->db->get('location_permissions');
	    return $query;
    }
    function get_item_perms($item_id,$organizationid)
    {
    }
    function get_database_network_logs(){
	    $user_data = $this->session->userdata('user_data');
	    $this->db->where('organization_id',$user_data['organization_id']);
	    $this->db->order_by("id", "desc"); 
	    $this->db->limit(15);
	    $db_ids=$this->get_database_perms($user_data['id'],$user_data['organization_id']);
	    $this_db_ids=array();
	    if($db_ids):
		    if($db_ids->num_rows() > 0):
	    foreach($db_ids->result() as $key) {
		    $this_db_ids[]=$key->database_id;
	    }
		    $this->db->where_in('database_id',$this_db_ids);
		    endif;
	    endif;
	    if(!$db_ids):
		    $this->db->where_in('database_id',null);
	    endif;
	    $query=$this->db->get('logs_databases');
	    if($query):
		    return  $query->result_array();
	    endif;    
	    if(!$query):
	    	    return null;
	    endif;    
    }
    function get_locations_logs(){
	    $user_data = $this->session->userdata('user_data');
	    $this->db->order_by("id", "desc"); 
	    $this->db->limit(15);
    	    $loc_log_ids=$this->db->get_where('logs_locations', array('organization_id' => $user_data['organization_id']))->result_array();
	    return $loc_log_ids;
	}
    function get_location_network_logs(){
	    $user_data = $this->session->userdata('user_data');
	    $this->db->where('organization_id',$user_data['organization_id']);
	    $this->db->order_by("id", "desc"); 
	    $this->db->limit(15);
	    $loc_ids=$this->get_location_perms($user_data['id'],$user_data['organization_id']);
	    $this_loc_ids=array();
	    if($loc_ids):
		    if($loc_ids->num_rows() > 0):
			    foreach($loc_ids->result() as $key) {
				    $this_loc_ids[]=$key->location_id;
			    }
		    $this->db->where_in('location_id',$this_loc_ids);
		    endif;
	    endif;
	    if($loc_ids->num_rows() == 0):
		    $this->db->where('location_id is null');
	    endif;
    	    $query=$this->db->get('logs_locations');
	    if($query):
		    return  $query->result_array();
	    endif;    
	    if(!$query):
	    	    return null;
	    endif;    
    }
    //remove logic from controller and put here
    //use this for logs
    function get_category_perms(){
		$query=$this->db->query("select * from categories where owner='".$this->user_data['id']."' or id in (select category_id from items where location_id in (select locations.id from `locations`,`location_permissions` where locations.id=location_permissions.location_id and locations.database_id='".$this->session->userdata('database_id')."' and location_permissions.pto_umask > 0 and location_permissions.user_id='".$this->user_data['id']."' order by locations.name asc)) or owner='".$this->user_data['id']."' order by categories.name asc");
	    return $query;
    }
    function get_categories_logs(){
	    $user_data = $this->session->userdata('user_data');
	    $this->db->order_by("id", "desc"); 
	    $this->db->limit(15);
    	    $cat_log_ids=$this->db->get_where('logs_categories', array('organization_id' => $user_data['organization_id']))->result_array();
	    return $cat_log_ids;
	}
    function get_category_network_logs(){
	    $user_data = $this->session->userdata('user_data');
	    $this->db->where('organization_id',$user_data['organization_id']);
	    $this->db->order_by("id", "desc"); 
	    $this->db->limit(15);
	    $cat_ids=$this->get_category_perms();
	    $this_cat_ids=array();
	    if($cat_ids):
		    if($cat_ids->num_rows() > 0):
		    foreach($cat_ids->result() as $key) {
			    $this_cat_ids[] = $key->id;
		    }
		    $this->db->where_in('category_id',$this_cat_ids);	    
		    endif;	    
	    endif;	    
	    if(!$cat_ids):
		    if($cat_ids->num_rows() == 0):
		    $this->db->where('category_id',null);	    
		    endif;    
	    endif;    
    	    $query=$this->db->get('logs_categories');
	    if($query):
		    return  $query->result_array();
	    endif;    
	    if(!$query):
	    	    return null;
	    endif;    
	}
    function get_item_network_logs(){
	    $user_data = $this->session->userdata('user_data');
	    $this->db->where('organization_id',$user_data['organization_id']);
	    $this->db->order_by("id", "desc"); 
	    $loc_ids=$this->get_location_perms($user_data['id'],$user_data['organization_id']);
	    $this_loc_ids=array();
	    if($loc_ids):
		    if($loc_ids->num_rows() > 0):
	    foreach($loc_ids->result() as $key) {
		    $this_loc_ids[]=$key->location_id;
	    }
		    $this->db->where_in('location_id',$this_loc_ids);
		    endif;
	    endif;
	    if(!$loc_ids):
		    $this->db->where_in('location_id',null);
	    endif;
	    $this->db->limit(15);
	    $query=$this->db->get_where('logs_items');
	    if($query):
		    return  $query->result_array();
	    endif;    
	    if(!$query):
	    	    return null;
	    endif;    
	}
    function get_items_logs(){
	    $user_data = $this->session->userdata('user_data');
	    $this->db->order_by("id", "desc"); 
	    $this->db->limit(15);
	    $items_log_ids=$this->db->get_where('logs_items', array('organization_id' => $user_data['organization_id']))->result_array();
	    return $items_log_ids;
	}
}
?>
