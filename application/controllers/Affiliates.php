<?php
class Affiliates extends MY_Controller {
	function __construct() {
		parent::__construct();
		
        $this->authentication->require_authentication('admin');
        
        $this->load->model('Category_model');
        $this->load->model('Affiliates_model');
        $this->load->model('Organization_model');
		$this->load->model('package_model', 'package');
    }
    
    function index() {
    
		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);

    
        $data = array(
            'user_data'             => $this->user_data,
            'affiliate_code'        => $this->user_data['affiliate_code'],
            'body_id'               => 'affiliates',
			'packages' 				=> $this->Package_model->get_by_id($package_id)
        );
        
        //If they are an affiliate, we need to show their info, otherwise, show the affiliate
        // signup forms
        
        if($this->user_data['affiliate_code']) {
        
            $data['referrals'] = $this->Affiliates_model->get_referrals($this->user_data['id']);
        
    		$this->load->view('header', $data);
            $this->load->view('affiliates/index');
            $this->load->view('footer');
        
        } else {
        
            $this->load->helper('form');
    		$this->load->library('form_validation');

        	$this->form_validation->set_rules('affiliateFirstName', 'First Name', 'trim|required');
        	$this->form_validation->set_rules('affiliateLastName', 'Last Name', 'trim|required');
        	$this->form_validation->set_rules('affiliateStreet', 'Street', 'trim|required');
        	$this->form_validation->set_rules('affiliateStreet2', 'Extended Street', 'trim');
        	$this->form_validation->set_rules('affiliateCity', 'City', 'trim|required');
        	$this->form_validation->set_rules('affiliateState', 'State', 'trim|required');
        	$this->form_validation->set_rules('affiliateZip', 'Zip', 'trim|required');	
        	$this->form_validation->set_rules('affiliateTax', 'Tax ID', 'trim|required');
        	$this->form_validation->set_rules('affiliatePaypal', 'Tax ID', 'trim|valid_email');
            
    		if ($this->form_validation->run() == FALSE) {
        		$this->load->view('header', $data);
                $this->load->view('affiliates/signup');
                $this->load->view('footer');
    		} else {
    		
                $this->Affiliates_model->process_registration($this->user_data['id'], $this->input->post('affiliateFirstName'), $this->input->post('affiliateLastName'), $this->input->post('affiliateStreet'), $this->input->post('affiliateStreet2'), $this->input->post('affiliateCity'), $this->input->post('affiliateState'), $this->input->post('affiliateZip'), $this->input->post('affiliateTax'), $this->input->post('affiliatePaypal'));
                
                $this->load->library('authentication');
                $this->authentication->rebuild_user_var($this->user_data['id']);
                
                $this->session->set_flashdata('success', 'Your affiliate account has been successfully created!');

                redirect('affiliates');
    		
    		}
        
        }

    }
function request_expert(){
$expert_code = $this->input->post('expert_code'); 
$data = array(
'expert_id' => $expert_code,
'has_expert' => '0'
);
$user_organization = $this->user_data['id'];
$first_name = $this->user_data['first_name'];
$last_name = $this->user_data['last_name'];
$user_email = $this->user_data['email_address'];
$subject = "Paper Tiger Online Expert Approval Request";
$message = ("Please approve" . " " . $first_name . " " . $last_name . " (" . $user_email . ") as a Paper Tiger Expert for publishing in the app.");
$this->load->library('email', $config);
$this->email->set_newline("\r\n");
$this->email->from('leads@thepapertiger.com', 'Paper Tiger Online Leads');
$this->email->to('sales@thepapertiger.com');
$this->email->subject($subject);
$this->email->message($message);
$this->email->send();

redirect('dashboard');

}

function update_expert($user_id, $affiliate_level){
$user_id = $this->input->post('user_id');
$affiliate_level = $this->input->post('affiliate_level');

$data = array(
  'affiliate_level' => $affiliate_level
);

$this->Affiliates_model->update_affiliate_level($data, $user_id);

redirect('dashboard');
}

    function approve_request($user_organization){
    $this->load->helper('form');
    $org_id = $this->user_data['organization_id'];
    $user_expert = $this->Affiliates_model->assign_expert($org_id);
    		$data = array(
			/* 'expert_id' => '1', */
			'expert_id' => $user_expert,
			'has_expert' => '1'
		);
    $user_organization = $org_id;
		$organization_admin = $this->Organization_model->get_org_admin($user_organization);
		$get_admin_user = $this->User_model->get_organization_user($organization_admin, $user_organization); 
		$this->Affiliates_model->set_approved_for_org($data, $user_organization);
		
		redirect('affiliates');
	
	}

    function choose_expert_page(){
	$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
    
        $data = array(
            'user_data'             => $this->user_data,
            'affiliate_code'        => $this->user_data['affiliate_code'],
            'body_id'               => 'affiliates',
			'packages' 				=> $this->Package_model->get_by_id($package_id)
        );
    		$this->load->view('header', $data);
        $this->load->view('affiliates/choose_expert_page');
        $this->load->view('footer');
    }
    function change_expert_page(){
	$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
    
        $data = array(
            'user_data'             => $this->user_data,
            'affiliate_code'        => $this->user_data['affiliate_code'],
            'body_id'               => 'affiliates',
			'packages' 				=> $this->Package_model->get_by_id($package_id)
        );
    		$this->load->view('header', $data);
        $this->load->view('affiliates/change_expert_page');
        $this->load->view('footer');
    }


	function choose_expert(){
    $expert_code = $this->input->post('expert_code'); 
    $data = array(
        'expert_id' => $expert_code,
        'has_expert' => '1'
    );
		$user_organization = $this->user_data['id'];
		$expert_email = $this->Organization_model->get_this_org_expert_email($expert_code);
    $expert_name = $this->Organization_model->get_this_org_expert_name($expert_code);
    $expert_phone = $this->Organization_model->get_this_org_expert_phone($expert_code);
		$first_name = $this->user_data['first_name'];
    $last_name = $this->user_data['last_name'];
		$user_email = $this->user_data['email_address'];
		$expert_subject = $first_name . " has requested your expertise in Paper Tiger Online";
    $user_subject = "Paper Tiger Expert has been notified.";
		$this->Affiliates_model->set_expert_for_org($data, $user_organization);
	  $new_expert_email = $this->Organization_model->get_this_org_expert_email($expert_code);

    $expert_message = "Hi \n " . $first_name . " " . $last_name . " ( " . $user_email . ") would like to request your professional services in Paper Tiger Online.  Please contact this customer via email as soon as possible to schedule your inital 30 minute complimentary consultation with them and to share your professional services package to get them on the right path to getting organized and working more productively.\n\nIf you find that you cannot contact the customer in  timely manner, please let us know as soon as possible at leads@thepapertiger.com\n\nThank you for your continued partnership with us in being a Paper Tiger Expert.  Please let us know if we can do anything for you, or if you have feedback for us.  We appreciate you!\n\nSincerely,\nJanet Baker\nThe Paper Tiger Team\nwww.thepapertiger.com\nPhone:\nUS Toll Free: 866 701-1561, extension 1\nInternational: 404 478-6413, extension 1\nFax:815 331-5270";

    $user_message = "Your chosen expert " . $expert_name . " has been notified, and will contact you as soon as possible via email to schedule your initial phone consultation. In the meantime, feel free to contact your chosen expert directly at " . $expert_email . " and " . $expert_phone . ".\n\nPlease note that the expert may offer an initial complimentary 15-30 minute phone consultation, but you will negotiate with the expert directly for any additional consultation time and services including customizing a personal work-flow solution to help you get your office organized and working more productively, and will be able to help you set up Paper Tiger fast!\n\nThank you for using Paper Tiger!\n\nThe Paper Tiger Team\nwww.thepapertiger.com\nPhone:\nUS Toll Free: 866 701-1561, extension 1\nInternational: 404 478-6413, extension 1\nFax:815 331-5270";

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		
		$this->email->from('leads@thepapertiger.com', 'Paper Tiger Online Leads');
		$this->email->to($new_expert_email);		
		$this->email->subject($expert_subject);		
		$this->email->message($expert_message);
		$this->email->send();

  $this->email->clear();

		$this->email->from('sales@thepapertiger.com', 'Paper Tiger Online Leads');
		$this->email->to($user_email);		
		$this->email->subject($user_subject);		
		$this->email->message($user_message);
		$this->email->send();

		redirect('dashboard');
	
	}
	
	function change_expert(){
	$expert_code = $this->input->post('expert_code'); 
	$data = array(
			'expert_id' => $expert_code,
			'has_expert' => '1'
		);
		$user_organization = $this->user_data['id'];
		$old_expert = $this->organization_data->expert_id;
		$old_email = $this->Organization_model->get_this_chosen_expert_email($old_expert);
		$old_expert_email = $this->Organization_model->get_this_org_expert_email($old_email);
		$first_name = $this->user_data['first_name'];
$last_name = $this->user_data['last_name'];
		$user_email = $this->user_data['email_address'];
		$expert_subject = $first_name . " has requested your expertise in Paper Tiger Online";
    $user_subject = "Paper Tiger Expert has been notified.";
		$change_subject = "Paper Tiger Online Expert Request Withdrawn";
		$change_message = ( $first_name . " " . $last_name . " has withdrawn request for your services." . $first_name ." "  . "has been removed from your approved list"); 

    $expert_message = "Hi \n " . $first_name . " " . $last_name . " ( " . $user_email . ") would like to request your professional services in Paper Tiger Online.  Please contact this customer via email as soon as possible to schedule your inital 30 minute complimentary consultation with them and to share your professional services package to get them on the right path to getting organized and working more productively.\n\nIf you find that you cannot contact the customer in  timely manner, please let us know as soon as possible at leads@thepapertiger.com\n\nThank you for your continued partnership with us in being a Paper Tiger Expert.  Please let us know if we can do anything for you, or if you have feedback for us.  We appreciate you!\n\nSincerely,\nJanet Baker\nThe Paper Tiger Team\nwww.thepapertiger.com\nPhone:\nUS Toll Free: 866 701-1561, extension 1\nInternational: 404 478-6413, extension 1\nFax:815 331-5270";

    $user_message = "Your chosen expert " . $expert_name . " has been notified, and will contact you as soon as possible via email to schedule your initial phone consultation. In the meantime, feel free to contact your chosen expert directly at " . $expert_email . " and " . $expert_phone . ".\n\nPlease note that the expert may offer an initial complimentary 15-30 minute phone consultation, but you will negotiate with the expert directly for any additional consultation time and services including customizing a personal work-flow solution to help you get your office organized and working more productively, and will be able to help you set up Paper Tiger fast and use Paper Tiger more efficiently.\n\nThank you for using Paper Tiger!\n\nThe Paper Tiger Team\nwww.thepapertiger.com\nPhone:\nUS Toll Free: 866 701-1561, extension 1\nInternational: 404 478-6413, extension 1\nFax:815 331-5270";

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");

		$this->email->from('leads@thepapertiger.com', 'Paper Tiger Online Leads');
		$this->email->to($old_expert_email);		
		$this->email->subject($change_subject);		
		$this->email->message($change_message);
		$this->email->send();

  $this->email->clear();

		$this->Affiliates_model->set_expert_for_org($data, $user_organization);
		$new_expert_email = $this->Organization_model->get_this_org_expert_email($expert_code);


		$this->email->from('leads@thepapertiger.com', 'Paper Tiger Online Leads');
		$this->email->to($new_expert_email);		
		$this->email->subject($expert_subject);		
		$this->email->message($expert_message);
		$this->email->send();

  $this->email->clear();
		$this->email->from('leads@thepapertiger.com', 'Paper Tiger Online Leads');
		$this->email->to($user_email);		
		$this->email->subject($user_subject);		
		$this->email->message($user_message);
		$this->email->send();
			redirect('dashboard');
	
	}

	function update_profile() {  
  if(isset($_POST['go'])) { 
    $website = $this->input->post('website');
	  $phone	= $this->input->post('phone_number');
	  $profile = $this->input->post('profile');
	  $city = $this->input->post('city');
	  $state = $this->input->post('state');
    $country = $this->input->post('country');
    $image = $this->input->post('image1');
 
    /* Create the config for upload library */  
    /* (pretty self-explanatory) */  
    $config['upload_path'] = './uploads/'; /* NB! create this dir! */  
    $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';  
    $config['max_size']  = '0';  
    $config['max_width']  = '0';  
    $config['max_height']  = '0';  
    /* Load the upload library */  
    $this->load->library('upload', $config);  
  
    /* Create the config for image library */  
    /* (pretty self-explanatory) */  
    $configThumb = array();  
    $configThumb['image_library'] = 'gd2';  
    $configThumb['source_image'] = '';  
    $configThumb['create_thumb'] = TRUE;  
    $configThumb['maintain_ratio'] = TRUE;  
    /* Set the height and width or thumbs */  
    /* Do not worry - CI is pretty smart in resizing */  
    /* It will create the largest thumb that can fit in those dimensions */  
    /* Thumbs will be saved in same upload dir but with a _thumb suffix */  
    /* e.g. 'image.jpg' thumb would be called 'image_thumb.jpg' */  
    $configThumb['width'] = 140;  
    $configThumb['height'] = 210;  
    /* Load the image library */  
    $this->load->library('image_lib');  
  
    /* We have 5 files to upload 
     * If you want more - change the 6 below as needed 
     */  
    if(isset($image)) {
    
    for($i = 1; $i < 2; $i++) {  
      /* Handle the file upload */  
      $upload = $this->upload->do_upload('image1');  
      /* File failed to upload - continue */  
      if($upload === FALSE) continue;  
      /* Get the data about the file */  
      $data = $this->upload->data();  
  
      $uploadedFiles[$i] = $data;  
      /* If the file is an image - create a thumbnail */  
      if($data['is_image'] == 1) {  
        $configThumb['source_image'] = $data['full_path'];
        $img_path = $data['full_path'];
        $image_parse = explode("/", $img_path);
        $affiliate_photo = "/uploads/" . array_pop($image_parse);  
        $this->image_lib->initialize($configThumb);  
        $this->image_lib->resize();  
      }  
    }  
    }
  }  
    /* And display the form again */  
  
    if($affiliate_photo) {
   $data = array(
			'website'	=> $website,
			'phone_number'=>	$phone,
			'profile'	=>	$profile,
		  'affiliate_photo'	=>	$affiliate_photo,
		  'city'	=>	$city,
      'state'	=>	$state,
      'country' => $country
	);
    }else{
   $data = array(
			'website'	=> $website,
			'phone_number'=>	$phone,
			'profile'	=>	$profile,
		  'city'	=>	$city,
		  'state'	=>	$state,
      'country' => $country
	);
    
    }
	$user_id = $this->user_data['id'];
	$this->Affiliates_model->profile_update($data, $user_id);
	redirect('dashboard');

  } 
}

/* End of file affiliates.php */
/* Location: ./system/application/controllers/affiliates.php */
