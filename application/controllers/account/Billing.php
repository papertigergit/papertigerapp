<?php

class Billing extends CI_Controller {


	function __construct() {
		parent::__construct();
		include APPPATH.'helpers/mcapiclass_helper.php';  
	}

    function index() {
        $this->update();
    }
	
	function create() {
		
		//DEBUG
		// if( $this->organization_data->subscription_id != "0") {
		// 	redirect('/');
		// 	return;
		// }
		// if($this->uri->segment(4) == '') {
		// 	redirect('/');
		// 	return;
		// }

		$packages_by_name=$this->Package_model->get_by_name($this->uri->segment(4));
		if($packages_by_name->num_rows() == 0) {
			redirect('/');
			return;
		}
		
		$this->authentication->require_billing_authentication('admin');
		$this->load->model('package_model', 'package');
		
		// End authentication check
		
		$this->load->helper('ccform');	
		$this->load->library('form_validation');
		$this->load->library('billingsubscription');
		$this->load->model('organization_model', 'organization');
      		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
	
	    $data = array(
	            'user_data'       	=>  $this->user_data,
	      	    'packages' => $this->Package_model->get_by_id($package_id),
		    'packages_by_name' => $packages_by_name,
		    'is_network' => $this->Network_model->is_network($package_id),
		    'organization_admin' => $this->Permissions_model->get_organization_admin($this->user_data['organization_id']),
	            'organization'		=> $this->organization_data,
		    'selected_plan'		=> $this->uri->segment(4),
	            'body_id'       	=>  'account',	
	    );
	
		if($this->input->post('ccnumber') !== FALSE) {
			$this->form_validation->set_rules('ccnumber', 'Credit Card Number', 'trim|required');
			$this->form_validation->set_rules('card_expires_on_month', 'Credit Card Expiration Month', 'trim|required');
			$this->form_validation->set_rules('card_expires_on_year', 'Credit Card Expiration Year', 'trim|required|exact_length[4]');
            $this->form_validation->set_rules('card_cvv', 'Credit Card Security Code', 'trim|required|numeric');
			
			//Billing Address Stuff
			$this->form_validation->set_rules('billingFirstName', 'First Name', 'trim|required');
			$this->form_validation->set_rules('billingLastName', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('billingStreet', 'Street', 'trim|required');
			$this->form_validation->set_rules('billingStreet2', 'Extended Street', 'trim');
			$this->form_validation->set_rules('billingCity', 'City', 'trim|required');
			$this->form_validation->set_rules('billingState', 'State', 'trim|required');
			$this->form_validation->set_rules('billingZip', 'Zip', 'trim|required');			
			$this->form_validation->set_rules('subscriptionType', 'Billing Type', 'required');
			
			if($this->form_validation->run() !== FALSE) {
			
                //Validate the package name
        		$package = $this->package->get_package($this->input->post('subscriptionType'), 'name');
        		if(!$package) {
        			redirect('account/billing/create');
        			return;
        		}
			
		      	//add them to the ARB   
				$billing_data = array(  
					'id'        		=> $this->organization_data->id,
					'package'           => $package->name,
					'companyName'   	=> $this->organization_data->name,
					'cardNumber'    	=> $this->input->post('ccnumber'),
					'cardCVV'           => $this->input->post('card_cvv'),
					'expirationDate'  	=> $this->input->post('card_expires_on_month')."/".$this->input->post('card_expires_on_year'),
					'firstName'	     	=> $this->user_data['first_name'],
					'lastName'  	    => $this->user_data['last_name'],
					'email'       		=> $this->user_data['email_address'],
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
	
					$this->organization->set_subscription_id($this->organization_data->id, $this->user_data['id'], $subscription['subscription_id'], "subscription id set to ".$subscription['subscription_id']);
					
					$this->organization->set_package($this->organization_data->id, $this->user_data['id'], $package->id, 'created billing subscription and upgraded from free to paid '.$package->name);
						//mail chimp integration
							$api_key='1ddf218cb55d3d51f6be533b72fc81ae-us1';
							$listId = "a31d451a83";
							$api = new MCAPI($api_key);

							$my_email=$this->user_data['email_address'];
							$retval = $api->listUnsubscribe( $listId,$my_email,false,false,false);

							$fname=$this->user_data['first_name'];
							$lname=$this->user_data['last_name'];
							$company=$this->organization_data->name;
							$groupings = array (
									'Contact Type' => array (
									'name' => 'Contact Type',
									'groups' => $groupings=$package->mailing_list,
										)
									);
							
							$time_now=time();
							$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'COMPA'=>$company, 'DPRCH'=>$time_now);
							$merge_vars['GROUPINGS'] = $groupings;
							$retval = $api->listSubscribe( $listId, $my_email, $merge_vars,'html', 'false', 'false' );

						/* email_to_admin */
						$billing_data['package']=$this->Package_model->get_by_id($package->id)->row()->plan_description;
						$billing_data['price']=$this->Package_model->get_by_id($package->id)->row()->price;
						$billing_data['first_name']=$this->user_data['first_name'];
						$billing_data['last_name']=$this->user_data['last_name'];
						$billing_data['company_name']=$this->organization_data->name;
						$this->load->library('postmark');
						$this->postmark->to('frontdesk@thepapertiger.com');
						$this->postmark->subject('Account Upgrade from Free Created '.time());
						$this->postmark->message_html($this->load->view('email_templates/admin_account_created', $billing_data, true));
						$this->postmark->send();			
						/* end email */

	
				    //full page
				    $this->load->view('header', $data);
			        $this->load->view('account/billing/create_success');
			        $this->load->view('footer');  
	
				} else {
				
			        if( $subscription['message'] !== NULL ) {
			/*          We have an error back from the billing api, lets tell the user */
			          $vdata['billing_error'] = $subscription['message'];
			        } else {
					  redirect('/account');
					  $this->session->set_flashdata('error', 'An unexpected error occurred when processing your account. Please try again in a few hours.');
			          //$vdata['billing_error'] = 'An unexpected error occurred when processing your account. Please try again in a few hours.';
			        }
					
				    //full page
				    // $this->load->view('header', $data);
				    // 			        $this->load->view('account/billing/create', $vdata);
				    // 			        $this->load->view('footer');  
					
					
					$current_form = 'account/billing/create/'.$this->uri->segment(4);
					$this->session->set_flashdata('validation_errors', validation_errors());
					redirect($current_form);
				}
			
			} else {
			    //full page
		 	$this->load->view('header', $data);
		        $this->load->view('account/billing/create',$data);
		        //redirect('account/billing/create/'.$this->input->post('selected_plan'));
		        $this->load->view('footer');  
			}
				
		} else {
		    //full page
		    $this->load->view('header', $data);
	        $this->load->view('account/billing/create',$data);
	        $this->load->view('footer');  
		}		
	}

	function update() {
	
		$this->authentication->require_authentication('admin');
	
		if( $this->organization_data->subscription_id == "0") {
			redirect('/');
			return;
		}
	
		$this->load->helper('ccform');	
		
		// let's load up Braintreezle
		require_once('./vendor/braintree/braintree_php/lib/Braintree.php');
		$this->config->load('braintree');
				
		$gateway = new Braintree\Gateway([
    		    'environment' => $this->config->item('environment'),
    		    'merchantId' => $this->config->item('merchantId'),
    		    'publicKey' => $this->config->item('publicKey'),
    		    'privateKey' => $this->config->item('privateKey')
		    ]);

		
		if($this->input->post('ccnumber') !== NULL) {
		
			$this->load->library(array('form_validation'));
			$this->form_validation->set_error_delimiters("<span class='alert new_alert'>","</span>");
			$this->form_validation->set_rules('ccnumber', 'Credit Card', 'trim|required');
			$this->form_validation->set_rules('cardCVV', 'Credit Card CVV', 'trim|required');
//No custom validation function with this name callback_cc_check (cc_check) for credit card. Commented after upgrade.
			$this->form_validation->set_rules('card_expires_on_month', 'Expiration Month', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('card_expires_on_year', 'Expiration Year', 'trim|required|is_natural_no_zero');
        	$this->form_validation->set_rules('billingFirstName', 'First Name', 'trim|required');
        	$this->form_validation->set_rules('billingLastName', 'Last Name', 'trim|required');
        	$this->form_validation->set_rules('billingStreet', 'Street', 'trim|required');
        	$this->form_validation->set_rules('billingStreet2', 'Extended Street', 'trim');
        	$this->form_validation->set_rules('billingCity', 'City', 'trim|required');
        	$this->form_validation->set_rules('billingState', 'State', 'trim|required');
        	$this->form_validation->set_rules('billingZip', 'Zip', 'trim|required');
			if ($this->form_validation->run() == FALSE) {

/* 				echo '{ "result": "validation_error", "errors": {"ccnumber":"Invalid credit card information."}}'; */

				if(form_error('ccnumber')!=''){$errors[]='"ccnumber":"'.form_error('ccnumber').'"';}
				if(form_error('cardCVV')!=''){$errors[]='"cardCVV":"'.form_error('cardCVV').'"';}
				if(form_error('card_expires_on_month')!=''){$errors[]='"card_expires_on_month":"'.form_error('card_expires_on_month').'"';}
				if(form_error('card_expires_on_year')!=''){$errors[]='"card_expires_on_year":"'.form_error('card_expires_on_year').'"';}
				if(form_error('card_expires_on_month')!=''){$errors[]='"card_expires_on_month":"'.form_error('card_expires_on_month').'"';}
				if(form_error('billingFirstName')!=''){$errors[]='"billingFirstName":"'.form_error('billingFirstName').'"';}
				if(form_error('billingLastName')!=''){$errors[]='"billingLastName":"'.form_error('billingLastName').'"';}
				if(form_error('billingStreet')!=''){$errors[]='"billingStreet":"'.form_error('billingStreet').'"';}
				if(form_error('billingStreet2')!=''){$errors[]='"billingStreet2":"'.form_error('billingStreet2').'"';}
				if(form_error('billingCity')!=''){$errors[]='"billingCity":"'.form_error('billingCity').'"';}
				if(form_error('billingState')!=''){$errors[]='"billingState":"'.form_error('billingState').'"';}
				if(form_error('billingZip')!=''){$errors[]='"billingZip":"'.form_error('billingZip').'"';}
				$errors = implode(",", $errors);
				echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
                return;

			} else {
				
				//we know this isn't an error cause we ran a check for it already
				$result = $gateway->creditCard()->create(array(
				    'customerId' => $this->organization_data->id,
				    'number' => $this->input->post('ccnumber'),
					'cvv' => $this->input->post('cardCVV'),
				    'expirationDate' => $this->input->post('card_expires_on_month').'/'.$this->input->post('card_expires_on_year'),
        		    'billingAddress' => array(
                        'firstName'         => $this->input->post('billingFirstName'),
                        'lastName'          => $this->input->post('billingLastName'),
                        'streetAddress'     => $this->input->post('billingStreet'),
                        'extendedAddress'   => $this->input->post('billingStreet2'),
                        'locality'          => $this->input->post('billingCity'),
                        'region'            => $this->input->post('billingState'),
                        'postalCode'        => $this->input->post('billingZip')
        		    ),
                    'options' => array(
                        'makeDefault' => true
                    )
				));
												
				
				
				if( $result->success ) {
					$creditCard = $result->creditCard;
					//Updating with Billing API worked
				
    				$result = $gateway->subscription()->update($this->organization_data->subscription_id, array(
    					'paymentMethodToken' => $creditCard->token				
    				));
								
					$this->session->set_flashdata('success', 'Billing information successfully updated');			
					echo '{ "result": "successful_redirect", "url": "'.site_url().'account"}';
				
				} else {
					//Updating with Billing API failed
/* 					echo '{ "result": "validation_error", "errors": {"ccnumber":"Billing information could not be updated."}}'; */
					echo '{ "result": "validation_error", "errors": {"ccnumber":"'.$result->errors->errors->nested['creditCard']->errors[0]->message.'"}}';

				
				}
				
			
			}
			
			
		} else {
			$this->load->view('account/billing/update');
		}
	}
	
	function subscription_check($str) {
	   if( in_array($str, array('Monthly','Yearly','Basic','BasicYearly','ProYearlySpecial')) ) {
	       return TRUE;
	   } else {
			$this->session->set_flashdata('error', 'Please select a valid plan!');
			redirect('/account');
	       return FALSE;
	   }
	}
	
}


?>
