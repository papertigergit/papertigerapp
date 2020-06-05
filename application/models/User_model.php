<?php
class User_model extends CI_Model {

    function __construct() {
		parent::__construct();
		$this->load->database();
		//indexes: id, organization_id
    }
    function last_login($user_id=null){
	    $time=time();
	    $data = array('last_login' => $time
			  );
	    $this->db->where('id',$user_id);
	    $update_id=$this->db->update('users',$data);
	    $update_logins="update users set logins=logins+1 where id='".$user_id."'";	
	    $update_id=$this->db->query($update_logins);
	 return $update_id;
    }
    
    //field can be any column
    function get_user($value, $column='id') {
    
    	$this->db->where($column, $value);
    	return $this->db->get('users');    
    }
   
    function create($first_name, $last_name, $email_address, $password, $organization_id = FALSE) {
    	
		$password = ($password == "") ? sha1(time().$this->config->item('encryption_key')) : $password;
		
		$date_added = time();
		
		//if we aren't providing an organization id, it's because this is the initial admin user.
		if($organization_id === FALSE) {
			$auth_code = NULL;
			$active = 1;
		} else {
			$auth_code = md5($first_name.$last_name.$email_address.$date_added);
			$active = 0;
		}
    	
    	$data = array(			'first_name'		=>	$first_name,
    					'last_name'			=>	$last_name,
    					'email_address'		=>	$email_address,
    					'sha1_password'			=>	$password,
    					'date_added'		=>	$date_added,
    					'activation_code'	=>	$auth_code,
    					'active'			=>	$active,
    	);
    	
    	if( $organization_id != FALSE) {
    		$data['organization_id'] = $organization_id;
    	}
    
    	$this->db->insert('users', $data);
	$insert_id=$this->db->insert_id();

        $update_logins="update users set logins=logins-1 where id='".$this->db->insert_id()."'";	
	$update_id=$this->db->query($update_logins);

    	
		return $insert_id; 
    }
    
    function delete($user_id) {
    	$this->db->where('id', $user_id);
    	$this->db->delete('users');
    }
    function change_password($user_id,$new_password=NULL) {

	    $data = array();

	if($new_password !== NULL) {
		$data['sha1_password'] = $new_password;
		$data['password'] = NULL;
	}
		$this->db->where('id', $user_id);

	    	$this->db->update('users', $data);
    	
		return $this->db->insert_id();

    }
    
    function update($user_id, $first_name, $last_name, $email_address=NULL, $password=NULL) {
    	
   		$data = array(	'first_name'		=>	$first_name,
    				'last_name'		=>	$last_name,
    	);

		//only update the password if it get changed.
		if($password !== NULL) {
			$data['sha1_password']	= $password;
		}
		
		if($email_address !== NULL) {
			$data['new_email_address']	= $email_address;
		}

		$this->db->where('id', $user_id);
    	$this->db->update('users', $data);
    	
		return $this->db->insert_id();
    
    }
    
    function set_activation_code($user_id, $activation_code) {
		$this->db->where('id', $user_id);
    		$this->db->update('users', array('activation_code' => $activation_code));
    }

    function change_email_address($user_id, $activation_code) {
		$this->db->where('id', $user_id);
		$new_email_address = $this->db->get_where('users', array('id' => $user_id))->row()->new_email_address;
		$this->db->where('id', $user_id);
    		$this->db->update('users', array('activation_code' => $activation_code, 'new_email_address' => null, 'email_address' => $new_email_address));
    }
    
    function activate($user_id, $password) {
    	$data = array(	'date_confirmed'	=> time(),
    			'activation_code'	=> "",
    			'sha1_password'		=> $password,
			'active'		=> 1
    	);
    
    	$this->db->where('id', $user_id);
    	$this->db->update('users', $data);
    }
    function get_users_by_org($organization_id=null){
	    $this->db->where('organization_id',$organization_id);
	    $this->db->where('active >',0);
	    $query=$this->db->get('users');
	    return $query;
    }
    
    function set_organization($user_id, $organization_id) {
    	$data = array(	'organization_id'	=> $organization_id,
    		'id'				=> $user_id
    	);
    	
    	$this->db->where('id', $user_id);
    	$this->db->update('users', $data);
    }

    function get_user_by_organization($organization_id=null) {
	    $this->db->where('organization_id',$organization_id);
	    $organization_users=$this->db->get('users');
	    return $organization_users;
    }
    
    function get_organization_user($user_id, $organization_id) {
    	$this->db->where('id', $user_id);
    	$this->db->where('organization_id', $organization_id);
	$query=$this->db->get('users');    
	return $query;
    }
	
    function get_users_by_db_id($db_id=NULL) {
	    //$db_id=mysql_real_escape_string($db_id);
	    $db_id = $this->db->escape_str($db_id);
	    $query="select * from users where organization_id=(select organization_id from `databases` where id='".$db_id."')";
	    $res=$this->db->query($query);
	    return $res;
    }

	function get_organization_owners() {
		
		$this->db->select('first_name, last_name, organizations.date_added, email_address, organizations.name, organizations.id');
		$this->db->from('organizations');
		$this->db->join('users', 'users.id=organizations.admin_user', 'left');
		return $this->db->get();
			
	}
	
	function get_user_count() {

		return $this->db->count_all_results('users');
		
	}
  function get_user_count_by_org($organization_id) {
		$this->db->where('organization_id', $organization_id);
	  return $this->db->count_all_results('users');	
	}
	
}
?>
