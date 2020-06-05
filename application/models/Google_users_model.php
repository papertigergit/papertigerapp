<?php

class Google_users_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    function create_token($user_id=null,$token=null) {
	    $last_login=time();
	    $data = array (
		    'user_id' => $user_id,
		    'token' => $token,
		    'last_login' => $last_login,
		    'status' => 0
	    );
	    //check if record exists, if so perform update
	    $this->db->where('user_id',$user_id);
	    $select_data=$this->db->get('google_users');
	    if($select_data->num_rows() > 0):
		   $google_id=$select_data->result_array(); 
	    	   $record_id=$google_id['0']['id'];
		   $this->db->where('id',$record_id);
		   $update_id=$this->db->update('google_users',$data);
		   return $update_id;
	    endif;    
	    if($select_data->num_rows() == 0):
		    //create new record
		    $insert_id=$this->db->insert('google_users',$data);
		    return $insert_id;
	    endif;
    }
    function digital_allowed($package_id=null){
		$this->db->where('id',$package_id);
		$package=$this->db->get('packages')->row();
		return $package;	
    }

    function get_token($user_id=null) {
	    $this->db->where('user_id',$user_id);
	    $token=$this->db->get('google_users');
	    return $token;
    }
    function create_secret_token($user_id=null,$oauth_token=null,$oauth_token_secret=null) {
	    $data = array (
	    		'oauth_token' => $oauth_token,
			    'oauth_token_secret' => $oauth_token_secret,
			    'status' => 1
			);
	    $this->db->where('user_id',$user_id);
	    $update_token=$this->db->update('google_users',$data);
	    return $update_token;
    }
    function revoke_token($user_id=null) {
	    $this->db->where('user_id',$user_id);
	    $delete=$this->db->delete('google_users');
	    return $delete;
    }
		function digital_status($user_id){
			$this->db->where('user_id', $user_id);
		  $this->db->select('status');
		  return $this->db->get('google_users');	
		}
}
?>
