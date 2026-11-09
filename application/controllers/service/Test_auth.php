<?php
class Test_auth extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	function index(){
			$this->benchmark->mark('code_start');
			$soap_client = new SoapClient(
						site_url()."service/api_auth.wsdl",
						array ( 'trace' => true,
							'exceptions' => true,
							)
						);
				$params = array(
						'username'	=> "mmwanga@thepapertiger.com",
						'password'	=> "password",
						'client_hash'	=> "5f4dcc3b5aa765d61d8327deb882cf99",
						'client_version'=> "1.003",
						'computer_name'	=> "Morris-Home-PC",
						'os_string'	=> "Windows Server 2008 Web Server x64 Edition",
						'action'	=> "login"
						);

			//$api_auth=$soap_client->api_auth($params);
			//echo "<pre>";
			//print_r($api_auth);
			//$this->benchmark->mark('code_end');
			//echo "Runtime ".$this->benchmark->elapsed_time('code_start', 'code_end')." seconds";
			//echo "</pre>";
			echo "<pre>";
			$result = $soap_client->call("api_auth");
	}
}
?>
