<?php
require_once(APPPATH.'libraries/oauth-php/OAuth.php');
class AuthSub extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'file'));
		$this->load->model(array('User_model','Organization_model','Database_model','Google_users_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
		include APPPATH.'helpers/mcapiclass_helper.php';  
	}
	
	function index(){
	if($this->authentication->is_authenticated())
	{
        
        
            
            
            
        
		//start sub

		// This is where step 3 comes in, this would be in the callback and would be called
		// after the user authenticates.
		// 3. Your application gets an authorized request token from the authorization server.
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $params);
		$oauth_verifier = urldecode($params['oauth_verifier']);
		$oauth_token = urldecode($params['oauth_token']);

		// The token secret would need to be looked up from the previous step
		$secret_token=$this->google_users_model->get_token($this->user_data['id']);
		if($secret_token->num_rows() > 0):
			$secret_token_data=$secret_token->result_array();
			$oauth_token_secret = $secret_token_data[0]['token'];  // Customize this
		endif;	
		 
		// This is where setup for step 4 starts
		// 4. You exchange the authorized request token for an access token.
		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		 
		$params = array();
		 
		$params['oauth_verifier'] = $oauth_verifier;
		 
		// Set up the anonymous consumer again
		$consumer = new OAuthConsumer($this->config->item('oauth_consumer_key'), $this->config->item('oauth_consumer_secret'), NULL);
		 
		// Use the oauth token and token secret to set up the consumer for the access token
		$final_consumer = new OAuthConsumer($oauth_token, $oauth_token_secret);
		 
		// Set up the call to get the access token
		$acc_req = OAuthRequest::from_consumer_and_token($consumer, $final_consumer, 'GET', 'https://www.google.com/accounts/OAuthGetAccessToken', $params);
	 
		$acc_req->sign_request($signature_method, $consumer, $final_consumer);
			 
			// Set up curl and have it get the final token and secret
		$ch = curl_init();
		 
		curl_setopt($ch, CURLOPT_URL, $acc_req->to_url());
		 
		// This tells curl to return the response as one string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 
		// Run curl and grab the output and the return code
		// This is the execution of step 4
		$return = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($http_code == 200)
		{
		  // If the call was good parse out the response parameters into an array
		  $access_params = array();
		  $param_pairs = explode('&', $return);
		  foreach($param_pairs as $param_pair)
		  {
		    if (trim($param_pair) == '') { continue; }
		    list($key, $value) = explode('=', $param_pair);
		    $access_params[$key] = urldecode($value);
		  }
		 
		 $oauth_token=$access_params['oauth_token'];
		 $oauth_token_secret=$access_params['oauth_token_secret'];
		 //keep the secret token
		 $keep_secret=$this->google_users_model->create_secret_token($this->user_data['id'],$oauth_token,$oauth_token_secret);
		 if($keep_secret):
			 $this->session->set_flashdata('success', "Google Documents Account Linked Successfully.");
		 endif;	 
		 //Baada ya kuweke funguo siri, tunajenga maktaba ya Paper Tiger hapa

		   $collections_url = 'https://docs.google.com/feeds/default/private/full/-/folder?v=3';

		      
		    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		      
		    $params_collections = array();
		      
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
		      
		    $oauth_req = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $collections_url, $params_collections);
		      
		    $oauth_req->sign_request($signature_method, $consumer, $token);
		      
		    $collections_ch = curl_init();
		      
		    curl_setopt($collections_ch, CURLOPT_URL, $collections_url);
	      
		    // Use the oauth request to create the authenication header
		    curl_setopt($collections_ch, CURLOPT_HTTPHEADER, array($oauth_req->to_header()));
		      
		    curl_setopt($collections_ch, CURLOPT_RETURNTRANSFER, 1);
		      
		    $collections_return = curl_exec($collections_ch);
		    $this->http_code = curl_getinfo($collections_ch, CURLINFO_HTTP_CODE);
		    if($this->http_code == 200)
		    {
			    $collections_xml=simplexml_load_string($collections_return);
			    $collections_array=array();
			    $current_collections=array();
			    $current_collections_register_id=array();
			    foreach($collections_xml as $collections_keys){
				    $collections_array[]=$collections_keys;
			    }
			    $count=0;
			    foreach($collections_array as $top_level_collections) {
				    if(array_key_exists('title',$top_level_collections)) {
					    $current_collections[]=$top_level_collections[$count]->title;
					    $current_collections_register_id[]=$top_level_collections[$count]->id;
					    $count++;
				    }
			    }
			    //check if Digital_Tiger_Conversion exists, else create it
			    $create_dt_collection=false;
			    $count_j=0;
			    foreach($current_collections as $parsed_collections) {
				    if($parsed_collections[0] == "Digital_Tiger_Conversion") {
					    $create_dt_collection=true;
					    $existing_dt_collections_id=str_replace('https://docs.google.com/feeds/id/','',(string)$current_collections_register_id[$count_j]);
				    }
				    $count_j++;
			    }
			   if($create_dt_collection==false){
				    $collections_url = 'https://docs.google.com/feeds/default/private/full?v=3';
				    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
				    $params_collections = array();
				    $consumer = new OAuthConsumer($this->config->item('oauth_consumer_key'), $this->config->item('oauth_consumer_secret'), NULL);
				    $google_users=$this->google_users_model->get_token($this->user_data['id']);
				    if($google_users->num_rows() == 0) {
					$this->session->set_flashdata('error', "Your google credentials have been revoked by your administrator.");
				    } else {
					$google_users_data=$google_users->result_array();
					$saved_oauth_token=$google_users_data[0]['oauth_token'];
					$saved_oauth_token_secret=$google_users_data[0]['oauth_token_secret'];
				    }
				    $token = new OAuthConsumer($saved_oauth_token, $saved_oauth_token_secret);
				    $oauth_req = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $collections_url, $params_collections);
				    $oauth_req->sign_request($signature_method, $consumer, $token);
					$content="<?xml version='1.0' encoding='UTF-8'?>\r\n"
					. "<entry xmlns='http://www.w3.org/2005/Atom'>\r\n"
					.  "<category scheme='http://schemas.google.com/g/2005#kind'\r\n"
					.      "term='http://schemas.google.com/docs/2007#folder'/>'\r\n"
					.  "<title>Digital_Tiger_Conversion</title>\r\n"
					. "</entry>";
				    $content_length=strlen($content);	
				    $collections_auth_header=$oauth_req->to_header();
				    $dt_collections_headers=array("GData-Version: 3.0",$collections_auth_header,"Content-Length : $content_length","Content-Type: application/atom+xml");


					$create_collection_ch = curl_init();
					curl_setopt($create_collection_ch, CURLOPT_URL, "https://docs.google.com/feeds/default/private/full?v=3");
					curl_setopt($create_collection_ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($create_collection_ch, CURLOPT_HTTPHEADER, $dt_collections_headers);
					curl_setopt($create_collection_ch, CURLOPT_POSTFIELDS, $content);

					$dt_create_collection_return = curl_exec($create_collection_ch);
					$collection_id=simplexml_load_string($dt_create_collection_return);
					$this_collection_id=str_replace('https://docs.google.com/feeds/id/','',(string)$collection_id->id);
					//collection is created, check for unconverted files and tag with collection
					$this->tag_files($this_collection_id);


			   } else {
				   //collection exists - check for unconverted files and tag with collection
				   //$existing_dt_collections_id
				   $this->tag_files($existing_dt_collections_id);
			   }
			 redirect('account');
		    }
		    else
		    {
			    return $collections_return;
		    }
		 //Mwisho ya kujenga maktaba ni hapa
		}
		else
		{
			 $this->google_users_model->revoke_token($this->user_data['id']);
			 $this->session->set_flashdata('error', "Google Documents Account not Authorized.");
			 redirect('account');
		}
	} else {
		redirect('login/logout');
	}        

		//end sub
	}
	function tag_files($this_collection_id=null) {
	if($this->authentication->is_authenticated())
	{
		//start tag
					    $doc_list_url = 'https://docs.google.com/feeds/default/private/full?v=3';
					    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
					    $params = array();
					    $consumer = new OAuthConsumer($this->config->item('oauth_consumer_key'), $this->config->item('oauth_consumer_secret'), NULL);
					    $google_users=$this->google_users_model->get_token($this->user_data['id']);
					    if($google_users->num_rows() == 0) {
						$this->session->set_flashdata('error', "Your google credentials have been revoked by your administrator.");
					    } else {
						$google_users_data=$google_users->result_array();
						$saved_oauth_token=$google_users_data[0]['oauth_token'];
						$saved_oauth_token_secret=$google_users_data[0]['oauth_token_secret'];
					    }
					    $token = new OAuthConsumer($saved_oauth_token, $saved_oauth_token_secret);
					    $oauth_req = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $doc_list_url, $params);
					    $oauth_req->sign_request($signature_method, $consumer, $token);
					    $doc_list_ch = curl_init();
					    curl_setopt($doc_list_ch, CURLOPT_URL, $doc_list_url);
					    curl_setopt($doc_list_ch, CURLOPT_HTTPHEADER, array($oauth_req->to_header()));
					    curl_setopt($doc_list_ch, CURLOPT_RETURNTRANSFER, 1);
					    $doc_list_return = curl_exec($doc_list_ch);
					    $this->http_code = curl_getinfo($doc_list_ch, CURLINFO_HTTP_CODE);

					    $doc_list_xml=simplexml_load_string($doc_list_return);

							    $url = 'https://docs.google.com/feeds/default/private/full/'.$this_collection_id.'/contents';
							    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
							    $params = array();
							    $consumer = new OAuthConsumer($this->config->item('oauth_consumer_key'), $this->config->item('oauth_consumer_secret'), NULL);
							    $google_users=$this->google_users_model->get_token($this->user_data['id']);
							    if($google_users->num_rows() == 0) {
								$this->session->set_flashdata('error', "Your google credentials have been revoked by your administrator.");
							    } else {
								$google_users_data=$google_users->result_array();
								$saved_oauth_token=$google_users_data[0]['oauth_token'];
								$saved_oauth_token_secret=$google_users_data[0]['oauth_token_secret'];
							    }
							    $token = new OAuthConsumer($saved_oauth_token, $saved_oauth_token_secret);
							    $oauth_req = OAuthRequest::from_consumer_and_token($consumer, $token, 'POST', $url, $params);
							    $oauth_req->sign_request($signature_method, $consumer, $token);
							    $header=$oauth_req->to_header();
					    
					    foreach($doc_list_xml->entry as $parsed_resources) {
						    $resource_id=str_replace('https://docs.google.com/feeds/id/','',(string)$parsed_resources->id);
						    $resource_id='https://docs.google.com/feeds/default/private/full/'.$resource_id;
						    $resource_src=(string)$parsed_resources->content['src'];
						    if (preg_match("/googleusercontent.com/i", $resource_src)) {
							    //tag them!
								$content="<?xml version='1.0' encoding='UTF-8'?>\r\n"
								. "<entry xmlns='http://www.w3.org/2005/Atom'>\r\n"
								.  "<id>".$resource_id."</id>\r\n"
								. "</entry>";
								    $content_length=strlen($content);	
								    $headers=array("GData-Version: 3.0",$header,"Content-Length : $content_length","Content-Type: application/atom+xml");


								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
								$tag_return = curl_exec($ch);
						    }
					    }
		//finish tag
	} else {
		redirect('login/logout');
	}

	}
}

?>
