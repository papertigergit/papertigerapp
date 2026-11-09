<?php
class Storage_model extends MY_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    function get_by_storage_auth_code($id=null) {
	    $this->db->where('id',$id);
	    $storage_auth_code=$this->db->get('storage_auth_codes');
	    return $storage_auth_code;
    }
    function insert_key($user_id=null,$username=null,$token=null,$secret=null) {
	    $time = time();
	    $auth_codes_data = array (
	    	'username' => $username,
		'token'	=> $token,
		'secret' => $secret,
		'date_authenticated' => $time,
		'authentication_expiry'	=> null,
		'suspended' => 0,
		'storage_id' => 1
		);
	    $this->db->where('username',$username);
	    $get_auth_codes=$this->db->get('storage_auth_codes');

	$this->db->trans_start();
        if($get_auth_codes->num_rows() > 0) {
		$auth_code_id=$get_auth_codes->row()->id;
		$this->db->where('id',$auth_code_id);
		$this->db->update('storage_auth_codes',$auth_codes_data);
	} else {
		$this->db->insert('storage_auth_codes', $auth_codes_data);
		$auth_code_id = $this->db->insert_id();
	}


	$storage_data = array (
			'storage_id' => $auth_code_id
			);
	$this->db->where('id',$user_id);
	$update_id=$this->db->update('users', $storage_data);
	$this->db->trans_complete();
	if(!$return_id){
		$return_id=0;
	}
	return $update_id;
    }
    function delete_key($auth_id=null,$username=null) {
	    $this->db->where('id',$auth_id);
	    $this->db->where('username',$username);
	    $delete_id=$this->db->delete('storage_auth_codes');
	    return $delete_id;
    }
    function update_key($column='id',$value=null) {
	    $this->db->where($column,$value);;
	    $update_id=$this->db->update('storage_auth_codes');
	    return $update_id;
    }
}
?>
