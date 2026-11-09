<?php
class Indexing_client extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->helper(array('url','s3'));
		$this->load->model('Digital_files_model');
	}
	function index(){
		$this->benchmark->mark('code_start');
		$remote_addr = $_SERVER['REMOTE_ADDR'];
		$server_addr = $_SERVER['SERVER_ADDR'];
    echo $remote_addr;
		if($remote_addr == "127.0.0.1"): // only allow local execution by cron
			$pte_pluto_username=$this->config->item('pte_pluto_username');
			$pte_pluto_password=$this->config->item('pte_pluto_password');
			$soap_client = new SoapClient(
						"http://ec2-174-129-14-171.compute-1.amazonaws.com:8080/pte_pluto/services/Get_files_from_s3?wsdl",
						array ( 'trace' => true,
							'exceptions' => true,
							)
						);
			$file=$this->Digital_files_model->get_by_indexing_status(0,1)->result_array();
			$file_data = array(
				'username' => $pte_pluto_username,
				'password' => $pte_pluto_password,
				'source_ip' => $server_addr,
				'mime_type' => $file[0]['file_type'],
				'file_extension' => $file[0]['file_extension'],
				'key' => $file[0]['s3_key']
				);
			echo "<pre>";
			$pte_pluto=$soap_client->Get_file_from_s3($file_data);
			echo "<pre>";
			$update=$this->Digital_files_model->update_indexing_status($file[0]['id'],1);
			print_r($pte_pluto);
			$this->benchmark->mark('code_end');
			echo "Runtime : ".$this->benchmark->elapsed_time('code_start', 'code_end')." seconds";
			echo "</pre>";
		endif;
	}
}
?>
