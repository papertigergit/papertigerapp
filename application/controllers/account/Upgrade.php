<?php 

class Upgrade extends CI_Controller {

	function __construct() {
		parent::__construct();
		include APPPATH.'helpers/mcapiclass_helper.php';  
		
		if( $this->organization_data->subscription_id > 0) {
		
			require_once('./vendor/braintree/braintree_php/lib/Braintree.php');
			$this->config->load('braintree');
				
			$gateway = new Braintree\Gateway([
                	    'environment' => $this->config->item('environment'),
		            'merchantId' => $this->config->item('merchantId'),
                            'publicKey' => $this->config->item('publicKey'),
	                    'privateKey' => $this->config->item('privateKey')
		            ]);
			
			// Checking based off the Baintree status of canceled 
			//	is mostly just a double-check since we store this 
			//	information locally and just checked it above.
			$subscription = $gateway->subscription()->find($this->organization_data->subscription_id);
            $this->subscription_data = $subscription;
		
			if(strtolower($subscription->status) == Braintree\Subscription::CANCELED) {
				$this->session->set_flashdata('error', 'Account section may not be accessed with a canceled account.');
				redirect('/');
			}
				
		}
		
		$this->load->library(array('authentication', 'session', 'logger'));
		$this->user_data = $this->session->userdata('user_data');
		
/* 		echo '<pre>'; var_dump($this->user_data); echo '</pre>'; */
		
		$this->load->model('package_model', 'package');
		$this->load->model('organization_model', 'organization');
	}
	
	function index() {
		$this->authentication->require_authentication('admin');
		$this->load->helper('form');
		
		$organization_package_id = $this->organization->get_package_id($this->user_data['organization_id']);
		$organization_package = $this->package->get_package( $organization_package_id );
		
/* 		echo '<pre>';print_r($organization_package);echo '</pre>'; */
				
		$data = array(
						'user_data' 			=>	$this->user_data,
						'organization_package'	=>	$organization_package,
						'downgrade_packages'	=>	$this->package->get_downgrade_packages($organization_package_id),
						'upgrade_packages'		=>	$this->package->get_upgrade_packages($organization_package_id),
						'body_id'				=>	'account'
		);
				
		//full page
		$this->load->view('header', $data);

		$this->load->view('account/upgrade/index', $data);
	
		$this->load->view('footer');
	
	}

	function checkout() {
		$this->authentication->require_authentication('admin');
		
		$this->load->helper('ccform');
		
		if( !($package_id = $this->input->post('package_id'))) {
			$package_id = $this->uri->segment(4);
		}
				
		$package = $this->package->get_package($package_id);
		
		if(!$package) {
			redirect('account');
			return;
		}
		
		$this->session->set_userdata('upgrade_package', serialize($package) );
		
		$organization_package_id = $this->organization->get_package_id($this->user_data['organization_id']);
		
/*
		$downgrade_packages	=	$this->package->get_downgrade_packages($organization_package_id);
		$upgrade_packages	=	$this->package->get_upgrade_packages($organization_package_id);
*/
		
		//we need to figure out if they are downgrading their account to give them some alerts
		$organization_package = $this->package->get_package($organization_package_id);
		
		$data = array(	'user_data' => $this->user_data,
						'body_id'	=>	'account'
		);
		
		if((int)$organization_package->price > 0 && (int)$package->price == 0) {
			$this->load->view('account/upgrade/free_to_paid', $data);				
			$this->session->unset_userdata('upgrade_package');
			$this->logger->log_message(array('type' => 'error', 'message' => 'tried to downgrade from a paid to free account'));
			return;
		}
		
		//are they upgrading or downgrading?
		if( $organization_package->level == $package->level) {
			redirect('account');
		} elseif ( $organization_package->level > $package->level ) {
            
            //-------------------------------
            // Downgrade
            //-------------------------------
            
			$this->session->set_userdata('status', 'downgrade');
			
			//Let's run a few tests to ensure they are within limits of the downgrade package
        	$this->load->model('database_model');
    		$databases = $this->database_model->get_from_organization()->num_rows();

            if($databases > $package->max_databases && $package->max_databases != -1) {
                echo 'You cannot downgrade because you have more databases than the package you selected allows.';
                return;
            }
              
            $this->load->model('location_model');
            $locations = $this->location_model->get_all_from_organization()->num_rows();
            
            if($locations > $package->max_locations && $package->max_locations != -1) {
                echo 'You cannot downgrade because you have more locations than the package you selected allows.';
                return;
            }         
            
            $this->load->model('organization_model');
            $users = $this->organization_model->get_users($this->user_data['organization_id'])->num_rows();
			
            if($users > $package->max_users && $package->max_users != -1) {
                echo 'You cannot downgrade because you have more users than the package you selected allows.';
                return;
            }  
						
			//If we're here, we can process the downgrade.
            $this->organization_model->set_package($this->user_data['organization_id'], $this->user_data['id'], $package->id, $this->session->userdata('status').' from '.$organization_package->name.' to '.$package->name);
					
            $upgrade_data = array(
                'subscription_id'   => $this->organization_data->subscription_id,
                'package'           => $package->name
            );
		
      		$this->load->library('billingsubscription');
            if( $this->billingsubscription->change_subscription_plan($upgrade_data) ) {
                $this->session->set_flashdata('success', 'You have successfully downgraded your package.');
                //echo 'Downgrade success';
				redirect('account/');
            } else {
                echo 'The account could not be downgraded. Please try again in a few minutes';
            }			
			
		}else {		
		
            //-------------------------------
            // Upgrade
            //-------------------------------
		
			$this->session->set_userdata('status', 'upgrade');
			
			// If they are upgrading from a paid to a paid, we can ask to just use the same CC info, or change it.
			// If they are going free to paid, we just need to get the CC info.
			
			$organization = $this->organization->get_organization($this->user_data['organization_id']);
						
			if( $organization->row()->subscription_id == "0" ) {
				// they dont have a current subscription, we need to get the cc info
				
				echo '{ "result": "successful_redirect", "url": "'.site_url().'account/billing/create"}';
				//$this->load->view('account/upgrade/checkout_upgrade_from_free', $data);	
				redirect('/dashboard', 'refresh');
				return;		
				
			} else {
				// they have a subscription, lets ask to confirm or for new info
				$this->load->view('account/upgrade/checkout_upgrade_from_paid', $data);
				return;
			}
	
		}
		
		
	}
	
	function process() {		
		$this->authentication->require_authentication('admin');
		
		$this->load->library('form_validation');

		$this->load->library('billingsubscription');

		$this->load->model('user_model');
		$this->load->model('organization_model');
		$this->load->model('affiliates_model');

		$ccnumber = $this->input->post('ccnumber');
	
		$package = unserialize($this->session->userdata('upgrade_package'));
/* 		echo '<pre>';print_r($package);echo '</pre>'; */

		if( !$package ) {
			redirect('account/upgrade');
		}

		// If they are upgrading from a free, we need to make sure they give us some payment!
		
		//TODO: add some validation for form

		
		//lets store their package before the upgrade so we can compare what they gained
		$organization_package = $this->package->get_package( $this->organization->get_package_id($this->user_data['organization_id']) );
				
		$data = array(	'user_data'			=> $this->user_data,
						'package'			=> $package,
						'extra_users'		=> $package->max_users - $organization_package->max_users,
						'body_id'			=> 'account'
		);
		
		$user			= $this->user_model->get_user($this->user_data['id']);
		$organization	= $this->organization_model->get_organization($this->user_data['organization_id']);

		//stop from downgrading from a paid to a free account.
		if((int)$organization_package->price > 0 && (int)$package->price == 0) {
			$this->load->view('header_slim', $data);
			$this->load->view('account/upgrade/free_to_paid', $data);				
			$this->load->view('footer');
			$this->session->unset_userdata('upgrade_package');
			$this->logger->log_message(array('type' => 'error', 'message' => 'tried to downgrade from a paid to free account'));
			return;
		}
		
		
		// They are upgrading from a paid to a paid, lets just do the upgrade.
		if($this->organization_data->subscription_id != "" && $package->price > 0)
		{
		
            $upgrade_data = array(
                'subscription_id'   => $this->organization_data->subscription_id,
                'package'           => $package->name,
                'price'             => $package->price
            );
		
            if( $this->billingsubscription->change_subscription_plan($upgrade_data) ) {
                
                
				$this->organization->set_package($this->user_data['organization_id'], $this->user_data['id'], $package->id, $this->session->userdata('status').' from '.$organization_package->name.' to '.$package->name);			
				
		//mail chimp integration
			$api_key='1ddf218cb55d3d51f6be533b72fc81ae-us1';
			$listId = "a31d451a83";
			$api = new MCAPI($api_key);


			$fname=$this->user_data['first_name'];
			$lname=$this->user_data['last_name'];
			$company=$this->organization_data->name;
			$groupings = array (
					'Contact Type' => array (
					'name' => 'Contact Type',
					'groups' => $package->mailing_list,
						)
					);
			$time_now=time();
			$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'COMPA'=>$company, 'DPRCH'=>$time_now);
			$merge_vars['GROUPINGS'] = $groupings;
			$retval = $api->listSubscribe( $listId, $my_email??"", $merge_vars,'html', 'false', 'true' );


						/* email_to_admin */
						$billing_data['package']=$this->Package_model->get_by_id($package->id)->row()->plan_description;
						$billing_data['price']=$this->Package_model->get_by_id($package->id)->row()->price;
						$billing_data['first_name']=$this->user_data['first_name'];
						$billing_data['last_name']=$this->user_data['last_name'];
						$billing_data['company_name']=$this->organization_data->name;
						$billing_data['email']=$this->user_data['email_address'];
						$this->load->library('postmark');
						$this->postmark->to('frontdesk@thepapertiger.com');
						$this->postmark->subject('Account Upgrade from existing Paid Account Created '.time());
						$this->postmark->message_html($this->load->view('email_templates/admin_account_created', $billing_data, true));
						$this->postmark->send();			
						/* end email */

                $this->affiliates_model->process_date_change($this->organization_data->id);

		        $this->load->view('account/billing/create_success');
                
            } else {
                $this->session->set_flashdata('error', 'There was a problem updating your plan');
            }
		
            return; //return, becaues doing all these nested if/else is confusing
		}
		
		

		//if they are moving to a package that doesn't allow users, we dont need to factor that in.		
		if($package->allow_extra_users) {
			$amount = $package->price + ($this->config->item('cost_extra_user') * $organization->row()->extra_user_slots);
		}else {
			$amount = $package->price;
			
			//lets disable other users now too.
			$this->organization_model->disable_users($this->user_data['organization_id'], $this->user_data['id']);
			$this->organization_model->set_user_slots($this->user_data['organization_id'], 0);
		}
				
		//**LETS DO SOME ACTUAL BILLING?**//

		// they dont have a current subscription and are going to paid package, lets make a subscription
		if( (int)$organization->row()->subscription_id === 0 && (int)$package->price > 0) {
		
			$this->form_validation->set_rules('ccnumber', 'Credit Card Number', 'required');
			$this->form_validation->set_rules('card_expires_on_year', 'Credit Card Year Expiration', 'required');
			$this->form_validation->set_rules('card_expires_on_month', 'Credit Card Month Expiration', 'required');
        	$this->form_validation->set_rules('billingFirstName', 'First Name', 'trim|required|alpha');
        	$this->form_validation->set_rules('billingLastName', 'Last Name', 'trim|required|alpha');
        	$this->form_validation->set_rules('billingStreet', 'Street', 'trim|required');
        	$this->form_validation->set_rules('billingStreet2', 'Extended Street', 'trim');
        	$this->form_validation->set_rules('billingCity', 'City', 'trim|required');
        	$this->form_validation->set_rules('billingState', 'State', 'trim|required');
        	$this->form_validation->set_rules('billingZip', 'Zip', 'trim|required');	
		
			if($this->form_validation->run() == FALSE) {
				echo '{ "result": "validation_error", "errors": {"name":"Form must be filled out completely."}}';
				return;
			} else {
		
		
				$billing_data = array(	'id'                => $this->organization_data->id,
				                        'amount'			=> $amount,
				                        'package'           => $package->name,
            							'companyName'		=> $this->organization_data->name,
            							'cardNumber'		=> $this->input->post('ccnumber'),
            							'expirationDate'	=> $this->input->post('card_expires_on_month')."/".$this->input->post('card_expires_on_year'),
										'firstName'			=> $user->row()->first_name,
										'lastName'			=> $user->row()->last_name,
										'email'             => $user->row()->email_address,
                                        'billingFirstName'  => $this->input->post('billingFirstName'),
                                        'billingLastName'   => $this->input->post('billingLastName'),
                                        'billingStreet'     => $this->input->post('billingStreet'),
                                        'billingStreet2'    => $this->input->post('billingStreet2'),
                                        'billingCity'       => $this->input->post('billingCity'),
                                        'billingState'      => $this->input->post('billingState'),
                                        'billingZip'        => $this->input->post('billingZip')	
								
				);
				$subscription = $this->billingsubscription->create_subscription($billing_data);
				
		
				if($subscription['success']) {
					$this->organization->set_subscription_id($organization->row()->id, $this->user_data['id'], $subscription['subscription_id'], "subscription id set to ".$subscription['subscription_id']);
					$this->organization->set_package($this->user_data['organization_id'], $this->user_data['id'], $package->id, $this->session->userdata('status').' from '.$organization_package->name.' to '.$package->name);

				
				    $this->session->set_flashdata('success', 'You have successfully upgraded your package.');
				    
                    $this->affiliates_model->process_date_change($this->organization_data->id);
				    
                    echo '{ "result": "successful_redirect", "url": "'.site_url().'account"}';
					return;
					
					
				} else {									

    				if( $subscription['message'] !== NULL ) {
                        /*We have an error back from the billing api, lets tell the user */
    					$billing_error = $subscription['message'];
    				} else {
    					$billing_error = 'An unexpected error occurred when processing your account. Please try again in a few hours.';
    				}

					$this->logger->log_message(array('type' => 'error', 'message' => 'creating a subscription when upgrading from a free to paid account - '.$billing_error));

					echo '{ "result": "validation_error", "errors": {"name":"'.$billing_error.'"}}';
					return;
				}
				
				$this->session->unset_userdata('upgrade_package');
				return;
			
			}
		
		
		}

		//----------------------------------------------------------------
		//if were here, were only going to update or cancel a subscription.	
		//----------------------------------------------------------------
		
		$billing_data =	array(	'subscription_id'	=> $organization->row()->subscription_id,
								'amount'			=> $amount
		);
			
		$this->load->view('header', $data);
				
		$this->organization->set_package($this->user_data['organization_id'], $this->user_data['id'], $package->id, $this->session->userdata('status').' from '.$organization_package->name.' to '.$package->name);
								
		// If price =0, then we need to cancel their subscription			
		if($package->price == "0.00") {
			if($this->billingsubscription->cancel_subscription($billing_data)) {
			
				$this->organization->set_subscription_id($this->user_data['organization_id'], $this->user_data['id'], 0, "subscription id set to 0");
				$this->load->view('account/upgrade/complete_downgrade_free', $data);
				$this->db->insert('logs', array('date' => time(), 'organization_id' => $this->user_data['organization_id'], 'user_id' => $this->user_data['id'], 'type' => 'billing', 'action' => 'subscription canceled because package price 0'.$package->price));
							
			} else {
				echo 'problem canceling subscription.';	
				$this->logger->log_message(array('type' => 'error', 'message' => 'problem canceling subscription '.$organization->row()->subscription_id));
			}
		
		}else {
			if( $this->billingsubscription->update_subscription($billing_data) ) {
				$this->organization->set_package($this->user_data['organization_id'], $this->user_data['id'], $package->id, $this->session->userdata('status').' from '.$organization_package->name.' to '.$package->name);
				$this->db->insert('logs', array('date' => time(), 'organization_id' => $this->user_data['organization_id'], 'user_id' => $this->user_data['id'], 'type' => 'billing', 'action' => 'billing amount updated to '.$package->price));
				
                $this->affiliates_model->process_date_change($this->organization_data->id);
				
				if($this->session->userdata('status') == 'downgrade'){			
					$this->load->view('account/upgrade/complete_downgrade', $data);
					
				} else {
					$this->load->view('account/upgrade/complete_upgrade', $data);
				}
							
	
			} else {
				echo 'There was a problem updating your account. Your issue has been reported automatically.';
				$this->logger->log_message(array('type' => 'error', 'message' => 'problem updating subscription'));
				return;
			}
		
		}		
				
		$this->session->unset_userdata('upgrade_package');
		$this->load->view('footer');
				
	}
}
