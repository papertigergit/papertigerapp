<?php
class Locations extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'empty_messages','access_helper'));
		$this->load->model(array('Location_model', 'Item_model', 'Category_model', 'Transfers_model', 'Permissions_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
		if($this->session->userdata('database_id')) {
			database_exists($this->session->userdata('database_id'));
		}
	}

	function modify_capacity($location_id=NULL){
		location_exists($location_id);

		if($this->authentication->is_authenticated())
		{
		global $user_data;
		$packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']));
		$package=(array($packages->result()));
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		$loc_permissions=$this->Permissions_model->get_by_location($location_id,$user_data['id']);
		$can_edit=false;
		if($loc_permissions) {
		if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3) {
			$can_edit=true;
		  }
		}
		if($organization_admin == $this->user_data['id']) {
			$can_edit=true;
		}
		
		$this_location = $this->Location_model->get_by_id($location_id);
		if($this->input->post('modify_location_capacity') !== NULL) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('new_capacity', 'Capacity', 'trim|required|is_natural_no_zero');
			
			if ($this->form_validation->run() == FALSE) {
				echo '{ "result": "validation_error", "errors": {"capacity":"'.form_error('new_capacity').'"}}';
			} else 	if(($this_location->capacity+$this->input->post('new_capacity')) > $package[0][0]->max_items && $package[0][0]->id == 1) {
				echo '{ "result": "validation_error", "errors": {"new_capacity":"Upgrade to Paper Tiger Pro to have more 100 items per location!"}}';
			} else if($can_edit == false) {
				echo '{ "result": "validation_error", "errors": {"new_capacity":"You do not have access to edit this location!"}}';
			}
			else {
				//echo ($this_location->capacity+$this->input->post('new_capacity'));
				$this->Location_model->update_capacity($location_id);
				echo '{ "result": "successful_input", "grid_callback": "/locations/location_grid/'.$location_id.'", "message_type": "success", "message": "Location capacity adjusted successfully."}';				
			}
		} else {
			// ajax view
			$data = array('this_location'=>$this_location);
			$this->load->view('modify_location_capacity', $data);
		}
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
	
	function edit(){
		if($this->authentication->is_authenticated())
		{
		header("Cache-Control: no-cache, must-revalidate");
		$location_id = $this->uri->segment(3);
		location_exists($location_id);

		global $user_data;
		$loc_permissions=$this->Permissions_model->get_by_location($location_id,$user_data['id']);
		$can_edit=false;
		if($loc_permissions) {
		if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3) {
			$can_edit=true;
		  }
		}
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
		if($organization_admin == $this->user_data['id']) {
			$can_edit=true; 
		}



		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		if($is_network->num_rows() == 0):
			$database_permissions=3;
			$can_edit="true";
		endif;


		if($can_edit==false){
			$this->load->view('facebox_error',array('error'=>'You do not have access to edit this location.'));
			return;
		}


		if(!$location_id){
			$this->load->view('facebox_error',array('error'=>'Please select a Location first.'));
			return;
		}
		
		if(!$location_id){
			$this->load->view('facebox_error',array('error'=>'Please select a Location first.'));
			return;
		}

		global $user_data;

		// lets make sure this location is owned by the user		
		$location = $this->Location_model->get_by_id($location_id, $this->user_data['organization_id']);

		//lets make sure we got a valid location to work with.
		if( !isset($location)) {
			$this->load->view('facebox_error',array('error'=>'Please select a location from your locations page.'));
			return;
		}

		
		if($this->input->post('edit_location') !== NULL) {
			$this_location = $location;
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			$this->form_validation->set_rules('capacity', 'Capacity', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('review_num', 'Review Frequency', 'trim|is_natural_no_zero');

			$interval_frequency="Specific Date...";
			if($this->input->post('interval_frequency'))
			{
				$interval_frequency=$this->input->post('interval_frequency');
			}

			$packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']));
			$package=(array($packages->result()));


			$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			$is_network = $this->Network_model->is_network($package_id);
			if($is_network->num_rows() == 0):
				$locations=$this->Location_model->get_from_database();
			endif;
			if($is_network->num_rows() > 0):
				$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
			endif;
			if($is_network->num_rows() > 0 && $organization_admin==$this->user_data['id']):
				$locations=$this->Location_model->get_from_database();
			endif;

			$num_of_locs=$locations->num_rows();

			if($package[0][0]->id == 1)
			{
				if($this->input->post('capacity') > $package[0][0]->max_items)
				{
					echo '{ "result": "validation_error", "errors": {"capacity":"<p>Upgrade to Paper Tiger Pro to have more 100 items per location!</p>"}}';
					return;
				}

			}

      if(($this->input->post('review_interval') > -1) &&  ($interval_frequency != "Specific Date..."))
      {   
        echo '{ "result": "validation_error", "errors": {"review_interval":"<p>Choose either review frequency or specific date. Not both values</p>"}}';
        return;
      }  

			$last_edit_user_id=$this->Location_model->get_by_id($location_id)->last_edited_by;
			$last_edit_db_time=$this->Location_model->get_by_id($location_id)->last_edited_time;

			if($this->input->post('edit_time') < $last_edit_db_time) {
				$last_user_edit=$this->User_model->get_user($last_edit_user_id)->row(); 
				$f_name=$last_user_edit->first_name;
				$l_name=$last_user_edit->last_name;
				$db_edit_time=unix_to_human($last_edit_db_time);

				$validation_name=$this->Location_model->get_by_id($location_id)->name;
				//echo '{ "validation_name": "validation_error", "errors": {"name":"'.$validation_name.'"}}';
				$error_text="While you were editing this location, $f_name $l_name edited it and saved the changes be changes before you did. Please refresh this page and try again!";
				echo '{ "result": "validation_error", "errors": {"errors":"'.$error_text.'"}}';
				return;
			}
			
			$will_truncate_data = $this->input->post('capacity') < $this->Location_model->getLastItem($location_id)->slot_num;
			
			if ($this->form_validation->run() == FALSE) {
				if(form_error('name')!=''){$errors[]='"name":"'.form_error('name').'"';}
				if(form_error('capacity')!=''){$errors[]='"capacity":"'.form_error('capacity').'"';}
				if(form_error('review_num')!=''){$errors[]='"review_num":"'.form_error('review_num').'"';}
				
				$errors = implode(",", $errors);
				echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
			}  else if($will_truncate_data){
				echo '{ "result": "validation_error", "errors": {"capacity":"Decreasing the capacity to this amount would truncate items<br>and result in data loss.<br> First, move some items out of this location."}}';
			} else {
				$modified_rows = $this->Location_model->update($location_id);
				$this->Logging_model->locations(1,$modified_rows);
				$insert_id=$this->Location_model->log_modifications($location_id,$this->user_data['id']);
				$this->session->set_flashdata('modified_rows', $modified_rows);
				
				echo '{ "result": "successful_input", "grid_callback": "/locations/grid/", "message_type": "success", "message": "Location edited successfully."}';
					
			}
		} else {
			// ajax view
			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			$is_network=$this->Network_model->is_network($package_id);
			$data = array('this_location'=>$location,'last_edited_time'=>time(),'is_network'=>$is_network);
			$this->load->view('edit_location', $data);
		}
		
		
		
		/*if($location_id == NULL) {
		//	$this->load->view('facebox_error',array('error'=>'Please select a location first.'));
		} else if($this->input->post('edit_location') !== FALSE) {
			$this_location = $this->Location_model->get_by_id($location_id);
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			$this->form_validation->set_rules('capacity', 'Capacity', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('review_num', 'Review Frequency', 'trim|is_natural_no_zero');
			
			if ($this->form_validation->run() == FALSE) {
				if(form_error('name')!=''){$errors[]='"name":"'.form_error('name').'"';}
				if(form_error('capacity')!=''){$errors[]='"capacity":"'.form_error('capacity').'"';}
				if(form_error('review_num')!=''){$errors[]='"review_num":"'.form_error('review_num').'"';}
				
				$errors = implode(",", $errors);
				echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
			} else if($this->input->post('capacity')>1000){
				echo '{ "result": "validation_error", "errors": {"capacity":"Locations may only hold 1000 items."}}';
			} else if(($this_location->capacity - $this->input->post('capacity'))>($this_location->empty_slots)){
				echo '{ "result": "validation_error", "errors": {"capacity":"You have attempted to decrease the capacity by more than the number of open slots."}}';
			} else {
				$modified_row = $this->Location_model->update($location_id);
				$this->session->set_flashdata('modified_row', $modified_row);
				echo '{ "result": "successful_input", "grid_callback": "/locations/grid/"}';	
			}
		} else {
			// ajax view
			$location = $this->Location_model->get_by_id($location_id);
			$data = array('this_location'=>$location);
			$this->load->view('edit_location', $data);
		}*/
		}
		else
		{
			$this->load->view('session_expired');
		}
		
	}
	
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;
		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);

		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
		if($is_network->num_rows() > 0 && ($organization_admin != $this->user_data['id'])):
			$database_permissions=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id'])->pto_umask;
				if($organization_admin == $this->user_data['id']):
					$database_permissions=3;
				endif;	
		endif;	
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
					$database_permissions=3;
		endif;	
		if($is_network->num_rows() == 0):
			$database_permissions=3;
		endif;	
		//make this if one block
			if($database_permissions == 0) {
				$this->session->set_flashdata('error', 'You do not have access to this database.');
				redirect(site_url());
			} else {
		//make this if one block

		$database_id=$this->session->userdata('database_id');
		if($this->uri->segment('3')):
			$database_id=$this->uri->segment('3');
		endif;	
		if($database_id){
      	$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);

		if($is_network->num_rows() == 0):
			$table_data = $this->Location_model->get_from_database($database_id);				
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin != $this->user_data['id'])):
			$table_data = $this->Location_model->get_from_network_database($database_id);				
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$table_data = $this->Location_model->get_from_database($database_id);				
        endif; 
        $network_utils = array();
        $network_utils = enable_buttons();
        //Check if we want to autoload the add location facebox
        $autoload_add_facebox = false;
        if (($this->uri->segment('4')) && ($this->uri->segment('4') == 'auto')) {
            $autoload_add_facebox = true;
        }
				$data = array(
									'user_data' => $this->user_data, 
									'table_data'=>$table_data, 
									'organization_admin' => $organization_admin,
                                    'body_id' => 'browse',
                                    'buttons_on' => $network_utils['buttons_on'],
                                    'autoload' => $autoload_add_facebox,
                                    'is_network' => $network_utils['is_network'],
                                    'admin_email' => $network_utils['admin_email'],
                                    'admin_name' => $network_utils['admin_name'],
									'modified_rows'=>$this->session->flashdata('modified_rows')
				);
        //Load Views!
        $this->load->view('header', $data);
        $this->load->view('location_grid', $data);
        $this->load->view(empty_message_view($database_id), $data); //This loads the corresponding empty views!
        $this->load->view('footer');
        }
         /*
         * //Load Views!
         * $this->load->view('header', $data);
         * $this->load->view('location_grid', $data);

         * //If 0
         * if (($table_data->num_rows() == 0) && ($db_umask == '1')) {
         *    $this->load->view('empty_messages/no_locations', $data);
         * }

         * elseif (($table_data->num_rows() == 0) && ($db_umask == '0' || '2')) {
         *    $this->load->view('empty_messages/no_permissions', $data);
         * }

         * $this->load->view('footer');
		}

         */        
        else{
			$this->session->set_flashdata('error', 'Please select a database first.');
			redirect(site_url());
		}
		}
	}
	
	function view($database_id=null,$location_id = NULL,$page_number=null,$order_by=null,$order=null){
		location_exists($location_id);
		// $this->authentication->require_authentication();
		if($this->authentication->is_authenticated()) {
			global $user_data;
			$page_number=$this->uri->segment('5');
			if($page_number == null || !$page_number) {
				$page_number = 1;
			}
			if($order_by == null || !$order_by) {
				$order_by="slot_num";
			}
			if($order == null || !$order) {
				$order = "asc";
			}
			$table_data = $this->Item_model->get_from_location($location_id,$page_number,$order_by,$order);
			
			// lets make sure this location is owned by the user		
			$this_location = $this->Location_model->get_by_id($location_id, $this->user_data['organization_id']);
			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);

			//lets make sure we got a valid location to work with.
			if( !isset($this_location)) {
				redirect('locations');
				return;
			}		

			$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
			$is_network = $this->Network_model->is_network($package_id);
			$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
			if($is_network->num_rows() == 0):
				$locations = $this->Location_model->get_from_database();
			endif;	
			if($is_network->num_rows() > 0):
				$locations = $this->Location_model->get_from_network_database($database_id);				
			endif;	
			if($is_network->num_rows() > 0 && $organization_admin == $this->user_data['id']):
				$locations = $this->Location_model->get_from_database();
			endif;	
			$data = array(
								'user_data' => $user_data,
								'organization_admin' => $organization_admin,
								'table_data'=>$table_data,
								'body_id'=>'browse',
								'packages' => $this->Package_model->get_by_id($package_id),
								'this_location'=>$this_location,
								'locations'=>$locations,
								'page_number'=>$page_number,
								'order_by'=>$order_by,
								'order'=>$order,
								'modified_rows'=>$this->session->flashdata('modified_rows')
			);
            $this->session->set_userdata('location_id',$location_id);

      //Only show full view if it's not an AJAX request
      if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $this->load->view('item_grid_by_page_location', $data);
      }
      else {
        $this->load->view('header', $data);
        $this->load->view('item_grid_by_location', $data);
        $this->load->view('footer');
      }        
  } 

    else {
      if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo 'session_expired';
      }
      else {
        redirect('login/logout');
      }        
    }

  }
	
	function grid() {
    global $user_data;
		$this->authentication->require_authentication();

		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		$database_id=$this->session->userdata('database_id');
		if($this->uri->segment('3')):
			$database_id=$this->uri->segment('3');
		endif;	

    //LOGIC TO TURN ON BUTTONS IN VIEW
    $buttons_on = false;
    $can_edit = false;

    if($is_network->num_rows() == 0){
      $table_data = $this->Location_model->get_from_database($this->session->userdata('database_id'));				
      $can_edit="edit_true";
      $buttons_on = true;
    }
    //IF YOU ARE A NETWORK ACCOUNT, ONLY SHOW BUTTONS IF YOU HAVE WRITE ACCESS
        if($is_network->num_rows() > 0){
          $table_data = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));				
          $database_permissions=$this->Permissions_model->get_by_database($database_id,$user_data['id']);
          if (isset($database_permissions)){
            $database_permissions=$this->Permissions_model->get_by_database($database_id,$user_data['id'])->pto_umask;
          }
          if (($database_permissions == '1') || ($database_permissions == '3')) {
            $can_edit="edit_true";
            $buttons_on = true;
          }

        }

    if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])){
      $table_data = $this->Location_model->get_from_database($this->session->userdata('database_id'));				
      $can_edit="edit_true";
      $buttons_on = true;
    }

		$data = array(
							'organization_admin'=>$organization_admin,
							'table_data'=>$table_data,
              'modified_rows'=>$this->session->flashdata('modified_rows'),
              'buttons_on' => $buttons_on,
              'can_edit' => $can_edit,
              'is_network' => $is_network,
              'autoload'=> 'false'
		);
		//ajax view
		$this->load->view('location_grid', $data);
        $this->load->view(empty_message_view($database_id), $data); //This loads the corresponding empty views!
	}
	
	function add() {
		if($this->authentication->is_authenticated())
		{
		$this->authentication->require_authentication();
		global $user_data;
		$loc_permissions=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$user_data['id']);
		$can_edit=false;
		if($loc_permissions) {
		if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3) {
			$can_edit=true;
		  }
		}
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
		if($organization_admin == $this->user_data['id']) {
			$can_edit=true;
		}

		if($can_edit==false){
			$this->load->view('facebox_error',array('error'=>'You do not have sufficient permission to add a location in this database.'));
			return;
		}
		
		if($this->input->post('name') !== NULL) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			$this->form_validation->set_rules('capacity', 'Capacity', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('review_num', 'Review Frequency', 'trim|is_natural_no_zero');
			//date and time interval validation

                if(($this->input->post('review_interval') > -1) &&  ($this->input->post('interval_frequency') != ""))
                {   
                        echo '{ "result": "validation_error", "errors": {"review_interval":"<p>Choose either review frequency or specific date. Not both values</p>"}}';
			return;
                }   
		$packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']));
		$package=(array($packages->result()));
		$locations=$this->Location_model->get_from_database();
		$num_of_locs=$locations->num_rows();
		if($package[0][0]->max_locations <= intval($num_of_locs) && $package[0][0]->max_locations != -1) {
                        echo '{ "result": "validation_error", "errors": {"name":"<p>Upgrade to Paper Tiger Pro to have more than 1 location</p>"}}';
			return;
		}
		if($package[0][0]->id == 1)
		{
			if($this->input->post('capacity') > $package[0][0]->max_items)
			{
				echo '{ "result": "validation_error", "errors": {"capacity":"<p>Upgrade to Paper Tiger Pro to have more 100 items per location!</p>"}}';
				return;
			}

		}

			if ($this->form_validation->run() == FALSE) {
				if(form_error('name')!=''){$errors[]='"name":"'.form_error('name').'"';}
				if(form_error('capacity')!=''){$errors[]='"capacity":"'.form_error('capacity').'"';}
				if(form_error('review_num')!=''){$errors[]='"review_num":"'.form_error('review_num').'"';}
				
				$errors = implode(",", $errors);
				echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
			}  else {
				$modified_rows = $this->Location_model->insert();
				$this->Logging_model->locations(0,$modified_rows);
				$this->Permissions_model->set_location_permissions($this->user_data['organization_id'],$this->user_data['id'],$modified_rows[0],1,1);
				$this->session->set_flashdata('modified_rows', $modified_rows);
				
				$loc_name = $this->input->post('name');
				
				echo '{ "result": "successful_input", "grid_callback": "/locations/grid/", "message_type": "success", "message": "Location \"'.htmlspecialchars($loc_name).'\" added successfully."}';				
			}
		} 
		

		else{
			// ajax view

				$packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']));
				$package=(array($packages->result()));
				$locations=$this->Location_model->get_from_database();
				$num_of_locs=$locations->num_rows();
				if($package[0][0]->max_locations <= intval($num_of_locs) &&  $package[0][0]->max_locations != -1) {
						$this->load->view('upgrade_now');
				}
			// limit locations for free users
			else {
			$this->load->view('add_location');
			}
		}
		}
		else {
			$this->load->view('session_expired');
		}
	}
	
	function help(){
	
	$this->load->view('digital_help');
	
	}
	function help_red(){
	
	$this->load->view('digital_help_red');
	
	}
	function help_blue(){
	
	$this->load->view('digital_help_blue');
	
	}
	function help_green(){
	
	$this->load->view('digital_help_green');
	
	}
	
	function location_grid($location_id = NULL,$items=NULL,$page_num = NULL,$order_by = NULL,$order = NULL ) {
		$page_size=$this->config->item('page_size');
		if($items!==NULL){
			$page_slot_num=$this->Item_model->get_by_id($items)->slot_num;
		} else {
			$page_slot_num=1;
		}
		if($page_num==null) {
			$page_num=1;
		}
		if($order_by==null) {
			$order_by="slot_num";
		}
		if($order==null) {
			$order="asc";
		}
		location_exists($location_id);
		$this->authentication->require_authentication();
		global $user_data;
	
		$table_data = $this->Item_model->get_from_location($location_id,$page_num,$order_by,$order);

		// lets make sure this location is owned by the user		
		$this_location = $this->Location_model->get_by_id($location_id, $this->user_data['organization_id']);

		//lets make sure we got a valid location to work with.
		if( !isset($this_location)) {
			redirect('locations');
			return;
		}

		$database_id=$this->session->userdata('database_id');

		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);

		$is_network = $this->Network_model->is_network($package_id);

		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		if($is_network->num_rows() == 0):
			$locations = $this->Location_model->get_from_database();
		endif;	

		if($is_network->num_rows() > 0):
			$locations = $this->Location_model->get_from_network_database($database_id);				
		endif;	

		if($is_network->num_rows() > 0 && $organization_admin == $this->user_data['id']):
			$locations = $this->Location_model->get_from_database();
		endif;	
		
		$data = array(
							'table_data'=>$table_data,
							'this_location'=>$this_location,
							'locations'=>$locations,
							'page_number'=>$page_num,
							'order_by'=>$order_by,
							'order'=>$order,
							'modified_rows'=>$this->session->flashdata('modified_rows')
		);
		//ajax view
		//Are we on the last page?
		$last_page = ((ceil($this_location->capacity / $page_size )) - (ceil($page_slot_num / $page_size)));
		$this->load->view('item_grid_by_location', $data);
	}
	
	
	
	function delete(){
		global $user_data;	
		if($this->authentication->is_authenticated())
		{

		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		
		// lets get an array of all the location ids from the user's database
		$org_locs = $this->Location_model->get_from_database()->result_array();
		$org_loc_ids = array();
		foreach($org_locs as $loc) {
			$org_loc_ids[] = $loc['id'];			
		}

		foreach($arr as $loc_ids) {
			$loc_permissions=$this->Permissions_model->get_by_location($loc_ids,$user_data['id']);
			$can_edit=false;
			if($loc_permissions) {
			if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3) {
				$can_edit=true;
			  }
			}

			$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
			if($organization_admin == $this->user_data['id']) {
				$can_edit=true;
			}

			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			$is_network=$this->Network_model->is_network($package_id);
			if($is_network->num_rows() == 0):
				$database_permissions=3;
				$can_edit="true";
			endif;

			if($can_edit==false){
				$this->load->view('facebox_error',array('error'=>'You do not have permission to delete this location.'));
				return;
			}
		}
				
		if($this->input->post('delete') !== NULL) {	
		
			//we need to make sure they are only deleting locations that they own


			foreach($loc as $loc_id) {
				if( ! in_array($loc['id'], $org_loc_ids) )
				{
					$this->load->view('facebox_error',array('error'=>'You are attempting to delete a location which you do not have access to.'));
					return;
				}
			}


		
			$locs = $this->input->post('loc');
			$this->Logging_model->locations(2,$locs);

			//verify password before the locations are deleted

			$this->load->library('form_validation');
	
			if(($user_data['sha1_password']) != sha1($this->input->post('location_delete_password').$this->config->item('encryption_key')))
			{
				echo '{ "result": "validation_error", "errors": {"name":"<p>The password is wrong, You must provide a valid password to delete a location.</p>"}}';
			}
			else
			{
				$this->Location_model->delete($locs);
			
				$text = count($locs)>1?'Locations':'Location';
				
				$text = $text." deleted successfully.";			
 			
				echo '{ "result": "successful_input", "grid_callback": "/locations/grid/", "message_type": "success", "message": "'.$text.'"}';
			}
 			
		} else {
			
			if(count($arr)==0){
				$this->load->view('facebox_error',array('error'=>'Please select a location first.'));
			} else {
			
				//we need to make sure they are only deleting locations that they own
				foreach($arr as $loc_id) {
					if( ! in_array($loc_id, $org_loc_ids) )
					{
						$this->load->view('facebox_error',array('error'=>'You are attempting to delete a location which you do not have access to.'));
						return;
					}
				}
			//make sure both source and destination location have no pending tranfers
			foreach($arr as $loc_id) {
					$dest_missing=$this->Transfers_model->destination_is_pending($loc_id);
					$orig_missing=$this->Transfers_model->origin_is_pending($loc_id);
					if($orig_missing > 0 || $dest_missing > 0)
					{
						$data = array('pending_locations' => $this->Transfers_model->pending_locations($arr));
						$this->load->view('locations_pending',$data);
						return;
					}
			}
			
				$data = array('locations' => $this->Location_model->get_by_ids($arr)->result_array());
				$this->load->view('location_delete_confirm', $data);
			}
		}
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
}
?>