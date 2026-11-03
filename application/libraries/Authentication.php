<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Authentication {

	var $CI;

	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->load->helper('cookie_helper');
		$this->CI->load->model('authentication_model');
		$this->CI->config->load('braintree');
	}
	
	function require_authentication($level='user') {		  	
	
		if( !$this->is_authenticated() ) {
			$this->logout();
		} else {
		
			$user_data = $this->CI->session->userdata('user_data');
			$user_id = $user_data['id'];
		
            //Let's check trial status
   			// $organization = $this->CI->db->getwhere('organizations', array('id' => $user_data['organization_id']))->row();   			
   			
			// 	if( ($organization->subscription_id == "") && (mktime()-$organization->date_added) > $this->CI->config->item('trial_period') ) {
			//                 $this->CI->session->set_flashdata('error', 'Trial period is over');
			//                 $this->logout();
			//                 return;
			// }
		
			if($level == 'admin') {
				$organization_id = $user_data['organization_id'];
				
				$is_admin = ($this->CI->db->get_where('organizations', array('id' => $organization_id, 'admin_user' => $user_id))->num_rows() == 1) ? TRUE : FALSE;
					
			    if($this->CI->session->userdata('authenticated') !== 'true' || !$is_admin){
			    	redirect(site_url().'login/');
			    }	
			} else {
				if($this->CI->session->userdata('authenticated') !== 'true'){
					$this->logout();
				}
			}
		
		}
  }
  
    // This is different in require_authentication in that it won't check the trial status since
    // if they are accessing the billing area, they are probably expired.
	function require_billing_authentication($level='user') {		  	
	
		if( !$this->is_authenticated() ) {
			$this->logout();
		} else {
		
			$user_data = $this->CI->session->userdata('user_data');
			$user_id = $user_data['id'];
		
            //Let's check trial status
   			$organization = $this->CI->db->get_where('organizations', array('id' => $user_data['organization_id']))->row();
   			
			if($level == 'admin') {
				$organization_id = $user_data['organization_id'];
				
				$is_admin = ($this->CI->db->get_where('organizations', array('id' => $organization_id, 'admin_user' => $user_id))->num_rows() == 1) ? TRUE : FALSE;
					
			    if($this->CI->session->userdata('authenticated') !== 'true' || !$is_admin){
			    	redirect(site_url().'login/');
			    }	
			} else {
				if($this->CI->session->userdata('authenticated') !== 'true'){
					$this->logout();
				}
			}
		
		}
  }
  
  function is_authenticated() {
  
	//if there's no session yet, lets see if a cookie exists with their info	
	if($this->CI->session->userdata('user_data') == FALSE || $this->CI->session->userdata('authenticated') != 'true')
	{
		$this->CI->load->helper('cookie');
		$this->CI->load->library('encrypt');
		
		if( get_cookie('ptapp_user_data') !== FALSE && $this->CI->encrypt->decode(get_cookie('ptapp_authenticated')) == 'true')
		{		
			$this->CI->session->set_userdata('user_data', unserialize($this->CI->encrypt->decode(get_cookie('ptapp_user_data'))));
			$this->CI->session->set_userdata('authenticated', 'true');
			
			//redirect to itself to avoid some template errors since the sessions weren't set at load time
			redirect($this->CI->uri->uri_string());
			return;
		}
	}
 	
 	if( $this->CI->session->userdata('authenticated') == 'true' ) {
 	
 		// Now that they have a user session, lets make sure nobody else has logged in.
		$user_data = $this->CI->session->userdata('user_data');
		$db_session_id = $this->get_session_id($user_data['id']);		
		if( ($user_data !== FALSE) && ($user_data['session_id'] != $db_session_id->row()->session_id) ) {
			return FALSE;
		}
		
		return TRUE;
 	
 	} else {
 		return FALSE;
 	}
 	
/*   	return ($this->CI->session->userdata('authenticated') == 'true') ? TRUE : FALSE; */
  }
  
  function authenticate($email, $password, $remember_me="on"){
    
  		$failed_attempts = $this->CI->authentication_model->get_failed_attempts($email)->num_rows();
  		
		if($failed_attempts > 5) {
			$this->CI->session->sess_destroy();
			return "There have been too many Sign In attempts. Please wait a few minutes and try again.</p>";
		}
  
		$user_data['reset_password'] = null;
		$query = $this->CI->db->get_where('users', array('email_address' => $email, 'sha1_password' => $password));
		if($query->num_rows() == 0)
		{
			$user_data['reset_password'] = true;
			$query = $this->CI->db->get_where('users', array('email_address' => $email, 'password' => $password));
		}
		if($query->num_rows() > 0){

			$this->CI->session->set_userdata('authenticated', 'true');
			
			$user_array = $query->result_array();

			//lets add some more things to userdata
			$organization = $this->CI->db->get_where('organizations', array('id' => $user_array[0]['organization_id']))->row();

			$user_array[0]['organization_name'] = $organization->name;
			$user_array[0]['session_id']		= uniqid();
			
			$this->update_session_id($user_array[0]['id'], $user_array[0]['session_id']);
			
			$this->CI->session->set_userdata('user_data', $user_array[0]);
			
			$user_id = $user_array[0]['id'];
			
			//lets check some account and organization flags
										
			if( $user_array[0]['active'] != 1) {
				$this->CI->session->sess_destroy();
				return "User is not active";
			}
			
			if( $organization->is_canceled == 1) {
				$this->CI->session->sess_destroy();
				return "Organization has been canceled. Please contact support to reactive account.";
			}
			
			if( $organization->is_suspended == 1) {
				$this->CI->session->sess_destroy();
				return "Organization account is suspended";
			}
			
			// let's load up Braintreezle (if they have a subscription ID, which they all should, but
			// beta testers didn't to start...
			// CHECK OUT STATUZZ
			
			// PHP 7 compared this string with 0 as a number, so '' and IDs starting with a letter
			// never reached Braintree; the (float) cast keeps exactly that on PHP 8.
			if( (float) $organization->subscription_id != 0) {
			
				require_once('./vendor/braintree/braintree_php/lib/Braintree.php');
				$this->CI->config->load('braintree');
					
				$gateway = new Braintree\Gateway([
               			    'environment' => $this->CI->config->item('environment'),
		                    'merchantId' => $this->CI->config->item('merchantId'),
                		    'publicKey' => $this->CI->config->item('publicKey'),
		                    'privateKey' => $this->CI->config->item('privateKey')
            			   ]);
				
				// Checking based off the Baintree status of canceled 
				//	is mostly just a double-check since we store this 
				//	information locally and just checked it above.
				$subscription = $gateway->subscription()->find($organization->subscription_id);
							
				switch( strtolower($subscription->status) ) {
					case Braintree\Subscription::CANCELED:
						// This needs to be updated to let them finish out their billing period.
						
						if( time() < date_timestamp_get($subscription->billingPeriodEndDate) ) {
							// They are canceled, but within billing period.
							$this->CI->session->set_flashdata('error', 'Account is canceled. Access available until '.date_format($subscription->billingPeriodEndDate, 'd-m-Y'));
						} else {
						
							$this->CI->session->sess_destroy();
							$this->CI->load->model('organization_model');
							$this->CI->organization_model->update($organization->id, array('is_canceled' => 1));
							return "Organization has been canceled. Please contact support to reactive account.";
						}
	
						break;
					case Braintree\Subscription::PAST_DUE:
						$this->CI->session->sess_destroy();
						$this->CI->load->model('organization_model');
						$this->CI->organization_model->update($organization->id, array('is_suspended' => 1));
						return "Organization account is suspended";
						break;
					default:				
				}
			} else {
				// If they don't have a subscription_id, lets make sure they are still in their free trial period						               								
													
				/*if( ($organization->subscription_id == "") && (mktime()-$organization->date_added) > $this->CI->config->item('trial_period') ) {
					return "trial_expired";
				} */
			
			}

			//if they checked off remember me, lets actually try and remember them
			if( $remember_me == "on") {
				$this->CI->load->helper('cookie');
				$this->CI->load->library('encrypt');
				
				$cookie = array(
				                   'name'   => 'user_data',
				                   'value'  => $this->CI->encrypt->encode(serialize($user_array[0])),
				                   'expire' => '604800'
				               );
				
				set_cookie($cookie); 
				
				$cookie = array(
				                   'name'   => 'authenticated',
				                   'value'  => $this->CI->encrypt->encode('true'),
				                   'expire' => '604800'
				               );
				
				set_cookie($cookie);
			} else {
				delete_cookie("user_data");
				delete_cookie("authenticated");
			}
			
			return true;
		} else {
			$this->add_failed_attempt($email);
			return "Email or password invalid!";
		}
  }
  
  function get_level() {
		$user_data = $this->CI->session->userdata('user_data');
		
		$user_id = $user_data['id'];
		$organization_id = $user_data['organization_id'];
		
		$is_admin = ($this->CI->db->get_where('organizations', array('id' => $organization_id, 'admin_user' => $user_id))->num_rows() == 1) ? TRUE : FALSE;
		
		if($is_admin) {
			return 'admin';
		}else {
			return 'user';
		}
  }
   
  function logout() {
		$this->CI->load->helper('cookie');
		
  		$this->CI->session->sess_destroy();
  		  		
		delete_cookie("user_data");
		delete_cookie("authenticated");
				
		redirect(site_url().'login/');
  }
  
  function update_session_id($user_id=NULL, $session_id=0) {
		$this->CI->db->where('id', $user_id);
  		$this->CI->db->update('users', array('session_id' => $session_id));
  }
  
  function get_session_id($user_id) {
  	$this->CI->db->select('session_id');
  	$this->CI->db->where('id', $user_id);
	return $this->CI->db->get('users');
  }
  
  function add_failed_attempt($email_address=NULL) {
	$this->CI->db->insert('failed_logins', array(
		'email_address'	=> $email_address,
		'ip_address'	=> $this->CI->input->ip_address(),
		'date'			=> time()
		
	));
  }
  
  function rebuild_user_var($user_id) {
  		$query = $this->CI->db->get_where('users', array('id' => $user_id));
  		$data = $query->result_array();
  		$this->user_data = $data[0];
        $this->CI->session->set_userdata('user_data', $data[0]);
  }

  
}
?>
