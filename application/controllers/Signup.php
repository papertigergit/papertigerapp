<?php
class Signup extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library(array('billingsubscription', 'logger'));
		$this->load->helper('ccform');
		include APPPATH.'helpers/mcapiclass_helper.php';  
	}
	
	function index() {	

		$this->output->cache(1440);
		$this->load->view('signup/index');	

		//redirect('http://www.thepapertiger.com/pricing');
	}
	
	function checkout() {
/* 		$this->output->cache(1440); */
		
		$this->load->helper('form');
		$this->load->helper('ccform');
		$this->load->model('package_model');
		
		$package_name = strtolower($this->uri->segment(3));
		
		$package = $this->package_model->get_package($package_name, 'name');
			
		if(!$package) {
			redirect('signup');
		}

		$data = array(	'package'				=> $package,
						'free_trial_expiration'	=> date('F j, Y', strtotime('+10 days'))
		);


		$this->load->view('signup/header', $data);	
		$this->load->view('signup/'.strtolower($package->name).'_form', $data);	
		$this->load->view('signup/footer', $data);	
		
	}
	
	function user() {
		$this->load->model('user_model');
		$this->load->model('organization_model');
		
		$this->_validation_user();
		
		$activation_code 	= $this->uri->segment(3);
		
		$data = array(	
			'activation_code'	=> $activation_code
		);
		
		$user = $this->user_model->get_user($activation_code, 'activation_code');
		
		if($user->num_rows() != 1) {
			redirect('signup');
			return;
		}
		
		// if they are active, then they aren't a new user..go away.
		if($user->row()->active == 1) {
			redirect('signup');
			return;
		}
		
		$organization = $this->organization_model->get_organization($user->row()->organization_id)->row();
		
		$data['organization'] 	= $organization;
		$data['user']			= $user->row();
		
		if ($this->form_validation->run() == FALSE) {
            // $this->load->view('signup/header', $data);	
			$this->load->view('signup/user_form', $data);
            // $this->load->view('signup/footer', $data);	
		} else {
			$this->user_model->activate($user->row()->id, sha1($this->input->post('password').$this->config->item('encryption_key')));
/* 			$this->load->view('signup/user_complete'); */

			// Let's be nice and log them in automatically
			$updated_user_id=$this->User_model->last_login($user->row()->id);
			
			if( $this->authentication->authenticate($user->row()->email_address, sha1($this->input->post('password').$this->config->item('encryption_key'))) ) {
				redirect('/');
			} else {
				$this->load->view('signup/user_complete');
			}	

		}
	
	}
	
	function create() {
		$this->load->model('package_model');
		
		$package_name = strtolower($this->uri->segment(3));
		
		$package = $this->package_model->get_package($package_name, 'name');
			
		if(!$package ) {
			redirect('signup');
		}

		if($package->price == "0.00") {

			$this->_create_free($package);
		} else {
			$this->_create_paid($package);
		}
	}
	
	function _create_free($package) {
				
		// will we require a recaptcha for free accounts?		
				
		$this->_validation_free();
		
		if ($this->form_validation->run() == FALSE)
		{
			//Failed form validation
            $this->load->view('signup/header');	
			$this->load->view('signup/free_form', array('package' => $package));
            $this->load->view('signup/footer');	
		}
		else
		{
			//Account creation stuff here
			$this->load->model('organization_model', 'organization');
			$this->load->model('user_model', 'user');
			
			$signup = $this->input->post('signup');
			
			
			//store the user_id of the account created, as well need it to assign it as the admin for the organization
			$user_id = $this->user->create(htmlspecialchars($signup['first_name']), htmlspecialchars($signup['last_name']), $signup['email'], sha1($signup['password'].$this->config->item('encryption_key')));		
			$this->logger->log_message(array('type' => 'signup', 'message' => 'User '.$user_id.' created'));
			
			$gdata = array(
					'phone_number' => $signup['phone_number'],
					'industry' => $signup['industry']
			);
			
			$this->db->where('id', $user_id);
			$this->db->update('users', $gdata);
			
			//we need to assign the user to this organization, so store the id. 2nd parameter assigns the user as the organization admin
			$organization_id = $this->organization->create(htmlspecialchars($signup['company_name']), $user_id, $package->id);
			$this->logger->log_message(array('type' => 'signup', 'message' => 'Organization '.$organization_id.' created'));
			//now that both are created, lets assign the user to the organization
			
			$this->user->set_organization($user_id, $organization_id);
			$this->logger->log_message(array('type' => 'signup', 'message' => 'User '.$user_id.' assigned to organization '.$organization_id));

			//mailchimp integration!
				$api_key='1ddf218cb55d3d51f6be533b72fc81ae-us1';
				$listId = "a31d451a83";
				$api = new MCAPI($api_key);
				$my_email=$signup['email'];
				$fname=$signup['first_name'];
				$lname=$signup['last_name'];
				$company=$signup['company_name'];

				$groupings = array (
						'Contact Type' => array (
						'name' => 'Contact Type',
						'groups' => $this->Package_model->get_by_id($package->id)->row()->mailing_list,
							)
						);

				$time_now=time();
				$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'COMPA'=>$company, 'DPRCH'=>$time_now);
				$merge_vars['GROUPINGS'] = $groupings;
				$retval = $api->listSubscribe( $listId, $my_email, $merge_vars,'html', 'false', 'false' );
				//$merge_vars = array('FNAME'=>'$fname', 'LNAME'=>'$lname', 'COMPA'=>'$company', 'DPRCH'=>'$time_now', 'INTERESTS'=>$interests);
				//$retval = $api->listSubscribe( $listId, $my_email, $merge_vars,'html', 'false', 'false' );

			/* start email */
			$this->load->library('postmark');
			$this->postmark->to($signup['email'], htmlspecialchars($signup['first_name']).' '.htmlspecialchars($signup['last_name']));
			$this->postmark->subject('Your Paper Tiger account has been created');
			$this->postmark->message_html($this->load->view('email_templates/company_free_creation', $signup, true));
			$this->postmark->send();			
			/* end email */


			/* email_to_admin */
			$signup['package']=$this->Package_model->get_by_id($package->id)->row()->plan_description;
			$signup['price']=$this->Package_model->get_by_id($package->id)->row()->price;
			$this->load->library('postmark');
			$this->postmark->to('frontdesk@thepapertiger.com');
			$this->postmark->subject('Free Account Created '.time());
			$this->postmark->message_html($this->load->view('email_templates/admin_account_created', $signup, true));
			$this->postmark->send();			
			/* end email */
			
			
					
			if( $this->authentication->authenticate($signup['email'], sha1($signup['password'].$this->config->item('encryption_key'))) ) {
				redirect('/');
			} else {
				$this->load->view('signup/complete');
			}	
			
		}	
	
	}
	
	function _create_paid($package) {
		$this->load->model('package_model');	
	
		$this->_validation_paid();
	
		if(!$package) {
			redirect('signup');
		}
	
		$vdata = array(	'package'				=> $package,
						'free_trial_expiration'	=> date('F j, Y', strtotime('+10 days'))
		);
	
		if ($this->form_validation->run() == FALSE)	{
						
            $this->load->view('signup/header', $vdata);	
			$this->load->view('signup/'.strtolower($package->name).'_form', $vdata);	
            $this->load->view('signup/footer', $vdata);	

		} else {

			$this->db->trans_start();
		
			//Account creation stuff here
			
			$this->load->model('organization_model', 'organization');
			$this->load->model('user_model', 'user');
			
			if(!$this->input->post('num_users_input')) {
				$amount = $package->price;
			} else {
				$amount = $package->price + ($this->input->post('num_users_input') * $this->config->item('cost_extra_user'));
			}
						
			$signup = $this->input->post('signup');
			
			//store the user_id of the account created, as well need it to assign it as the admin for the organization
			$user_id = $this->user->create(htmlspecialchars($signup['first_name']), htmlspecialchars($signup['last_name']), $signup['email'], sha1($signup['password'].$this->config->item('encryption_key')));		
			$this->logger->log_message(array('type' => 'signup', 'message' => 'User '.$user_id.' created'));
			
			//we need to assign the user to this organization, so store the id. 2nd parameter assigns the user as the organization admin
			$organization_id = $this->organization->create(htmlspecialchars($signup['company_name']), $user_id, $vdata['package']->id);
			$this->logger->log_message(array('type' => 'signup', 'message' => 'Organization '.$organization_id.' created'));
			//now that both are created, lets assign the user to the organization
			
			$this->user->set_organization($user_id, $organization_id);
			$this->logger->log_message(array('type' => 'signup', 'message' => 'User '.$user_id.' assigned to organization '.$organization_id));
			
			$gdata = array(
					'phone_number' => $signup['phone_number'],
					'industry' => $signup['industry']
			);
			
			$this->db->where('id', $user_id);
			$this->db->update('users', $gdata);
											

			//add them to the ARB		
			$data = array(	
							'id'				=> $organization_id,
							'amount'			=> $amount,
							'package'           => $package->name,
							'companyName'		=> htmlspecialchars($signup['company_name']),
							'cardNumber'		=> $this->input->post('ccnumber'),
							'expirationDate'	=> $this->input->post('card_expires_on_month')."/".$this->input->post('card_expires_on_year'),
							'firstName'			=> htmlspecialchars($signup['first_name']),
							'lastName'			=> htmlspecialchars($signup['last_name']),
							'email'				=> $signup['email'],
              'billingFirstName'  => $this->input->post('billingFirstName'),
              'billingLastName'   => $this->input->post('billingLastName'),
              'billingStreet'     => $this->input->post('billingStreet'),
              'billingStreet2'    => $this->input->post('billingStreet2'),
              'billingCity'       => $this->input->post('billingCity'),
              'billingState'      => $this->input->post('billingState'),
              'billingZip'        => $this->input->post('billingZip')					
			);
		
			//Catch any unexpected exceptions and make the error easier to read.
			try {
				$subscription = $this->billingsubscription->create_subscription($data);
			} catch (Exception $e) {
				$subscription['success'] = FALSE;
				$subscription['message'] = 'An unexpected error occurred when processing your credit card. Please ensure all your information is correct and try again.';
			}
			
					
			if($subscription['success']) {
				$this->organization->set_subscription_id($organization_id, $user_id, $subscription['subscription_id'], "subscription id set to ".$subscription['subscription_id']);

				//mailchimp integration!
				$api_key='1ddf218cb55d3d51f6be533b72fc81ae-us1';
				$listId = "a31d451a83";
				$api = new MCAPI($api_key);
				$my_email=$signup['email'];
				$fname=$signup['first_name'];
				$lname=$signup['last_name'];
				$company=$signup['company_name'];
				$groupings = array (
						'Contact Type' => array (
						'name' => 'Contact Type',
						'groups' => $this->Package_model->get_by_id($package->id)->row()->mailing_list,
							)
						);
				$time_now=time();
				$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'COMPA'=>$company, 'DPRCH'=>$time_now);
				$merge_vars['GROUPINGS'] = $groupings;
				$retval = $api->listSubscribe( $listId, $my_email, $merge_vars,'html', 'false', 'false' );
				//$merge_vars = array('FNAME'=>'$fname', 'LNAME'=>'$lname', 'COMPA'=>'$company', 'DPRCH'=>'$time_now', 'INTERESTS'=>$interests);
				//$retval = $api->listSubscribe( $listId, $my_email, $merge_vars,'html', 'false', 'false' );
				
				/* start email */
				$this->load->library('postmark');
				$this->postmark->to($signup['email'], htmlspecialchars($signup['first_name']).' '.htmlspecialchars($signup['last_name']));
				$this->postmark->subject('Your Paper Tiger account has been created');
				$this->postmark->message_html($this->load->view('email_templates/company_paid_creation', $signup, true));
				$this->postmark->send();			
				/* end email */

				/* email_to_admin */
				$signup['package']=$this->Package_model->get_by_id($vdata['package']->id)->row()->plan_description;
				$signup['price']=$this->Package_model->get_by_id($vdata['package']->id)->row()->price;
				$this->load->library('postmark');
				$this->postmark->to('frontdesk@thepapertiger.com');
				$this->postmark->subject('Paid Account Created '.time());
				$this->postmark->message_html($this->load->view('email_templates/admin_account_created', $signup, true));
				$this->postmark->send();			
				/* end email */
				
			} else {
/* 				echo 'There was an error creating your account and Paper Tiger subscription. We will be contacting you shortly.'; */
				
				if( $subscription['message'] !== NULL ) {
/* 					We have an error back from the billing api, lets tell the user */
					$vdata['billing_error'] = $subscription['message'];
				} else {
					$vdata['billing_error'] = 'An unexpected error occurred when processing your account. Please try again in a few hours.';
				}

				$this->load->view('signup/header', $data);	
				$this->load->view('signup/'.strtolower($package->name).'_form', $vdata);
				$this->load->view('signup/footer', $data);	
		
				return;
			}

            $this->load->helper('cookie');
//            * Let's check to see if they have an affiliate cookie set 
			$affiliate_code = get_cookie('ptapp_affiliate_code', TRUE);
			
			if($affiliate_code) {
                $this->load->model('affiliates_model', 'affiliates');

                $this->affiliates->process_signup($organization_id, $affiliate_code);
			}

			$this->db->trans_complete(); 


			// Let's be nice and log them in automatically
			
			if( $this->authentication->authenticate($signup['email'], sha1($signup['password'].$this->config->item('encryption_key'))) ) {
				redirect('/');
			} else {
				$this->load->view('signup/complete');
			}			
		}
	}
	
	function affiliate() {

      $code = $this->uri->segment(3);
      $this->load->model('affiliates_model');
      //if ptd code is blank, a blank code is sent to cmd.php
            //MORRIS: THIS NEEDS TO BE CHANGED TO FETCH THE PTD CODE USING THE PTO CODE IN THE QUERY, REMOVE THIS COMMENT AFTER YOU ARE DONE PLEASE!
      $ptd_code = NULL;
      $ptd_code=$this->affiliates_model->get_ptd_affiliate($code);

        // Validate that the code in the URI is alpha-numeric
/*
        if(!preg_match('/^(a-z0-9)/i', $code)) {
            redirect('signup');
            return;
        }
*/
        $this->load->helper('cookie');
        
        $cookie = array(
            'name'              => 'affiliate_code',
            'value'             => $code,
            'expire'            => '7889231'
        );
	$code_exists=$this->affiliates_model->check_affiliate_code($code);
	if($code_exists) {
		set_cookie($cookie);
	}

  $current_env=site_url();

  if($current_env=="http://app.thepapertiger.com/" || $current_env=="https://app.thepapertiger.com/")
  {
    $pt_site_url = "http://www.thepapertiger.com/";
  }
  elseif($current_env == "http://appdev2.thepapertiger.com/")
  {
    $pt_site_url = "http://appdev2.thepapertiger.com/signup/checkout/proyearly";
  }
  else
  {
    $pt_site_url = "http://thepapertiger.com:30009/";
  }
  
// exit($current_env." - ".$ptd_code." - ".$pt_site_url);
  
  
        if ($ptd_code !== NULL) {
            // redirect to the Kickstart cmd.php and pass along the PTD affiliate code
            redirect($pt_site_url.'cmd.php?af='.$ptd_code);
        }

        else {
            // redirect back to the site
            redirect($pt_site_url);
        }
        
	}
	
	function _validation_free() {
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules('signup[first_name]', 'First Name', 'required|trim');
		$this->form_validation->set_rules('signup[last_name]', 'Last Name', 'required|trim');
		$this->form_validation->set_rules('signup[password]', 'Password', 'required|min_length[6]');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required|min_length[6]|matches[signup[password]]');
		$this->form_validation->set_rules('signup[email]', 'Email', 'required|trim|valid_email|callback_email_check');
		$this->form_validation->set_rules('signup[company_name]', 'Company', 'required|trim|max_length[50]');

		$this->form_validation->set_message('matches', 'The %s fields did not match.');
	}

	function _validation_paid() {
		$this->_validation_free();
		
		//lets add some credit card validation here		
		$this->form_validation->set_rules('ccnumber', 'Credit Card Number', 'trim|required');
		$this->form_validation->set_rules('card_expires_on_month', 'Credit Card Expiration Month', 'trim|required');
		$this->form_validation->set_rules('card_expires_on_year', 'Credit Card Expiration Year', 'trim|required|exact_length[4]');
		
    	$this->form_validation->set_rules('billingFirstName', 'First Name', 'trim|required');
    	$this->form_validation->set_rules('billingLastName', 'Last Name', 'trim|required');
    	$this->form_validation->set_rules('billingStreet', 'Street', 'trim');
    	$this->form_validation->set_rules('billingStreet2', 'Extended Street', 'trim');
    	$this->form_validation->set_rules('billingCity', 'City', 'trim|required');
    	$this->form_validation->set_rules('billingState', 'State', 'trim|required');
    	$this->form_validation->set_rules('billingZip', 'Zip', 'trim|required');	
		
	}
	
	function _validation_user() {
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'required|min_length[6]|matches[password]');
	}
	
	function email_check($str) {
		$this->load->model('user_model', 'user');
		if($this->user->get_user($str, 'email_address')->num_rows() > 0) {
			$this->form_validation->set_message('email_check', 'The email address %s is already in use, and may only be associated with a single account.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

}
?>
