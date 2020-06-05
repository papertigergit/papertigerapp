<?php
class Permissions_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
			//indexes: id, user_id, database_id
    }
    function get_by_database($database_id=null,$user_id=null) {
	    $this->db->where('database_id',$database_id);
	    $this->db->where('user_id',$user_id);
	    $query=$this->db->get('database_permissions')->row();
	    return $query;
    }
    function get_organization_admin($organization_id=null) {
	    $this->db->where('id',$organization_id);
	    return $this->db->get('organizations')->row();
    }
    function get_by_location($location_id=null,$user_id=null) {
	    $this->db->where('location_id',$location_id);
	    $this->db->where('user_id',$user_id);
	    $query=$this->db->get('location_permissions')->row();
	    return $query;
    }
    function get_database_permissions_by_user($user_id=null) {
	    $this->db->where('user_id',$user_id);
	    $query=$this->db->get('database_permissions');
	    return $query;
    }
    function get_database_permissions_by_organization($organization_id=null) {
	    $this->db->where('organization_id',$organization_id);
	    $query=$this->db->get('database_permissions');
	    return $query;
    }
    function get_location_permissions_by_user($user_id=null) {
	    $this->db->where('user_id',$user_id);
	    $query=$this->db->get('location_permissions');
	    return $query;
    }
    function get_location_permissions_by_organization($organization_id=null) {
	    $this->db->where('organization_id',$organization_id);
	    $query=$this->db->get('location_permissions');
	    return $query;
    }

    function set_database_permissions($organization_id=null,$user_id=null,$database_id=null,$read_value=null,$write_value=null) {
	$base_number=2;
	$read=0;
	$write=0;
	if($read_value === 1) {
		$read=pow($base_number,1);
	}
	if($write_value === 1) {
		$write=pow($base_number,0);
	}
	$pto_umask=($write+$read);
	$data = array(
		'organization_id' => $organization_id,
		'user_id' => $user_id,
		'database_id' => $database_id,
		'pto_umask' => $pto_umask 
		);
	$insert_id=$this->db->insert('database_permissions',$data);
	return $insert_id;
    }

    function update_database_permissions($organization_id=null,$user_id=null,$database_id=null,$read_value=null,$write_value=null) {
	$base_number=2;
	$read=0;
	$write=0;
	if($read_value == 1) {
		$read=pow($base_number,1);
	}
	if($write_value == 1) {
		$write=pow($base_number,0);
	}
	$pto_umask=($write+$read);
	$data = array(
		'pto_umask' => $pto_umask 
		);
	$this->db->where('organization_id', $organization_id);
	$this->db->where('user_id', $user_id);
	$this->db->where('database_id', $database_id);
	$database_perms=$this->db->get('database_permissions',$data);
	if($database_perms->num_rows() == 0) {
		$insert_id=$this->set_database_permissions($organization_id,$user_id,$database_id,$read_value,$write_value);
		return $insert_id;
	} else { 
		$this->db->where('organization_id', $organization_id);
		$this->db->where('user_id', $user_id);
		$this->db->where('database_id', $database_id);
		$update_id=$this->db->update('database_permissions',$data);
		return $update_id;
	}
    }

    function set_location_permissions($organization_id=null,$user_id=null,$location_id=null,$read_value=null,$write_value=null) {
	$base_number=2;
	$read=0;
	$write=0;
	if($read_value === 1) {
		$read=pow($base_number,1);
	}
	if($write_value === 1) {
		$write=pow($base_number,0);
	}
	$pto_umask=($write+$read);
	$data = array(
		'organization_id' => $organization_id,
		'user_id' => $user_id,
		'location_id' => $location_id,
		'pto_umask' => $pto_umask 
		);
	$insert_id=$this->db->insert('location_permissions',$data);
	return $insert_id;
    }

    function update_location_permissions($organization_id=null,$user_id=null,$location_id=null,$read_value=null,$write_value=null) {
	$base_number=2;
	$read=0;
	$write=0;
	if($read_value === 1) {
		$read=pow($base_number,1);
	}
	if($write_value === 1) {
		$write=pow($base_number,0);
	}
	$pto_umask=($write+$read);
	$data = array(
		'pto_umask' => $pto_umask 
		);
	$this->db->where('organization_id',$organization_id);
	$this->db->where('user_id',$user_id);
	$this->db->where('location_id',$location_id);
	$location_perms=$this->db->get('location_permissions',$data);
	if($location_perms->num_rows() == 0) {
		$insert_id=$this->set_location_permissions($organization_id,$user_id,$location_id,$read_value,$write_value);
		return $insert_id;
	} if($location_perms->num_rows() > 0){ 
		$this->db->where('organization_id',$organization_id);
		$this->db->where('user_id',$user_id);
		$this->db->where('location_id',$location_id);
		$update_id=$this->db->update('location_permissions',$data);
		return $update_id;
	}
    }

}
?>
