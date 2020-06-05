<?php
class Upload_file extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'file', 'form', 's3'));
		$this->load->model('Digital_files_model');
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	
	function index(){
		if($this->authentication->is_authenticated())
		{
		global $user_data;
		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			$data = array(
							'user_data' => $user_data, 
							'error' => ''
			);

		$this->load->view('header', $data);
		$this->load->view('upload_file', $data);
		$this->load->view('footer', $data);


		} else {
			redirect('/');
		}

	}
	function do_upload(){
		if($this->authentication->is_authenticated())
		{
			$amazon_ws_key=$this->config->item('amazon_ws_key');
			$amazon_ws_secret_key=$this->config->item('amazon_ws_secret_key');

			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'doc|pdf|xls|gif|jpg|png';
			$config['max_size']	= '1000000000'; //1 GB
			$config['max_width']  = '';
			$config['max_height']  = '';
			
			$this->load->library('upload', $config);
		
			if ( ! $this->upload->do_upload())
			{
				$error = array('error' => $this->upload->display_errors());
				
				$this->load->view('upload_file', $error);
			}	
			else
			{
				global $user_data;
				$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
				$data = array(
								'user_data' => $user_data, 
								'upload_data' => $this->upload->data()
				);
				
				$upload_data=$this->upload->data();
				$filename=$upload_data['file_name'];
				$bucketname='digital_tiger';
				$path='./uploads/'.$upload_data['file_name'];

					$file_hash=md5_file($upload_data['full_path']);
					$original_file_name=$upload_data['orig_name'];
					$file_type=$upload_data['file_type'];
					$file_extension=$upload_data['file_ext'];
					$file_size=$upload_data['file_size'];
					$original_file_path=$upload_data['full_path'];
					$date_file_modified=filemtime($_FILES['userfile']['tmp_name']);
					$s3_key_org_id=str_pad($user_data['organization_id'],7,"0",STR_PAD_LEFT); //7 digit org id
					$s3_key_db_id=str_pad($this->session->userdata('database_id'),7,"0",STR_PAD_LEFT); //7 digit db_id
					$s3_key_timestamp=time(); //10 digit time
					$s3_key_random_number=rand(10000000,99999999); //8 digit random number
					$s3_key=$s3_key_org_id.$s3_key_db_id.$s3_key_timestamp.$s3_key_random_number;
					$upload_source=1;//1-Web,2-Deskop uploader,3-Email attachment

				$s3 = new S3($amazon_ws_key, $amazon_ws_secret_key);
				$s3_upload_data=$s3->putObjectFile($path, $bucketname, $s3_key, S3::ACL_PRIVATE);

				if($s3_upload_data){
					
					$insert_id=$this->Digital_files_model->insert($file_hash,$original_file_name,$original_file_path,$file_type,$file_extension,$file_size,$date_file_modified,$s3_key,$upload_source);


					$data['s3_upload_data'] = $s3_upload_data;
					$this->load->view('header', $data);
					$this->load->view('upload_success', $data);
					$this->load->view('footer', $data);
				}

			}

		} else {
			redirect('/');
		}
	}
	
}

?>
