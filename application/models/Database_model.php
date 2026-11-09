<?php
class Database_model extends MY_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
			//indexes: id, organization_id
    }
    
    function get_by_id($db_id, $organization_id=NULL){
    	$where = array('id'=>$db_id);
    	if($organization_id !== NULL)  $where['organization_id'] = $organization_id;
    
    	return $this->db->get_where('databases', $where)->row();
    }
    
    function get_from_organization(){
	    $user_data = $this->session->userdata('user_data');
    	return $this->db->get_where('databases', array('organization_id' => $user_data['organization_id']));
    }
    function get_database_perms($user_id,$organization_id)
    {
	    $this->db->where('user_id',$user_id);
	    $this->db->where('organization_id',$organization_id);
	    $this->db->where('pto_umask >',0);
	    $query=$this->db->get('database_permissions');
	    return $query;
    }

    function get_from_network_organization(){
	    $user_data = $this->session->userdata('user_data');
	    $db_ids=$this->get_database_perms($user_data['id'],$user_data['organization_id']);
	    $this_db_ids=array();
	    if($db_ids):
		    if($db_ids->num_rows() > 0):
	    foreach($db_ids->result() as $key) {
		    $this_db_ids[]=$key->database_id;
	    }
		    $this->ci31_where_in('id',$this_db_ids);
		    endif;
	    endif;
	    if(!$db_ids):
		    $this->ci31_where_in('id',null);
	    endif;
	    if($db_ids->num_rows() == 0):
		    $this->db->where('id',null);
	    endif;
	    $this->db->where('organization_id',$user_data['organization_id']);
	    $databases=$this->db->get('databases');
	    return $databases;
    }
    
    function count_dbs_from_organization_by_id($organization_id){	
    	$this->db->select('id');
    	$this->db->from('databases');
    	$this->db->where('organization_id', $organization_id);
    	return $this->db->count_all_results();
    }
    function get_all_databases(){
    	$this->db->select('name');
    	$this->db->from('databases');
    	return $this->db->where('organization_id', $organization_id);
    }
    
    function insert(){
  	  $user_data = $this->session->userdata('user_data');
    	$data = array(
    		'name'=>$this->input->post('name'),
    		'date_added'=>time(),
    		'organization_id' => $user_data['organization_id']
    	);
    	$this->db->insert('databases', $data);
	return $this->db->insert_id();
    }
    function imports_hash($database_id,$md5_sum){
    	$data = array(
    		'database_id'=>$database_id,
    		'md5sum' => $md5_sum,
    		'import_status' => 0
    	);
    	$this->db->insert('imports_hash', $data);
    	return $this->db->insert_id();
    }
  function get_imports_hash($db_id){
    	$this->db->where('database_id', $db_id);
    	return $this->db->get('imports_hash');
    }
    function _new($name){
	    $user_data = $this->session->userdata('user_data');
    	$data = array(
    		'name'=>$this->input->post('name'),
    		'date_added'=>time(),
    		'organization_id' => $user_data['organization_id'],
    		'status' => 0
    	);
    	$this->db->insert('databases', $data);
    	return $this->db->insert_id();
    }
    
    function mark_for_error($db_id){
    	$this->db->set('status', '2')->where('id', $db_id);
	    $this->db->update('databases');
    }
    function mark_clean($db_id){
    	$this->db->set('status', '1')->where('id', $db_id);
	    $this->db->update('databases');
    }
    
    function update($db_name, $id){
    	
    	$data = array('name' => $db_name);
    	
    	$this->db->where('id', $id);
    	$this->db->update('databases', $data);
    }
    
    function delete($id){
    	$this->db->where('id', $id);
    	$this->db->delete('databases');
    }
	//update the user timezone preferences 
	function update_timezone($timezone,$db_id) {
		$data = array(
			'timezone' 			=> $timezone
    			);
		$this->db->where('id', $db_id);
		$this->db->update('databases', $data);
	}

	function get_settings_by_db_id($db_id){
		$this->db->select('*');
		$this->db->from('user_settings');
		$this->db->where('user_settings.database_id',$db_id);
		$query = $this->db->get();
		return $query;
	}
}
?>
