<?php
class Api_calls_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    
	function log_incoming($username,$password,$client_hash,$client_version,$computer_name,$os_string,$action,$remote_addr) {	// defaults before a required parameter never applied (deprecated in PHP 8)
			$time_stamp=time();
			$data = array(
					'username'	=> $username,
					'password'	=> $password,
					'client_hash'	=> $client_hash,
					'client_version'=> $client_version,
					'computer_name'	=> $computer_name,
					'os_string'	=> $os_string,
					'action'	=> $action,
					'remote_addr'	=> $remote_addr,
					'time_stamp'	=> $time_stamp
					);

		    $this->db->insert('incoming_api_sessions', $data);
		    $insert_id = $this->db->insert_id();
		    return $insert_id;
	  }

	  function get_users($username=null,$password=null) {
		    $this->db->where('username',$username);
		    $this->db->where('password',$password);
		    $this->db->where('active >',0);
		    $query=$this->db->get('users');
		    return $query;
	  }
	  function get_or_register_device($user_id=null,$device_hash=null,$client_version=null,$os_string=null) {
		    $time_stamp = time();
		    $this->db->where('user_id',$user_id);	
		    $this->db->where('device_hash',$device_hash);
		    $devices=$this->db->get('authorized_devices');
		    if($devices->num_rows() == 0) {
			    $data = array(
			    		'user_id' => $user_id,
					'device_hash' => $device_hash,
					'date_created'	=> $time_stamp,
					'date_last_access' => $time_stamp,
					'client_version' => $client_version,
					'os_string' => $os_string,
					'status' => 1
					);
			    $this->db->insert('authorized_devices',$data);
			    $insert_id = $this->db->insert_id();
			    if($insert_id) {
				    //registered OK
				    return 1;
			    } else {
				    //not registered
				    return -1;
			    }
		    }
		    if($devices->num_rows() > 0) {
			    //check if device is banned
			    if($devices->result_array()->status == 0) {
				    //device is banned
				    return 0;
			    } else {
				    //device is active
				    return 1;
			    }
		    }
	  }
	  function create_session($user_id,$remote_addr,$os_string,$device_hash,$input_data=null){	// defaults before a required parameter never applied (deprecated in PHP 8)
		  //destroy all sessions first before creating new sessions
		  $destroy_id=$this->destroy_session($user_id,$device_hash);
		  $time_stamp = time();
		  $session_id=sha1($input_data.$this->config->item('encryption_key'));
		  $data = array (
		  		'session_id' => $session_id,
				'user_id'    => $user_id,
				'device_hash' => $device_hash,
				'ip_address' => $remote_addr,
				'user_agent' => $os_string,
				'last_activity' => $time_stamp,
				'user_data'	=> $input_data
				);
		  $this->db->insert('ci_auth_sessions',$data);
	    	  $insert_id = $this->db->insert_id();
		  if($insert_id) {
			  return $session_id;
		  } else {
			  return null;
		  }
	  }
	  function destroy_session($user_id=null,$device_hash=null) {
		  $time_stamp = time();
		  $this->db->where('user_id',$user_id);
		  $this->db->where('device_hash',$device_hash);
		  $destroy_id=$this->db->delete('ci_auth_sessions');
		  return $destroy_id;
	  }
	  function unlink_device($user_id=null,$device_hash=null) {
		  $tables = array('ci_auth_sessions', 'authorized_devices');
		  $this->db->where('user_id',$user_id);
		  $this->db->where('device_hash',$device_hash);
		  $unlink_id=$this->db->delete($tables);
		  return $unlink_id;
	  }
	  function session_check($username=null,$session_id=null) {
		$this->db->where('username', $username);
		$user_details=$this->db->get('users');    
		if($user_details->num_rows() == 0) {
			return null;
		} else {
			$user_id=$user_details->row()->id;
			$this->db->where('user_id',$user_id);
			$this->db->where('session_id',$session_id);
			$session_data=$this->db->get('ci_auth_sessions');
			if($session_data->num_rows() == 0) {
				return null;
			} else {
				return $session_data;
			}
		}
	  }

}
?>
