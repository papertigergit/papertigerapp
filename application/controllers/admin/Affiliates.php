<?php

/**
     _     ___           _  ____  ___   ___ _   _ _     ___ _  _      _  _ 
 /\ | \|\/| | |\ |   /\ |_ |_ | |  |  /\ | |_  / / \|\ | | |_)/ \| | |_ |_)
/--\|_/|  |_|_| \|  /--\|  | _|_|__|_/--\| |_  \_\_/| \| | | \\_/|_|_|_ | \

!!This is not the controller that nornal users will see. This is a PTO admin script!!
**/


class Affiliates extends MY_Controller {

    function __construct() {
        parent::__construct();
        
        // Restrict this controller to these admin users only
		if( !in_array( $this->user_data['id'], array(38, 1, 3, 1862) ))
		{
			redirect('/');
			return;
		}	
		
        $this->load->model('affiliates_model', 'affiliates');
    }
    
    function index() {
        
		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
        
       $data = array(
            'user_data'             => $this->user_data,
            'body_id'               => 'affiliates',
            'latest_referrals'      => $this->affiliates->get_latest(),
            'pending_payouts'       => $this->affiliates->get_pending_payouts(),
            'latest_payments'       => $this->affiliates->get_latest_payments(),
			'packages' 				=> $this->Package_model->get_by_id($package_id)
        );
        
        
        
		$this->load->view('header', $data);
        $this->load->view('admin/affiliates/index');
        $this->load->view('footer');    
        
        
    }
    
    // Will display a list of all registered affiliates
    function all() {
       $package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
       $data = array(
            'user_data'             => $this->user_data,
            'body_id'               => 'affiliates',
			'packages' 				=> $this->Package_model->get_by_id($package_id),
			'affiliates'			=> $this->affiliates->list_all()
        );
    
        $this->load->view('header', $data);
        $this->load->view('admin/affiliates/all');
        $this->load->view('footer');  
    }
    
    function edit() {
    
    	$affiliate_code = $this->uri->segment(4);
    	
        $affiliate = $this->User_model->get_user($affiliate_code, 'affiliate_code');
        
        $affiliate_level = $this->affiliates->get_level($affiliate->row()->affiliate_level)->row();

		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		
        if(!$affiliate || $affiliate->num_rows() == 0) {
            redirect('/admin/affiliates');
        }

        $this->load->library('session');
        $this->load->helper('form');

        
		if($this->input->post('affiliate_first_name') !== NULL) {
		
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('affiliate_first_name', 'First Name', 'required|max_length[12]|trim');
			$this->form_validation->set_rules('affiliate_last_name', 'Last Name', 'required|max_length[12]|trim');
			$this->form_validation->set_rules('affiliate_paypal', 'Email', 'max_length[60|trim|valid_email');
			
			if ($this->form_validation->run() == FALSE)
			{
				//Display error message with form
		       $data = array(
		            'user_data'             => $this->user_data,
		            'body_id'               => 'affiliates',
		            'affiliate_code'        => $this->uri->segment(4),
		            'affiliate'				=> $affiliate->row(),
					'packages' 				=> $this->Package_model->get_by_id($package_id)
		        );
		        
		        $this->session->set_userdata('affiliate_payout', $data);
		        
		        $this->load->view('header', $data);
		        $this->load->view('admin/affiliates/edit');
		        $this->load->view('footer');  
			
			} else {
			
				//Run the database calls for updating
				$this->affiliates->modify(
					$affiliate_code, 
					$this->input->post('affiliate_first_name'),
					$this->input->post('affiliate_last_name'),
					$this->input->post('affiliate_street'),
					$this->input->post('affiliate_street_2'),
					$this->input->post('affiliate_city'),
					$this->input->post('affiliate_state'),
					$this->input->post('affiliate_zip'),
					$this->input->post('affiliate_tax'),
					$this->input->post('affiliate_level'),
					$this->input->post('affiliate_paypal')
				);
				
				$this->session->set_flashdata('success','Affiliate information saved successfully.');
				redirect('/admin/affiliates/edit/'.$affiliate_code);
				
			}
		
		} else {
		
	       $data = array(
	            'user_data'             => $this->user_data,
	            'body_id'               => 'affiliates',
	            'affiliate_code'        => $this->uri->segment(4),
	            'affiliate'				=> $affiliate->row(),
				'packages' 				=> $this->Package_model->get_by_id($package_id)
	        );
	        
	        $this->session->set_userdata('affiliate_payout', $data);
	        
	        $this->load->view('header', $data);
	        $this->load->view('admin/affiliates/edit');
	        $this->load->view('footer');  
		
		}  
    }
    
    //Process a manual referral entry
    function manualentry() {
    
    	if($this->input->post('affiliate_code') === NULL) {
    		redirect('/admin/affiliates/');
    		return;
    	}
    	
    	$affiliate_code = $this->input->post('affiliate_code');
    	
		$this->load->library('form_validation');
			
		$this->form_validation->set_rules('referral_organization_id', 'Organization', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('referral_price', 'Price', 'required|trim');
		$this->form_validation->set_rules('referral_date', 'Date', 'required|callback_date_check');
			
		if ($this->form_validation->run() == FALSE) {	
				$this->session->set_flashdata('error','Referral was not saved. Validation Error!');
				redirect('/admin/affiliates/view/'.$affiliate_code);
		} else {
			$this->affiliates->manual_entry(
					$this->input->post('affiliate_code'),
					$this->input->post('referral_organization_id'),
					strtotime($this->input->post('referral_date')),
					$this->input->post('referral_price')
				);
				
				$this->session->set_flashdata('success','Referral saved successfully.');
				redirect('/admin/affiliates/view/'.$affiliate_code);
		}
    
    }
    
    
    function view() {
// let's load up Braintreezle
		require_once('./vendor/braintree/braintree_php/lib/Braintree.php');
		$this->config->load('braintree');
				
		$gateway = new Braintree\Gateway([
                    'environment' => $this->config->item('environment'),
                    'merchantId' => $this->config->item('merchantId'),
                    'publicKey' => $this->config->item('publicKey'),
                    'privateKey' => $this->config->item('privateKey')
                ]);
 
        $affiliate_code = $this->uri->segment(4);
        
        $affiliate = $this->User_model->get_user($affiliate_code, 'affiliate_code');
        
        $affiliate_level = $this->affiliates->get_level($affiliate->row()->affiliate_level)->row();

		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);

        if(!$affiliate_code || $affiliate->num_rows() == 0) {
            redirect('/admin/affiliates');
        }

        $this->load->library('session');
        $this->load->helper('form');
        
        $payouts    = $this->affiliates->get_affiliate_pending_payouts($this->uri->segment(4));
        
        //All annual amounts will be added
        $total_annual = 0;
        $total_payout = 0;
	$total_paid_out = 0;

		// The foreach should probably be moved to the model at some point so it can be re-used.
        $i = 0;
        $new = array();
        foreach($payouts->result() as $p) {
        
            $new[$i]['tracking_id'] = $p->tracking_id;
            $new[$i]['organization_id'] = $p->organization_id;
            $new[$i]['organization_name'] = $p->organization_name;
            $new[$i]['original_date'] = $p->original_date;
            $new[$i]['tier'] = $p->tier;
            
            // Are we calculating the amount or using a manual entry?
            if((int)$p->manual_payment_amount == 0) {
            	$new[$i]['annual'] = ($p->billing_cycle == 'month') ? $p->price * 12 : $p->price;
            } else {
				$new[$i]['annual'] = $p->manual_payment_amount;            
            }
           	$total_annual += $new[$i]['annual'];
            $total_paid_out = ($total_annual * .3);
            
            //tier 2 is at 10%, otherwise use the affiliate's level percentage.
            $new[$i]['payout_amount'] = ($p->tier == 0) ? ($affiliate_level->percentage * $new[$i]['annual']) : ($new[$i]['annual'] * .1);
            $total_payout = $new[$i]['payout_amount'];
            
            $i++;
        }

       $data = array(
            'user_data'             => $this->user_data,
            'body_id'               => 'affiliates',
            'affiliate_code'        => $this->uri->segment(4),
            'affiliate'				=> $affiliate->row(),
            'pending_payouts'       => $payouts,
            'new'                   => $new,
            'total_annual'          => $total_annual,
            'payout_amount'         => $total_payout,
            'total_paid'            => $total_paid_out,
            'payout_percent'        => $affiliate_level->percentage,
            'referrals'             => $this->affiliates->get_all($affiliate_code),
			'packages' 				=> $this->Package_model->get_by_id($package_id)
        );
        
        $this->session->set_userdata('affiliate_payout', $data);
        
        $this->load->view('header', $data);
        $this->load->view('admin/affiliates/view');
        $this->load->view('footer');   
    
    }
    
    function markaspaid() {
    
        $affiliate_code = $this->input->post('affiliate_code');
        
        if(!$affiliate_code) {
            redirect('admin/affiliates');
        }
    
        $this->load->library('session');

        $affiliate = $this->User_model->get_user($affiliate_code, 'affiliate_code');
        
        $affiliate_level = $this->affiliates->get_level($affiliate->row()->affiliate_level)->row();

        $payout_data = $this->session->userdata('affiliate_payout');


        $this->session->unset_userdata('affiliate_payout');
        
        $this->db->trans_start();
    
        $this->affiliates->create_payment($payout_data['affiliate_code'], $payout_data['payout_amount']);
        
        $limit = sizeof($payout_data['new']);
        for($i=0; $i<$limit; $i++) {
            $this->affiliates->mark_as_paid($payout_data['new'][$i]['tracking_id'], $affiliate_level->recurring);
        }
        
        $this->db->trans_complete();
        
        $this->session->set_flashdata('success', 'Affiliate has been successfully marked as paid.');
        
        redirect('admin/affiliates/view/'.$payout_data['affiliate_code']);
    }
    
    function date_check($str) {
    	$date = explode('/',$str);
    	if( checkdate($date[0],$date[1],$date[2]) ) {
    		return TRUE;	
    	} else {
	    	$this->form_validation->set_message('date_check', 'The %s field must be a valid date.');
    		return FALSE;
    	}
    }
    function change_sale_price(){
      $this->affiliates->update_sale_price();
      redirect('admin/affiliates/index/');
    }

    function change_normal_price(){
      $this->affiliates->update_normal_price();
      redirect('admin/affiliates/index/');
    }
}
