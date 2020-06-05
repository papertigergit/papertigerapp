<?php
    class Labels extends CI_Controller{
  
    function __construct(){
      parent::__construct();
      global $user_data;
      
      $this->load->library(array('session', 'authentication'));
      $this->load->helper('url');
      $this->load->helper('empty_messages');
      $this->load->database();
      $this->load->model('Reminders_model');
      $this->load->model('Database_model');
      $this->load->model('Category_model');
      $this->load->model('Location_model');
      $this->load->model('Label_settings_model');
      $this->load->model('Item_model');
      $user_data = $this->session->userdata('user_data');
    }
    
    function index(){
      $this->authentication->require_authentication();
      global $user_data;
	$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
	$is_network=$this->Network_model->is_network($package_id);
	$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
	if($is_network->num_rows() == 0):
	      $data['locations'] = $this->Location_model->get_from_database();
	endif;      
	if($is_network->num_rows() > 0):
	      $data['locations'] = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));				
	endif;      
	if($is_network->num_rows() > 0 && $organization_admin ==$this->user_data['id']):
	      $data['locations'] = $this->Location_model->get_from_database();
	endif;      
            $database_id=$this->session->userdata('database_id');

    $network_utils = array();
    $network_utils = enable_buttons();
    $data['body_id'] = 'labels';
                                    $data['admin_email']= $network_utils['admin_email'];
                                    $data['admin_name'] = $network_utils['admin_name'];
      $data['user_data'] = $user_data;
      $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
      $data['packages'] = $this->Package_model->get_by_id($package_id);
      $this->load->view('header', $data);
      $this->load->view('labels/select',$data);
      $this->load->view(empty_message_view($database_id), $data); //This loads the corresponding empty views!
      $this->load->view('footer',$data);
      //pdf_create($html, 'labels');
    }
    	
    function generate(){
		global $user_data;
		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
		
		if($is_network->num_rows() == 0):
			$locations = $this->Location_model->get_from_database()->result();
		endif;  
		
		if($is_network->num_rows() > 0):
			$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'))->result();
		endif;    
		
		if($is_network->num_rows() > 0 && $organization_admin ==$this->user_data['id']):
			$locations = $this->Location_model->get_from_database()->result();
		endif;      

		$labels_id = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']))->row()->labels;
		
		if($label_type=$this->input->post('label_type') != 1000 && $labels_id == 0)
		{     
			$this->session->set_flashdata('error', 'Upgrade to Pro account to print these labels.');
			redirect('/labels/');
		}


		$label_type=$this->input->post('label_type');
		$settings =$this->Label_settings_model->get_all($label_type); // retrieving the label settings from the database

		$size = 0;
		$this->load->library('pdf');

		$this->pdf->setPrintHeader(false);
		$this->pdf->setPrintFooter(false);

		if($settings->num_rows<1)
		{
			//proper error handling for reports
			echo "Error .. Too Few Rows!!";
		}

		//set margins
		foreach ($settings->result() as $item) {
			$this->pdf->SetMargins($item->left_margin,$item->top_margin,$item->right_margin); 
			// set font
			$this->pdf->SetFont('times', '', $item->font_size);
		}

		$this->pdf->SetAutoPageBreak(TRUE, '2');
		$html = '';
		$data['locations'] = $locations;
		$data['print_location_name']=$this->input->post('print_location_name');
		$data['print_item_name']=$this->input->post('print_item_name');
		// TEMPORARY SET WILL CHANGE AFTER APPROVAL OF PRINT LABEL REPORT
		$data['print_location_name'] ='on';
		$data['print_item_name'] = 'on';


		//print_r($_POST);
		foreach($locations as $location){

			$print_name=$this->input->post('p_name_'.$location->id);
			$print_type=$this->input->post('print_label_'.$location->id);

			if($this->input->post($location->id.'_from'))
			{
				$size+=1;
				if($this->input->post($location->id.'_to') <= $location->capacity && $this->input->post($location->id.'_to') > $this->input->post($location->id.'_from')){
					//$data['selected_location'] = $location->id;



					if($print_name==null)
						$data['location_name'] = $location->name;	// used to print the location name already stored in the database
					else
						$data['location_name'] = $print_name;

					$data['print_type'] = $print_type; // Only item should be printed or location,item or location only.
					$data['range_start'] = $this->input->post($location->id.'_from');
					$data['range_end'] = $this->input->post($location->id.'_to');
					$data['settings'] = $settings;
					$data['items']=$this->Item_model->get_for_labels($location->id,$data['range_start'],$data['range_end']);
					$print_string='labels/test_print_'.$label_type;
					/*echo $print_string;
					echo "<pre/>";
					print_r($data);
					die;*/
					$html = $this->load->view($print_string, $data, true);

					// add a page
					$this->pdf->AddPage();

					// print a line using Cell()
					$this->pdf->writeHTML($html, true, false, false, false, '');
				}
				else{
					$this->session->set_flashdata('error', 'There was an error with your request. Please try again.');
					redirect('/labels/');
				}
			}
		}
		ob_end_clean();
		$this->pdf->Output('labels.pdf', 'I');
		//echo $size;
	}

  }

?>
