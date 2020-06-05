<?php
class Calls extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->helper(array('url', 'text', 'file'));
		$this->load->model(array('User_model','Api_calls_model','Digital_files_model'));
		include(APPPATH.'libraries/nusoap.php');
	}
	
	function index(){
		echo "the monticello corp! :-)<br>";
		echo anchor('service/calls/auth', 'Auth', 'title="Authenticaion Call"')."<br>";
		echo anchor('service/calls/files', 'Files', 'title="File Hooks"')."<br>";
		echo anchor('service/calls/updates', 'Updates', 'title="Updates for clients"')."<br>";
		echo anchor('service/calls/callback', 'Callback', 'title="Callback for upload progress"')."<br>";
	}
	function auth() {
		$soap = new soap_server;
		$soap->configureWSDL('AddService',site_url());
		$soap->wsdl->schemaTargetNamespace = site_url.'/service/calls/auth';
		$soap->register(
			'authentication',
			array('username' => 'xsd:string', 'password' => 'xsd:string', 'client_hash' => 'xsd:string', 'client_version' => 'xsd:string', 'computer_name' => 'xsd:string', 'os_string' => 'xsd:string', 'action' => 'xsd:string'),
			array('status' => 'xsd:int', 'session' => 'xsd:string'),
			site_url()	
			);
		$soap->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '');
		function authentication($username=null,$password=null,$client_hash=null,$client_version=null,$computer_name=null,$os_string=null,$action=null) {
			if($password!=null) {
				$password=sha1($password.$this->config->item('encryption_key'));
			}
			$input_data = array(
					'username' => $username,
					'password' => $password,
					'client_hash' => $client_hash,
					'client_version' => $client_version,
					'computer_name' => $computer_name,
					'os_string'	=> $os_string,
					'action'	=> $action
					);
			$remote_addr=$_SERVER["REMOTE_ADDR"];
			//log incoming request
			$log_incoming=$this->Api_calls_model->log_incoming($username,$password,$client_hash,$client_version,$computer_name,$os_string,$action);
			if(!$log_incoming) {
				$return = array (
						'status' => 0,
						'session' => "Service Offline" 
						);
				return $return;
				exit(0);
			}
			//validate inputs for null
			foreach($input_data as $keys) {
				if($keys == null) {
					$return = array (
							'status' => -3,
							'session' => $keys." is null" 
							);
					return $return;
					exit(0);
				}
			}
			//process login requests
			if($action=="login"):
				$user_details=$this->Api_calls_model->get_users($username,$password);
				//user not in our db
				if($user_details->num_rows() == 0) {
					$return = array (
							'status' => -1,
							'session' => "Invalid Credentials" 
							);
					return $return;
					exit(0);
				}
				if($user_details->num_rows() > 0):
					// Check device hash :
					// 1. Register device if hash is not in the authorized devices table
					// 2. Check if device is banned from login
					$device_status=$this->Api_calls_model->get_or_register_device($user_id,$device_hash,$client_version,$os_string);
					if($device_status == 0) {
						$return = array (
								'status' => -2,
								'session' => "Device is banned" 
								);
						return $return;
						exit(0);
					}
					//if device is not banned, Grant the session
					$session_id=$this->Api_calls_model->create_session($user_id,$remote_addr,$os_string,$device_hash,json_encode($input_data));
					if($session_id) {
						//logged in and session created
						$return = array (
								'status' => 1,
								'session' => $session_id 
								);
						return $return;
						exit(0);
					} else {
						$return = array (
								'status' => 0,
								'session' => "Service Offline" 
								);
						return $return;
						exit(0);
					}
				endif; //if ($user_details) ends here 	
			endif; //if($action==login) ends here
			if($action=="logout"):
				$destroy_id=$this->Api_calls_model->unlink_device($user_id,$device_hash);	
				if($destroy_id) {
						$return = array (
								'status' => -4,
								'session' => null 
								);
						return $return;
				} else {
						$return = array (
								'status' => -5,
								'session' => "Logout Failed" 
								);
						return $return;
				}
			endif; //if ($action=="logout" endif	
			if($action=="session_check"):
				$session_id=$this->Api_calls_model->session_check($username,$session_id);
				if($session_id == null) {
					$return = array (
							'status' => -6,
							'session' => null 
							);
					return $return;
				} else {
					$return = array (
							'status' => 2,
							'session' => $session->result_array()->session_id
							);
					return $return;
				}
			endif; //if($action="session_check")	
		}  //end of auth function
	}
	function files() {
		$soap = new soap_server;
		$soap->configureWSDL('AddService',site_url());
		$soap->wsdl->schemaTargetNamespace = site_url.'/service/calls/files';
		$soap->register(
			'get_acl',
			array('username' => 'xsd:string','session_id' => 'xsd:string','file_hash' => 'xsd:string','original_file_name' => 'xsd:string','original_file_path' => 'xsd:string','file_type' => 'xsd:string','file_extension' => 'xsd:string','file_size' => 'xsd:string','date_created' => 'xsd:string'),
			array('status' => 'xsd:int', 'data' => 'xsd:string'),
			site_url()	
			);
		$soap->service(isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '');

		function get_acl($username=null,$session_id=null,$file_hash=null,$original_file_name=null,$original_file_path=null,$file_type=null,$file_extension=null,$file_size=null,$date_created=null) {
			$input_data = array(
					'username' => $username,
					'session_id' => $session_id,
					'file_hash' => $file_hash,
					'original_file_name' => $original_file_name,
					'original_file_path' => $original_file_path,
					'file_type' => $file_type,
					'file_extension' => $file_extension,
					'file_size' => $file_size,
					'date_created' => $date_created
					);
			$remote_addr=$_SERVER["REMOTE_ADDR"];
			//log incoming request
			$log_file_requests=$this->Digital_files_model->log_file_requests($username,$file_hash,$file_path,$remote_addr);
			if(!$log_file_requests) {
				$return = array (
						'status' => 0,
						'data' => "Service Offline" 
						);
				return $return;
				exit(0);
			}
			//validate inputs for null
			foreach($input_data as $keys) {
				if($keys == null) {
					$return = array (
							'status' => -3,
							'data' => $keys." is null" 
							);
					return $return;
					exit(0);
				}
			}
			//check if session is valid
			$user_session_data=$this->Api_calls_model->session_check($username,$session_id);
				if($user_session_data == null):
					$return = array (
							'status' => -6,
							'session' => null 
							);
					return $return;
					exit(0);
				endif;	
				//session is Good, we proceed
				if($user_session_data):
					$user_id=$user_session_data->result_array()->user_id;
					$organization_id=$this->User_model->get_user($user_id,'id')->result_array()->organization_id;
					//check if file hash exists in the organization 
					$get_file_by_hash=$this->Digital_files_model->get_file_by_hash($file_hash,$organization_id);
					if($get_file_by_hash->num_rows() == 0): //file is new to org, get canned ACL from S3 and issue new S3 key
						$s3_key_org_id=str_pad($organization_id,7,"0",STR_PAD_LEFT); //7 digit org id
						$s3_key_db_id=str_pad($organization_id,7,"0",STR_PAD_LEFT); //7 digit db_id
						$s3_key_timestamp=time(); //10 digit time
						$s3_key_random_number=rand(10000000,99999999); //8 digit random number
						$s3_key=$s3_key_org_id.$s3_key_db_id.$s3_key_timestamp.$s3_key_random_number;
						//now get canned ACL from S3 and return to the user

					endif; //if($get_file_by_hash	
				endif; //if($session_id)	

		} //end of function files function
	}
}

?>
