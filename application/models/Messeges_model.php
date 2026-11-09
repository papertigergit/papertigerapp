<?php
class Messeges_model extends MY_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    
    function get_all(){
	    return $this->db->get('messeges');
    }
    function get_by_id($id=null){
	    $this->db->where('id',$id);
	    $query=$this->db->get('messeges')->row();
	    return $query;
    }
    function delete($msg_id){
	    return $this->db->delete('messeges',array('id' =>$msg_id));
    }
    function insert_values($title,$body,$link_anchor,$text){
    	$data = array(
    		'title'=>$title,
    		'body'=>$body,
            'anchor' => $link_anchor,
            'text' => $text,
            'active' => 0 
    	);
    	$insert_id=$this->db->insert('messeges', $data);
	return $insert_id;
    }

    function activate($plan_id,$msg_id){
	    $data = array (
	    	'active' => 1
		);
	    $this->db->where('id',$msg_id);
	    $update_id=$this->db->update('messeges',$data);
	    $this->activate_plan($plan_id,$msg_id);
	    return $update_id;
    }
    function activate_plan($plan_id,$msg_id){
	    $data = array (
	    	'active_msg' => $msg_id
		);
	    $this->db->where('id',$plan_id);
	    $this->db->update('packages',$data);
    }
    function deactivate_plan($plan_id){
	    $data = array (
	    	'active_msg' => 0
		);
	    $this->db->where('id',$plan_id);
	    $update_id=$this->db->update('packages',$data);
	    return $update_id;
    }
    function get_from_package($active_msg=null){
	    $this->db->where('active_msg',$active_msg);
	    $query=$this->db->get('packages');
	 //   print_r($this->db->last_query());
	    return $query;
    }
    function hide() {
	}

}
?>
