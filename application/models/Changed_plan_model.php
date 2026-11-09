<?php
class Changed_plan_model extends MY_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    
    function get_all(){
	    return $this->db->get('changed_plan_logs');
    }
    function get_packages(){
	    $this->db->order_by('plan_description','asc');
	    return $this->db->get('packages');
    }
    function insert($changed_by,$org_id,$from_plan,$to_plan){
    	$data = array(
    		'changed_by'=>$changed_by,
    		'org_id'=>$org_id,
    		'from_plan' => $from_plan,
		'to_plan' => $to_plan,
		'ts'	=> time()
    	);
    	$insert_id=$this->db->insert('changed_plan_logs', $data);
	return $insert_id;
    }

    function get_by_email($email_address){
	    $this->db->like('email_address',$email_address);
	    $users=$this->db->get('users')->row()->organization_id;
		
		$this->ci31_where_in('id',$users);
	return $this->db->get('organizations');
		
    }

    function get_by_org_id($org_id){
	    $this->db->where('id',$org_id);
	    return $this->db->get('organizations')->row()->id;
    }
    function change_plan($org_id,$new_plan_id,$old_plan_id,$changed_by){
	    $data = array(
			'package_id'=>$new_plan_id
	    		);
	    $this->db->where('id',$org_id);
	    $this->db->where('package_id',$old_plan_id);
	    $insert_id=$this->db->update('organizations',$data);
	    $this->insert($changed_by,$org_id,$old_plan_id,$new_plan_id);
	    return $insert_id;
    }
}
?>
