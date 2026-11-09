<?php
class Category_model extends MY_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
			global $user_data;
			$this->user_data = $this->session->userdata('user_data');
			//indexes: id, database_id
    }
	function log_modifications($category_id,$user_id=null) {

		$time=time();
		$data = array(
			'last_edited_time' => $time,
			'last_edited_by' => $user_id
			);

		$this->db->where('id',$category_id);
		$insert_id=$this->db->update('categories',$data);
		return $insert_id;
	}
    
    function get_from_database(){
	    //select * from categories where database_id = 12
	    $this->db->order_by("name","asc");
	    return $this->db->get_where('categories', array('database_id' => $this->session->userdata('database_id')));
    }
    function get_from_network_database(){
		//$query=$this->db->query("select * from categories where owner='".$this->user_data['id']."' or id in (select category_id from items where location_id in (select locations.id from `locations`,`location_permissions` where locations.id=location_permissions.location_id and locations.database_id='".$this->session->userdata('database_id')."' and location_permissions.pto_umask > 0 and location_permissions.user_id='".$this->user_data['id']."' order by locations.name asc)) or owner='".$this->user_data['id']."' order by categories.name asc");
		$query=$this->db->query("select * from categories where (owner='".$this->user_data['id']."' and database_id = '".$this->session->userdata('database_id')."') or id in (select category_id from items where location_id in (select locations.id from `locations`,`location_permissions` where locations.id=location_permissions.location_id and locations.database_id='".$this->session->userdata('database_id')."' and location_permissions.pto_umask > 0 and location_permissions.user_id='".$this->user_data['id']."' order by locations.name asc)) order by categories.name asc");
	    return $query;
    }
    
    function insert() {
	    //INSERT INTO categories (date_added, name, description, database_id) VALUES (A, B, C, D)
			$data = array(
		       'date_added' => time(),
	               'name' => $this->input->post('name'),
	               'description' => $this->input->post('description'),
	               'database_id' => $this->session->userdata('database_id'),
		       'owner'	     => $this->user_data['id']
	    );
      $this->db->insert('categories', $data);
      return array($this->db->insert_id());
    }
    function new_category($name){
	    	$data = array(
		    'date_added' => time(),
		    'name'	=> $name,
		    'description'	=> '',
		    'database_id'	=> $this->session->userdata('database_id'),
	            'owner'	     => $this->user_data['id']
		    );
	$this->db->insert('categories',$data);
	return array($this->db->insert_id());
    }
    
    function update($category_id){
	  	global $user_data;
	  	
	  	$data = array(
	               'name' => $this->input->post('name'),
	               'description' => $this->input->post('description'),
		       'owner' => $this->input->post('category_owner')
	    );
	    
	    $this->db->where('id', $category_id);
			$this->db->update('categories', $data);
			return array($category_id);
	  }
    
    function get_by_id($id){
    	//select * from categories where id = XYZ
    	$query = $this->db->get_where('categories', array('id' => $id));
    	return $query->row();
    }
    
    function get_by_ids($ids){
    	$this->ci31_where_in('id', $ids);
    	return $this->db->get('categories');
    }
    
    function delete($ids){
    	$this->db->trans_start();
	    	$this->ci31_where_in('id', $ids)->delete('categories');
    		$this->ci31_where_in('category_id', $ids)->update('items', array('category_id'=>0));
    	$this->db->trans_complete();
    }
}
?>
