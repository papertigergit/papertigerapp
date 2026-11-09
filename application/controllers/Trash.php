<?php
class Trash extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'empty_messages'));
		$this->load->model(array('Location_model', 'Trash_model', 'Category_model', 'Item_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;

		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
	
		if($is_network->num_rows() > 0):
			$db_perms=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$user_data['id']);
			$database_permissions=null;
			if($db_perms):
				$database_permissions=$db_perms->pto_umask;
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
		


			$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			$is_network = $this->Network_model->is_network($package_id);
			if($is_network->num_rows() == 0):
				$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
				$table_data = $this->Trash_model->get_from_database();
			endif;
			if($is_network->num_rows() > 0):
				$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
				$table_data = $this->Trash_model->get_from_network_database();
			endif;
			if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
				$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
				$table_data = $this->Trash_model->get_from_database();
			endif;

            $database_id=$this->session->userdata('database_id');

            //LOGIC TO TURN ON BUTTONS IN VIEW
            $buttons_on = false;
            $can_edit = false;
            $package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
            $is_network=$this->Network_model->is_network($package_id);

            //IF YOU ARE A NETWORK ACCOUNT, ONLY SHOW BUTTONS IF YOU HAVE WRITE ACCESS
            if($is_network->num_rows() > 0):
                $db_perms=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id']);
			$database_permissions=null;
			if($db_perms):
				$database_permissions=$db_perms->pto_umask;
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
                                'buttons_on' => $network_utils['buttons_on'],
                                'is_network' => $network_utils['is_network'],
                                'admin_email' => $network_utils['admin_email'],
                                'admin_name' => $network_utils['admin_name'],
								'body_id'=>'browse',
								'locations'=>$locations
			);
			//full page
			$this->load->view('header', $data);
            $this->load->view('trash_grid', $data);
            $this->load->view(empty_message_view($database_id), $data); //This loads the corresponding empty views!
			$this->load->view('footer');
		}
	}
	
	function grid() {
		$this->authentication->require_authentication();


			$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
			$database_id=$this->session->userdata('database_id');
			$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
			$is_network = $this->Network_model->is_network($package_id);

			if($is_network->num_rows() == 0):
				$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
				$table_data = $this->Trash_model->get_from_database();
			endif;
			if($is_network->num_rows() > 0):
				$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
				$table_data = $this->Trash_model->get_from_network_database();
			endif;
			if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
				$locations = $this->Location_model->get_from_database($this->session->userdata('database_id'));
				$table_data = $this->Trash_model->get_from_database();
			endif;



            //LOGIC TO TURN ON BUTTONS IN VIEW
            $buttons_on = false;
            $can_edit = false;
            $package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
            $is_network=$this->Network_model->is_network($package_id);

            //IF YOU ARE A NETWORK ACCOUNT, ONLY SHOW BUTTONS IF YOU HAVE WRITE ACCESS
            if($is_network->num_rows() > 0):
                $database_permissions_handlers=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id']);
		$database_permissions=null;
		if($database_permissions_handlers) {
			$database_permissions=$database_permissions_handlers->pto_umask;
		}
                if (($database_permissions == '1') || ($database_permissions == '3')) {
                    $can_edit="edit_true";
                    $buttons_on = true;
                }
                endif;

	    $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
	    if($organization_admin == $this->user_data['id']):
                    $can_edit="edit_true";
                    $buttons_on = true;
	    endif;	

            //SHOW BUTTONS BY DEFAULT ON SINGLE USER PLANS
            if($is_network->num_rows() == 0):
                $can_edit="edit_true";
                $buttons_on = true;
        endif;


		$data = array(
                    'table_data'=>$table_data,
                    'buttons_on' => $buttons_on,
                    'can_edit' => $can_edit,
                    'is_network' => $is_network,
                    'locations'=>$locations
		);
		//ajax view
		$this->load->view('trash_grid', $data);
	}
	
	function restore(){
		if($this->authentication->is_authenticated())
		{
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		
		if($this->input->post('restore') !== NULL) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('new_location', 'Destination', 'is_natural_no_zero');
			$this->form_validation->set_message('is_natural_no_zero', 'You must choose a destination');
			
			if ($this->form_validation->run() === FALSE) {
				$errors[]='"missingFromForm":"'.form_error('new_location').'"';
				$errors = implode(",", $errors);
				echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
			} else {
			
				$restore_items = $this->input->post('restore');
				
				//$text = count($restore_items)>1?'Items':'Item';
				$text = 'Item';
					
				$text = $text." restored successfully.";
				
				$this->Logging_model->trash(12,$this->input->post('item'));
				$this->Trash_model->restore($this->input->post('item'), $this->input->post('new_location'));
				
				//echo '{ "result": "successful_input", "grid_callback": "/trash/grid/"}';
				echo '{ "result": "successful_input", "grid_callback": "/trash/grid/", "message_type": "success", "message": "'.$text.'"}';

			}
		} else {
			if(count($arr)==0){
				$this->load->view('facebox_error',array('error'=>'Select an Item first.'));
			} else {
				$item_arr = $this->Trash_model->get_by_ids($arr)->result_array();
				$data = array(
						'item' => $item_arr[0],
						'locations' => $this->Location_model->get_from_database()
				);
				$this->load->view('trash_restore_confirm', $data);
			}
		}
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
	
	function delete() {
		if($this->authentication->is_authenticated())
		{
		$arr = $this->uri->segment_array();
		
		array_shift($arr);
		array_shift($arr);
		
		if($this->input->post('delete') !== NULL) {
		//	exit(var_dump($this->input->post('trash')));
			$this->Logging_model->trash(11,$this->input->post('trash'));
			$this->Trash_model->delete($this->input->post('trash'));
			
			$trash_items = $this->input->post('trash');
			
			$text = count($trash_items)>1?'Items':'Item';
				
			$text = $text." permanently deleted successfully.";
			
			//echo '{ "result": "successful_input", "grid_callback": "/trash/grid/"}';
			echo '{ "result": "successful_input", "grid_callback": "/trash/grid/", "message_type": "success", "message": "'.$text.'"}';
		} else {
			if(count($arr)==0){
				$this->load->view('facebox_error',array('error'=>'Select an Item first.'));
			} else {
				$this->load->view('trash_delete_confirm', array('trash' => $this->Trash_model->get_by_ids($arr)->result_array()));
			}
		}
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
	
	function empty_trash(){
		global $user_data;
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
			if($organization_admin != $this->user_data['id']){
				$this->load->view('facebox_error',array('error'=>'Only administrators can empty the trash.'));
				return;
			}
		if($this->authentication->is_authenticated())
		{
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		if($this->input->post('empty') !== NULL) {
			if(($user_data['sha1_password']) != sha1($this->input->post('trash_empty_password').$this->config->item('encryption_key')))
			{
				echo '{ "result": "validation_error", "errors": {"name":"<p>The password is wrong, You must provide a valid password to empty the trash.</p>"}}';
			}
			else
			{
				$this->Logging_model->database(10,$this->session->userdata('database_id'));
				$this->Trash_model->empty_trash();
				echo '{ "result": "successful_input", "grid_callback": "/trash/grid/"}';
			}
		}
		else {
			$this->load->view('empty_trash_confirm', array('trash' => $this->Trash_model->get_from_database()->result_array()));
			}
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
}
?>
