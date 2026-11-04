<?php
class Organization_model extends CI_Model {

    function __construct() {
		parent::__construct();
		$this->load->database();
		//indexes: id
    }
    
    function create($name, $admin_user, $package_id) {	// the default before a required parameter never applied (deprecated in PHP 8)
    	$data = array(	'name' 			=> $name, 
    					'package_id' 	=> $package_id,
					'subscription_id'	=> "0",
    					'date_added'	=> time()			
    	);
    	
    	if($admin_user != FALSE) {
    		$data['admin_user'] = $admin_user;
    	}
    
    	$this->db->insert('organizations', $data);
    	
		return $this->db->insert_id();
    }
    
    function get_organization($value, $column='id') {
    	$this->db->where($column, $value);
    	
    	return $this->db->get('organizations');
    }
    function get_org_by_id($org_id) {
	    $this->db->where('id',$org_id);
	    return $this->db->get('organizations');
    }
    function get_organization_by_db($db_id) {
    	$this->db->where('id',$db_id);
	return $this->db->get('databases');
    }
    
    function set_admin($organization_id, $admin_user) {
    	$data = array(	
			'admin_user'	=> $admin_user
    	);
    	
    	$this->db->where('id', $organization_id);
    	$query=$this->db->update('organizations', $data);
	return $query;
    }

	function update($organization_id, $data, $action="number of user slots updated")
	{
		$this->db->where('id', $organization_id);
		$this->db->update('organizations', $data);
	}
	
	function set_subscription_id($organization_id, $user_id, $subscription_id, $action="subscription id set") {
    	$data = array(	'id'				=> $organization_id,
    					'subscription_id'	=> $subscription_id
    	);
    	
    	$this->db->where('id', $organization_id);
    	$this->db->update('organizations', $data);
    	
    	//lets try to keep some history
    	$this->db->insert('logs', array('date' => time(), 'organization_id' => $organization_id, 'user_id' => $user_id, 'type' => 'billing', 'action' => $action));
	}

    function set_package($organization_id, $user_id, $package_id, $action="package changed") {
    	$data = array(	'id'			=> $organization_id,
    					'package_id'	=> $package_id
    	);
    	
    	$this->db->where('id', $organization_id);
    	$this->db->update('organizations', $data);
    	
    	//lets try to keep some history
    	$this->db->insert('logs', array('date' => time(), 'organization_id' => $organization_id, 'user_id' => $user_id, 'type' => 'package', 'action' => $action));
    }

    function set_approved_for_org($data, $user_organization)
	{
	$user_org = $user_organization;
	$form_data = $data;
	/* $data = array('has_expert' => '0'); */
		$this->db->where('id', $user_org);
		$this->db->update('organizations', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			return TRUE;
		}
		
		return FALSE;
	}
  function assign_expert($organization_id){
   $this->db->where('organization_id', $organization_id);
   return $this->db->get('affiliate_tracking')->row()->affiliate_code ?? NULL;
 }
    function set_canceled($organization_id) {
    	$data = array(	'id'			=> $organization_id,
    					'is_canceled'	=> 1
    	);
    	
    	$this->db->where('id', $organization_id);
    	$this->db->update('organizations', $data);
    	
    	//lets try to keep some history
    	$this->db->insert('logs', array('date' => time(), 'organization_id' => $organization_id, 'action' => 'Canceled'));
    }
    
    function get_package_id($organization_id) {
    	$this->db->select('package_id');
    	$this->db->where('id', $organization_id);
    	$result=$this->db->get('organizations')->row();
	if($result) {
		return $result->package_id;
	} else {
		return null;
	}
    }
    function get_users($organization_id) {
    	$this->db->where('organization_id', $organization_id);
    	$this->db->where('active', 1);
		return $this->db->get('users');
/*
		$this->db->get('users');
		exit($this->db->last_query());
*/
    }
    
    function get_users_name_asc($organization_id) {
    	$this->db->where('organization_id', $organization_id);
    	$this->db->where('active', 1);
	$this->db->order_by("first_name", "asc"); 
		return $this->db->get('users');
    }
    function get_all_users($organization_id) {
    	$this->db->where('organization_id', $organization_id);
	return $this->db->get('users');
    }

	// get the total number of slots available for the user. includes the package users + extra slots
	function get_user_slots($organization_id) {
		$this->db->select('package_id, extra_user_slots');
		$this->db->where('id', $organization_id);
		
		$query = $this->db->get('organizations')->row();
		$extra_slots = $query->extra_user_slots;
		
		$this->db->select('max_users');
		$this->db->where('id', $query->package_id);
		$this->db->from('packages');
		
		$package_slots = $this->db->get()->row()->max_users;

		return $extra_slots + $package_slots;
	}
	
	function set_user_slots($organization_id, $amount = 0) {
		$data = array(	'extra_user_slots'	=> $amount);
	
		$this->db->where('id', $organization_id);
		
		$this->db->update('organizations', $data);
	}
	
	// lets not disable the admin user, just everyone else
	function disable_users($organization_id, $admin_id) {
		$data = array(	'active' => 0);
		$this->db->where('organization_id', $organization_id);
		$this->db->where('id !=', $admin_id);
		$this->db->update('users', $data);
	}
	function toggle_active_status($data) {
		$this->db->where('subscription_id', $data['subscription_id']);
		$this->db->update('organizations', $data);
	}
	
	function get_org_count() {
		return $this->db->count_all_results('organizations');
	}
	function get_org_admin($organization_id){
		$this->db->where('id', $organization_id);
		return $this->db->get('organizations')->row()->admin_user;
	
	}
	function get_org_expert($organization_id) {
    	$this->db->where('id', $organization_id);
    	return $this->db->get('organizations')->row()->expert_id;    
    }
    function get_this_affiliate_level($user_id) {
    	$this->db->where('id', $user_id);
    	return $this->db->get('users')->row()->affiliate_level;    
    }
    function get_this_org_expert($expert_id) {
    	$this->db->where('affiliate_code', $expert_id);
    	return $this->db->get('users');    
    }
    function get_this_org_expert_email($expert_id) {
    	$this->db->where('affiliate_code', $expert_id);
    	return $this->db->get('users')->row()->email_address;    
    } 	
    function get_this_org_expert_name($expert_id) {
    	$this->db->where('affiliate_code', $expert_id);
    	return $this->db->get('users')->row()->first_name;    
    } 	
    function get_this_org_expert_phone($expert_id) {
    	$this->db->where('affiliate_code', $expert_id);
    	return $this->db->get('users')->row()->phone;    
    } 	

    function get_this_chosen_expert_email($expert_id) {
    	$this->db->where('expert_id', $expert_id);
    	return $this->db->get('organizations')->row()->expert_id;    
    } 	
	function remove_org_expert($data, $organization_id) {
    	$this->db->where('id', $organization_id);
    	$this->db->update('organizations', $data);    
    }
    function change_org_expert($data, $organization_id) {
    	$this->db->where('id', $organization_id);
    	$this->db->update('organizations', $data);    
    }
    function get_all_experts() {
    	$this->db->where('affiliate_level', 3);
    	$this->db->order_by("last_name", "asc");
    	$q = $this->db->get('users');
    	return $q;    	    
    }
    function get_pending_by_expert($affiliate_code) {
    	$this->db->where('expert_id',$affiliate_code);
    	$this->db->where('has_expert',0);
    	$p = $this->db->get('organizations');
    	return $p;    	    
    }
    function get_approved_by_expert($affiliate_code) {
    	$this->db->where('expert_id',$affiliate_code);
    	$this->db->where('has_expert',1);
    	$p = $this->db->get('organizations');
    	return $p;    	    
    }
    function get_has_expert($organization_id){
    	$this->db->where('id', $organization_id);
    	return $this->db->get('organizations')->row()->has_expert;
    }
    function get_users_per_expert($expert_id){
    	$this->db->where('expert_id', $expert_id);
	    /* $k = $this->db->select('admin_user'); */
	    $k = $this->db->get('organizations')->row()->admin_user;
	    return $this->db->get_where('users', array('id' => $k));
/* 		$query = $k;  */
/*     	return $this->db->get_where('users', array('id' => $query)); */
    }
}
?>
