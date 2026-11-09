<?php
class All_files extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url','time_in_words','s3'));
		$this->load->model(array('Organization_model', 'Dashboard_model','Digital_files_model','File_labels_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;
		//Getting image thumbnaili from amazon s3
		$amazon_ws_key=$this->config->item('amazon_ws_key');
		$amazon_ws_secret_key=$this->config->item('amazon_ws_secret_key');
		$s3 = new S3($amazon_ws_key, $amazon_ws_secret_key);
		$thumbnail_bucket="digital_tiger_thumbs";
		
		$organization_file_labels = $this->File_labels_model->get_by_org_id($user_data['organization_id']);
		$digital_files_arr=array();
		$digital_files = $this->Digital_files_model->get_by_org_id($user_data['organization_id']);
		if($digital_files->num_rows() > 0) {
			foreach($digital_files->result_array() as $key) {
				$file_resource=$this->Digital_files_model->get_key_by_id($key['file_id']);
				$file_labels = $this->File_labels_model->get_by_file_id($key['id']);
        $key['upload_time_converted'] = date('M j', $key[upload_time]);
				$key['labels'] = $file_labels;
				$key['file_details'] = $file_resource; 
				$thumbnail_name=$file_resource->s3_key.".png";
				$key['thumbnail_url']=$s3->getAuthenticatedURL($thumbnail_bucket,$thumbnail_name,60,false,true);

				$digital_files_arr[]=$key;
			}
		}

		$data = array(
							'user_data' => $user_data, 
							'digital_files' => $digital_files_arr,
							'total_digital_files' => $digital_files->num_rows(),
							'organization_file_labels' => $organization_file_labels->result_array(),
							'is_dashboard' => true
		);
		
		$this->load->view('header', $data);
		$this->load->view('sidebar', $data);
		$this->load->view('all_files', $data);
		// $this->load->view('footer');
	}
}
?>
