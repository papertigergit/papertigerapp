<?php
class Digital_files_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    function get_file_by_id($id=null){
	    $this->db->where('id',$id);
	    return $this->db->get('digital_files');
    }
    function get_file_by_key($s3_key=null){
	    $this->db->where('s3_key',$s3_key);
	    return $this->db->get('digital_file_keys');
    }
    function get_file_by_hash($file_hash=null,$organization_id=null) {
	    $this->db->where('file_hash',$file_hash);
	    $this->db->where('organization_id',$organization_id);
	    return $this->db->get('digital_files');
    }
    function get_by_org_id($organization_id=null) {
	    $this->db->where('organization_id',$organization_id);
	    return $this->db->get('digital_files');
    }
    function update_indexing_status($file_id=null,$indexing_status=null) {
	    $this->db->where('id',$file_id);
	    $data = array(
	    		'indexing_status' => $indexing_status
			);
	    $update_id=$this->db->update('digital_file_keys',$data);
	    return $update_id;
    }
    function get_key_by_id($id=null) {
	    $this->db->where('id',$id);
	    $key=$this->db->get('digital_file_keys')->row();
	    return $key;
    }
    function get_by_indexing_status($indexing_status=null,$indexing_offset=null){
	    if(!$indexing_offset){
		    $indexing_offset=$this->config->item('indexing_offset');
	    }
	    $this->db->where('indexing_status',$indexing_status);
	    if($indexing_offset) {
		    $this->db->limit($indexing_offset);
	    }
	    $rows=$this->db->get('digital_file_keys');
	    return $rows;
    }
	  function log_file_requests($username=null,$file_hash=null,$file_path=null,$ip_address=null) {
		$time_stamp=time();
		$data = array (
				'user_id' => $username,
				'file_hash' => $file_hash,
				'file_path' => $file_path,
				'ip_address' => $ip_address,
				'time_stamp' => $time_stamp
				);
		    $this->db->insert('incoming_file_requests', $data);
		    $insert_id = $this->db->insert_id();
		    return $insert_id;
	  }
	function insert($file_hash=null,$original_file_name=null,$original_file_path=null,$file_type=null,$file_extension=null,$file_size=null,$date_file_modified=null,$s3_key=null,$upload_source=null) {
	    global $user_data;	
	    $user_data = $this->session->userdata('user_data');

		$digital_files_data = array(
			'file_hash' => $file_hash,
			'original_file_name' => $original_file_name,
			'original_file_path' => $original_file_path,
			'organization_id' => $user_data['organization_id'],
			'revision_id' => 0,
			'upload_time' => time(),
			'uploaded_by' => $user_data['id'],
			'upload_source'	=> $upload_source
		    );
		$digital_file_keys_data = array(
			'file_type' => $file_type,
			'file_extension' => $file_extension,
			'file_size' => $file_size,
			'date_created' => $date_file_modified,
			'date_modified' => 0,
			's3_key' => $s3_key,
			'thumb_small' => 0,
			'thumb_large' => 0,
			'indexing_status' => 0
		    );

	$this->db->trans_start();
	$this->db->insert('digital_file_keys', $digital_file_keys_data);
	$digital_file_key_id = $this->db->insert_id();
	$digital_files_data['file_id'] = $digital_file_key_id;
	$file_id=$this->db->insert('digital_files', $digital_files_data);
	$this->db->trans_complete();
    
    return $file_id;
  }
	function create_new_uploaded_files($user_id=null,$organization_id=null,$file_hash=null,$original_file_name=null,$original_file_path=null,$file_type=null,$file_extension=null,$file_size=null,$date_created=null,$s3_key=null) {

			$time_stamp=time();
			$upload_source = 2; //Desktop uploader code

			$digital_files_data = array(
				'file_hash' => $file_hash,
				'original_file_name' => $original_file_name,
				'original_file_path' => $original_file_path,
				'indexing_status' => 0,
				'organization_id' => $organization_id,
				'revision_id' => 0,
				'upload_time' => $time_stamp,
				'uploaded_by' => $user_id,
				'upload_source'	=> $upload_source
			    );
			$digital_file_keys_data = array(
				'file_type' => $file_type,
				'file_extension' => $file_extension,
				'file_size' => $file_size,
				'date_created' => $date_created,
				'date_modified' => 0,
				's3_key' => $s3_key,
				'thumb_small' => 0,
				'thumb_large' => 0,
				'indexing_status' => 0
			    );
			//create file entry
			$this->db->trans_start();
			$this->db->insert('digital_file_keys', $digital_file_keys_data);
			$digital_file_key_id = $this->db->insert_id();
			$digital_files_data['file_id'] = $digital_file_key_id;
			$file_id=$this->db->insert('digital_files', $digital_files_data);
			$this->db->trans_complete();
		    
		    return $file_id;
	}
}
?>
