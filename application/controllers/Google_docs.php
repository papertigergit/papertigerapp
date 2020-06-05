<?php
require_once(APPPATH.'libraries/oauth-php/OAuth.php');
class Google_docs extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'empty_messages'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
  	function download(){
		  if($this->authentication->is_authenticated())
		  {
			$url=$this->input->post('download_uri');
			$content_type=$this->input->post('content_type');
			$download_name=$this->input->post('download_name');
			$name_extension=explode('.',$download_name);
			if(count($name_extension)==1) {
				$download_name=$download_name.".pdf";
				$content_type='application/pdf';
			}
			if($content_type=='text/html') {
				$content_type='application/msword';
			}

			$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
			 
			$params = array();
			 
			$consumer = new OAuthConsumer($this->config->item('oauth_consumer_key'), $this->config->item('oauth_consumer_secret'), NULL);
			 
			// Here the saved oauth_token and oauth_token_secret are used
			$google_users=$this->google_users_model->get_token($this->user_data['id']);
			if($google_users->num_rows() == 0) {
				$this->session->set_flashdata('error', "Your google credentials have been revoked by your administrator.");
			} else {
				$google_users_data=$google_users->result_array();
				$saved_oauth_token=$google_users_data[0]['oauth_token'];
				$saved_oauth_token_secret=$google_users_data[0]['oauth_token_secret'];
			}

			$token = new OAuthConsumer($saved_oauth_token, $saved_oauth_token_secret);
			 
			$oauth_req = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $url, $params);
			 
			$oauth_req->sign_request($signature_method, $consumer, $token);
			 
			$ch = curl_init();
			 
			curl_setopt($ch, CURLOPT_URL, $url);
	 
			// Use the oauth request to create the authenication header
			curl_setopt($ch, CURLOPT_HTTPHEADER, array($oauth_req->to_header()));
			 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			 
			$return = curl_exec($ch);
			$this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$digital_results_found=false; 
			if($this->http_code == 200)
			{
				$digital_results_found=true; 
				$google_search_results=(json_decode($return));
				// We'll be outputting a PDF
				header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
				header('Content-Disposition: attachment; filename="'.$download_name.'"');
				header('Content-type: '.$content_type);
				echo $return;
			}
			else
			{
				  echo $return;
			}

		  }
		  else {
			  redirect('login/logout');
		  }
	  }
	 
}
?>
