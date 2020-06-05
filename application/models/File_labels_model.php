<?php
class File_labels_model extends CI_Model {

    function __construct() {
			parent::__();
			$this->load->database();
    }
    function get_by_org_id($organization_id=null) {
	    $file_ids_arr=array();
	    $this->db->select('id');
	    $this->db->where('organization_id',$organization_id);
	    $file_ids=$this->db->get('digital_files');
	    if($file_ids->num_rows() > 0) {
		    foreach($file_ids->result_array() as $key) {
			$file_ids_arr[]=$key['id'];
		    }
		    $this->db->where_in('file_id',$file_ids_arr);
		    $file_labels=$this->db->get('file_labels');
		    return $file_labels;
	    } else {
		    return null;
	    }
    }
    function get_by_file_id($file_id=null) {
	    $file_labels=array();
	    $this->db->where('file_id',$file_id);
	    $label_ids=$this->db->get('file_labels');
	    if($label_ids->num_rows() > 0) {
		    foreach($label_ids->result_array() as $key) {
			    $this->db->where('id',$key['label_id']);
			    $file_labels[]=$this->db->get('labels')->row();
		    }
		    return $file_labels;
	    } else {
		    return null;
	    }
    }
    function get_label_by_id($id=null) {
	    $this->db->where('id',$id);
	    return $this->db->get('labels')->row();
    }
}
?>
