<?php
class Categories extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library(array('authentication', 'session'));
		$this->load->helper(array('url', 'text', 'empty_messages','access_helper'));
		$this->load->model(array('Category_model', 'Item_model', 'Location_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
		if($this->session->userdata('database_id')) {
			database_exists($this->session->userdata('database_id'));
		}
	}
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;

		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		if($is_network->num_rows() > 0):
			$database_permissions=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id']);
			if($database_permissions):
				$database_permissions=$database_permissions->pto_umask;
			endif;	

				if($organization_admin == $this->user_data['id']):
					$database_permissions=3;
				endif;	
		endif;	
		if($is_network->num_rows() == 0):
			$database_permissions=3;
		endif;	
		if($database_permissions == 0) {
			$this->session->set_flashdata('error', 'You do not have access to this database.');
			redirect(site_url());
		} else {


		if($is_network->num_rows() == 0):
			$table_data = $this->Category_model->get_from_database();
		endif;
		if($is_network->num_rows() > 0):
			$table_data = $this->Category_model->get_from_network_database();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$table_data = $this->Category_model->get_from_database();
		endif;


			//$table_data = $this->Category_model->get_from_database();
			
			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			$is_network = $this->Network_model->is_network($package_id);
			if($is_network->num_rows() == 0):
				$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
			endif;
			if($is_network->num_rows() > 0):
				$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
			endif;
			if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
				$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
			endif;
			
            $database_id=$this->session->userdata('database_id');
			
            //LOGIC TO TURN ON BUTTONS IN VIEW
            $buttons_on = false;
            $can_edit = false;
            $package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
            $is_network=$this->Network_model->is_network($package_id);

            //IF YOU ARE A NETWORK ACCOUNT, ONLY SHOW BUTTONS IF YOU HAVE WRITE ACCESS
            if($is_network->num_rows() > 0):
		$database_permissions=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id']);
		if($database_permissions):
			$database_permissions=$database_permissions->pto_umask;
		endif;	

                if (($database_permissions == '1') || ($database_permissions == '3')) {
                    $can_edit="edit_true";
                    $buttons_on = true;
                }
                endif;
            //SHOW BUTTONS BY DEFAULT ON SINGLE USER PLANS
            if($is_network->num_rows() == 0):
                $can_edit="edit_true";
                $buttons_on = true;
            endif;


        $network_utils = array();
        $network_utils = enable_buttons();

			$data = array(
				'user_data' => $user_data,
				'table_data'=>$table_data,
                                'body_id'=>'browse',
                                'buttons_on' => $network_utils['buttons_on'],
                                'is_network' => $network_utils['is_network'],
				'locations'=>$locations,
                                'admin_email' => $network_utils['admin_email'],
                                'admin_name' => $network_utils['admin_name'],
				'organization_admin' => $organization_admin,
				'modified_rows'=>$this->session->flashdata('modified_rows')
			);
			//full page
			$this->load->view('header', $data);
			$this->load->view('category_grid', $data);
            $this->load->view(empty_message_view($database_id), $data); //This loads the corresponding empty views!
			$this->load->view('footer');
		}
	}
	
	function grid() {
		$this->authentication->require_authentication();

      		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		if($is_network->num_rows() == 0):
			$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
			$table_data = $this->Category_model->get_from_database();
		endif;
		if($is_network->num_rows() > 0):
			$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
			$table_data = $this->Category_model->get_from_network_database();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
			$table_data = $this->Category_model->get_from_database();
		endif;

            $database_id=$this->session->userdata('database_id');
        $network_utils = array();
        $network_utils = enable_buttons();
			
		$data = array(
							'table_data'=>$table_data,
                            'locations'=>$locations,
			    'organization_admin' => $organization_admin,
                            'buttons_on' => $network_utils['buttons_on'],
                            'is_network' => $network_utils['is_network'],
                            'admin_email' => $network_utils['admin_email'],
                            'admin_name' => $network_utils['admin_name'],
							'modified_rows'=>$this->session->flashdata('modified_rows')
		);
		//ajax view
		$this->load->view('category_grid', $data);
	}

	function edit() {
		if($this->authentication->is_authenticated())
		{
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		
		
		if($this->input->post('edit_category') !== NULL) {
			//form submit
			$category_id = $arr[0];
			category_exists($category_id);

			$last_edit_user_id=$this->Category_model->get_by_id($category_id)->last_edited_by;
			$last_edit_db_time=$this->Category_model->get_by_id($category_id)->last_edited_time;

			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Name', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				echo '{ "result": "validation_error", "errors": {"name":"'.form_error('name').'"}}';
			} else if ($this->input->post('edit_time') < $last_edit_db_time) {
				$last_user_edit=$this->User_model->get_user($last_edit_user_id)->row(); 
				$f_name=$last_user_edit->first_name;
				$l_name=$last_user_edit->last_name;
				$db_edit_time=unix_to_human($last_edit_db_time);
				$error_text="This category is out of date and has been edited by $f_name $l_name on $db_edit_time";

                	        echo '{ "result": "validation_error", "errors": {"errors":"'.$error_text.'"}}';
				return;
			}

			
			else {
				$modified_rows = $this->Category_model->update($category_id);
				$this->Logging_model->categories(1,$modified_rows);
				$insert_id=$this->Category_model->log_modifications($category_id,$this->user_data['id']);
				$this->session->set_flashdata('modified_rows', $modified_rows);
				echo '{ "result": "successful_input", "grid_callback": "/categories/grid/", "message_type": "success", "message": "Category edited successfully."}';				
			}
		} else {
			if(count($arr)==0) {
				$this->load->view('facebox_error',array('error'=>'Please select a Category first.'));
			} else if(count($arr)>1) {
				$this->load->view('facebox_error',array('error'=>'Please only select one Category at a time for editing.'));
			} else {
				// ajax view
				$category_id = $arr[0];
				$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
				$is_network=$this->Network_model->is_network($package_id);
				$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
				$users=$this->User_model->get_users_by_org($this->user_data['organization_id']);
		
				$data = array('this_category'=>$this->Category_model->get_by_id($category_id),'last_edit_time' => time(),'is_network' => $is_network,'organization_admin' => $organization_admin,'users' => $users);
				$this->load->view('edit_category', $data);
			}
		}
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
	
	function delete(){
		global $user_data;
		if($this->authentication->is_authenticated())
		{
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		
		if($this->input->post('delete') !== NULL) {
			$cats = $this->input->post('cat');

				$this->Logging_model->categories(2,$cats);
				$this->Category_model->delete($cats);
			
				$text = count($cats)>1?'Categories':'Category';
				
				$text = $text." deleted successfully.";			
			
				echo '{ "result": "successful_input", "grid_callback": "/categories/grid/", "message_type": "success", "message": "'.$text.'"}';			
			
		} else {
			if(count($arr)==0){
				$this->load->view('facebox_error',array('error'=>'Select a Category first.'));
			} else {
				$data = array(
									'categories' => $this->Category_model->get_by_ids($arr)->result_array()
				);
				$this->load->view('category_delete_confirm', $data);
			}
		}
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
	
	function add() {
		if($this->authentication->is_authenticated())
		{
		
		if($this->input->post('name') !== NULL) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			
			if ($this->form_validation->run() == FALSE) {
				echo '{ "result": "validation_error", "errors": {"name":"'.form_error('name').'"}}';
			} else {
				$modified_rows = $this->Category_model->insert();
				$this->Logging_model->categories(0,$modified_rows);
				$this->session->set_flashdata('modified_rows', $modified_rows);
				
				$cat_name = htmlspecialchars($this->input->post('name'));
				
				echo '{ "result": "successful_input", "grid_callback": "/categories/grid/", "message_type": "success", "message": "Category \"'.$cat_name.'\" added successfully."}';				
			}
		} else{
			// ajax view
			$this->load->view('add_category');
		   }
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
	
	function view($category_id = NULL){
		category_exists($category_id);
		$this->authentication->require_authentication();	
		global $user_data;
		

		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		if($is_network->num_rows() == 0):
			$table_data = $this->Item_model->get_by_category($category_id);
			$categories = $this->Category_model->get_from_database();
		endif;
		if($is_network->num_rows() > 0):
			$table_data = $this->Item_model->get_by_network_category($category_id);
			$categories = $this->Category_model->get_from_network_database();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$table_data = $this->Item_model->get_by_category($category_id);
			$categories = $this->Category_model->get_from_database();
		endif;


		$data = array(
							'user_data' => $user_data,
							'table_data'=>$table_data,
							'body_id'=>'browse',
							'organization_admin' => $organization_admin,
							'category_id'=>$category_id,
							'categories' => $categories,
							'modified_rows'=>$this->session->flashdata('modified_rows')
		);
		//full page
		$this->load->view('header', $data);
		$this->load->view('item_grid_by_category', $data);
		$this->load->view('footer');
	}
	
	function category_grid($category_id = NULL){
		category_exists($category_id);
		$this->authentication->require_authentication();	
		global $user_data;
		
		$table_data = $this->Item_model->get_by_category($category_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
		$categories = $this->Category_model->get_from_database();
		$data = array(
							'user_data' => $user_data,
							'table_data'=>$table_data,
							'organization_admin' => $organization_admin,
							'body_id'=>'browse',
							'category_id'=>$category_id,
							'categories' => $categories,
							'modified_rows'=>$this->session->flashdata('modified_rows')
		);
		//full page
		$this->load->view('item_grid_by_category', $data);
	}
}
?>
