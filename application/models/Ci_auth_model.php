<?php
class Ci_auth_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    function create_session ($session_id=null,$user_id=null,$remote_addr=null,$os_string=null,$computer_name=null) {
		$timestamp=time();
	    $data = array (
	    	'session_id'	=> $session_id,
		'user_id'	=> $user_id,
		'ip_address'	=> $remote_addr,
		'user_agent'	=> $os_string,
		'last_activity'	=> $timestamp,
		'iuser_data'	=> $computer_name
		);
	    $this->db->insert('ci_auth_sessions', $data);
	    $insert_id = $this->db->insert_id();
	    return $insert_id;

    }
    function delete_session($user_id=null) {
	    $data = array (
	    	'user_id'	=> $user_id
		);
	    $delete_id=$this->db->delete('ci_auth_sessions', $data);
	    return $delete_id;
    }
    function get_session_by_user($user_id=null) {
	    $data = array (
	    	'user_id'	=> $user_id
		);
	    $session=$this->db->get_where('ci_auth_sessions', $data);
	    return $session->row();
    }
}
?>
