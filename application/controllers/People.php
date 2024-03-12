<?php
class People extends CI_Controller {
	
	var $package;
	
	function __construct() {
		parent::__construct();

		$this->load->library('authentication', 'session');
		$this->user_data = $this->session->userdata('user_data');

		$this->load->model('package_model');
		$this->load->model('organization_model');
		$this->load->model('user_model');

		//$this->authentication->require_authentication();

		if($this->authentication->is_authenticated())
		{

		$organization_package_id = $this->organization_model->get_package_id($this->user_data['organization_id']);
		
		$this->package = $this->package_model->get_package( $organization_package_id );

		}
		//TODO: dont let them add more users if they've reached their max		
	}
	
	function index() {
		$this->authentication->require_authentication();
				
		$users = $this->organization_model->get_all_users($this->user_data['organization_id']);
		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		
		$data = array(	'user_data'			=>	$this->user_data,
						'users'				=>	$users,
						'package'			=>	$this->package,
                        'is_network' => $this->Network_model->is_network($package_id),                        
                        'organization_admin' => $this->Permissions_model->get_organization_admin($this->user_data['organization_id']),                        
						'packages' 			=>	$this->Package_model->get_by_id($package_id),
						'user_slots'		=>	$this->organization_model->get_user_slots($this->user_data['organization_id']),
						'body_id'			=>	'people'
		);
			
		$data['available_slots'] = $data['user_slots'] - $users->num_rows();
			
		//full page
		$this->load->view('header', $data);
		
		if($this->authentication->get_level() == 'admin') {
			$this->load->view('people/admin_index', $data);
		}else {
			$this->load->view('people/me');	
		}
	
		$this->load->view('footer');
	}
	
	//a user editing their own info
	function me() {
	$this->authentication->require_authentication();
	if($this->authentication->is_authenticated())
	{
		$this->load->helper('form');
			
		//lets check if the organization owns this user
		$user = $this->user_model->get_user($this->user_data['id']);
			
		if($user->num_rows() == 0) {
			echo '<strong>An error occurred.</strong><br/><br/>You do not have access to edit this user';
			return;
		}
		
		$this->_user_edit_validation();
				
		//only validate password field if it isnt blank
		if($this->input->post('password') != "") {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
			$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'trim|required|min_length[6]|matches[password]');
			$this->form_validation->set_rules('password_current', 'Current Password', 'required|callback_check_current_password'); 
			$password = sha1($this->input->post('password').$this->config->item('encryption_key'));
		} else {
			$password = NULL;
		}
		

		if( $this->input->post('email') == $user->row()->email_address ) {
			$email = NULL;
			$this->form_validation->set_rules('email', 'New Email', 'callback_email_check');
		} else if($this->input->post('email') == "") {
			$email = NULL;
		}
		else {
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|callback_email_check');
			$email = $this->input->post('email');
		}
		
		
		if($this->input->post('first_name') !== NULL) {
			
			if ($this->form_validation->run() == FALSE)
			{
				
				if(form_error('first_name')!=''){$errors[]='"first_name":"'.form_error('first_name').'"';}
				if(form_error('last_name')!=''){$errors[]='"last_name":"'.form_error('last_name').'"';}
				if(form_error('password')!=''){$errors[]='"password":"'.form_error('password').'"';}
				if(form_error('password_confirmation')!=''){$errors[]='"password_confirmation":"'.form_error('password_confirmation').'"';}
				if(form_error('email')!=''){$errors[]='"email":"'.form_error('email').'"';}
				if(form_error('password_current')!=''){$errors[]='"password_current":"'.form_error('password_current').'"';}
				
				$errors = implode(",", $errors);
				echo '{ "result": "validation_error", "errors": {'.$errors.'}}';	
			}else {
			
				//send out two emails, notification email to current address and activation to new address
				$activation_code=md5(time().$this->config->item('encryption_key'));
				$data = array(	
					'activation_code'	=> $activation_code,
					'email_body'		=> "A request has been generated from Paper Tiger Online for change of your email address. Your email address also doubles up as your login name"
				);

				if($email !== NULL) {
						
					$this->user_model->set_activation_code($user->row()->id, $activation_code);
					
					$this->load->library('postmark');
					$this->postmark->to($this->input->post('email'));
					$this->postmark->subject('Paper Tiger email change confirmation');
					$this->postmark->message_html($this->load->view('email_templates/change_email_address', $data, true));
					$this->postmark->send();			

					$this->load->library('postmark');
					$this->postmark->to($user->row()->email_address);
					$this->postmark->subject('Paper Tiger email change notification');
					$this->postmark->message_html($this->load->view('email_templates/confirm_email_change', $data, true));
					$this->postmark->send();			
				
				}

				$this->user_model->update($user->row()->id, $this->db->escape_str($this->input->post('first_name')), $this->db->escape_str($this->input->post('last_name')), $email, $password);
				
				/* 
				/ The following 2 lines need to be hidden until we release v1 

				
				$this->session->set_flashdata('user', 'Your Information Was Updated Successfully.');
				echo '{ "result": "successful_input", "grid_callback": "/people/edit_callback"}';
				
				*/
				
				/*
				/ The redirect will need to be removed on real launch
				*/
				if($this->input->post('email') != NULL){
					$text="An email has been sent to";
					$text.=" ";
					$text.= $this->input->post('email');
					$text.= " for verification";
					$this->session->set_flashdata('success',$text);
				}
				else {
					$this->session->set_flashdata('success', 'User information updated successfully.');
				}
				echo '{ "result": "successful_redirect", "url": "/"}';
		//		redirect('/people/me');
			}
		} else {
			$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
			$data = array(	'user_data'	=>	$this->user_data,
							'action'	=>	'do_edit',
							'title'		=>	'Edit User',
							'packages' 			=>	$this->Package_model->get_by_id($package_id),
							'user'		=>	$user->row(),
							'action'	=>	'people/me',
							'body_id'		=>	'people'
			);
				
			$this->load->view('header', $data);
			$this->load->view('people/edit_self_form', $data);
			$this->load->view('footer');
		}	
		}
		else
		{
			$this->load->view('session_expired');
		}
	}
	
	function edit() {
		$this->authentication->require_authentication('admin');
      		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_admin=$this->Permissions_model->get_organization_admin($this->user_data['organization_id']);

		
			$this->load->helper('form');
			$user_id = $this->uri->segment(3);

			if(!$user_id) {
				$this->session->set_flashdata('error', 'Choose a user to edit.');
				redirect('/people');
			}
			
			//lets check if the organization owns this user
			//$user_id=$this->uri->segment(3);
			$user = $this->user_model->get_organization_user($user_id, $this->user_data['organization_id']);
				
			if($user->num_rows() == 0) {
				$this->session->set_flashdata('error', 'You do not have access to edit this user.');
				redirect('/people');
			}
			
			$data = array(	'user_data'	=>	$this->user_data,
							'title'		=>	'Edit User',
							'user'		=>	$user->row(),
							'action'	=>	'people/do_edit/'.$user->row()->id,
			);
			
			$organization_users=$this->User_model->get_user_by_organization($this->user_data['organization_id']); //Getting not active and active users
			$data = array(
								'user_data' => $this->user_data, 
								'databases' => $this->Database_model->get_from_organization(),
								'user'		=>	$user->row(),
								'action'	=>	site_url().'people/do_edit/'.$user->row()->id,
								'packages' => $this->Package_model->get_by_id($package_id),
								'organization' => $this->Organization_model->get_organization($this->user_data['organization_id'])->row()->name,
								'is_network' => $is_network,
								'organization_users' => $organization_users,
								'is_network' => $this->Network_model->is_network($package_id),                        
								'organization_admin' => $organization_admin,
								'users'	=> $this->Organization_model->get_users($this->user_data['organization_id'])	
			);
			$this->load->view('header', $data);
			$this->load->view('people/edit_form', $data);
			$this->load->view('footer');
		//ajax page
	}
	
	function edit_callback() {
		$this->add_callback(); //why not?
	}
	
	function delete() {
		$this->authentication->require_authentication('admin');
		$user_id = $this->uri->segment(3);
		
		if($user_id === NULL) {
			$user_id = $this->input->post('user_id');
		}
		
		//still no user id?
		if($user_id === NULL) {
			$this->load->view('facebox_error',array('error'=>'Select a user first.'));
			return;
		}
			
		//lets check if the organization owns this user
		$user = $this->user_model->get_organization_user($user_id, $this->user_data['organization_id']);	
		
		if($user->num_rows() == 0) {
			$this->load->view('facebox_error',array('error'=>'<strong>An error occured.</strong><br/><br/>You do not have access to delete this user'));
			return;
		}

		if($user->row()->id == $this->user_data['id']) {
			$this->load->view('facebox_error',array('error'=>'<strong>An error occured.</strong><br/><br/>It is not possible to delete your own account. You are the admin!'));
			return;
		}
		
		
		if($this->input->post('delete') !== NULL) {
			$user_id = $this->input->post('user_id');
			//$user_id = $this->uri->segment(3);
			$this->user_model->delete($user_id);

			$this->session->set_flashdata('success', 'User successfully deleted.');
			redirect('people');


		} else {
			if($user_id === FALSE){
				$this->load->view('facebox_error',array('error'=>'Select a user first.'));
			} else {
				$data = array(
						'user' => $user->row()
				);
				$this->load->view('people/user_delete_confirm', $data);
			}
		}
	}
	
	function add() {
		$this->authentication->require_authentication('admin');
		
		$users = $this->organization_model->get_users($this->user_data['organization_id']);
				
		$available_slots = $this->organization_model->get_user_slots($this->user_data['organization_id']) - $users->num_rows();
		
		if($this->package->max_users == 1 OR $available_slots == 0) {
			$data = array(	'user_data'	=>	$this->user_data );
			$this->load->view('people/need_more_slots');					
			return;
		}
		
		$this->load->helper('form');
		

      		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_users=$this->User_model->get_user_by_organization($this->user_data['organization_id']); //Getting not active and inactive users
		$data = array(
							'user_data' => $this->user_data, 
							'databases' => $this->Database_model->get_from_organization(),
							'action' =>	'do_add',
	      						'packages' => $this->Package_model->get_by_id($package_id),
							'organization' => $this->Organization_model->get_organization($this->user_data['organization_id'])->row()->name,
							'is_network' => $is_network,
							'organization_users' => $organization_users,
							'users'	=> $this->Organization_model->get_users($this->user_data['organization_id'])	
		);
		$this->load->view('header', $data);
		$this->load->view('people/add_form');
		$this->load->view('footer');



		
	}
	
	function add_callback() {
		$this->authentication->require_authentication();
		
		$users = $this->organization_model->get_users($this->user_data['organization_id']);
		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);

		$data = array(	'user_data'		=>	$this->user_data,
						'packages' 			=>	$this->Package_model->get_by_id($package_id),
						'users'			=>	$users,
						'package'		=>	$this->package,
						'user_slots'	=>	$this->organization_model->get_user_slots($this->user_data['organization_id'])
		);
		
		$data['available_slots'] = $data['user_slots'] - $users->num_rows();
	
		$this->load->view('people/edit_self_form', $data);
	}
	
	
	function do_edit() {
		$this->authentication->require_authentication();
		
		$this->_user_edit_validation();
		
		//$user_id = $this->uri->segment(3);
		$user_id=$this->input->post('user_id');
		$user = $this->user_model->get_organization_user($user_id, $this->user_data['organization_id']);

		//does this admin own the user they are trying to edit?
		if($user->num_rows() == 0) {
			echo '<strong>An error occurred.</strong><br/><br/>You do not have access to edit this user';
		}
		
		//only validate password field if it isnt blank
		if($this->input->post('password') != "") {

			$this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]');
			$this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'trim|min_length[6]|matches[password]');

			//$this->form_validation->set_rules('password', 'Password', 'min_length[6]|matches[password_confirmation]');
			$password = sha1($this->input->post('password').$this->config->item('encryption_key'));
		} else {
			$password = NULL;
		}
			
		if ($this->form_validation->run() == FALSE)
		{
			$errors = array();
			
			if(form_error('first_name')!=''){$errors[]='"first_name":"'.form_error('first_name').'"';}
			if(form_error('last_name')!=''){$errors[]='"last_name":"'.form_error('last_name').'"';}
			if(form_error('password')!=''){$errors[]='"password":"'.form_error('password').'"';}
			if(form_error('password_confirmation')!=''){$errors[]='"password_confirmation":"'.form_error('password_confirmation').'"';}
			
			$errors = implode(",", $errors);
			echo '{ "result": "validation_error", "errors": {'.$errors.'}}';

		}
		else
		{	
		
			$this->user_model->update($user_id, $this->input->post('first_name'), $this->input->post('last_name'), NULL, $password);

			$databases = $this->Database_model->get_from_organization();
			foreach($databases->result() as $dbs) {
					$db_read="db_r_new_".$dbs->id;
					$db_write="db_w_new_".$dbs->id;
					if($this->input->post($db_read) == ''){
						$db_read=0;
					}
					if($this->input->post($db_read) == "yes"){
						$db_read=1;
					}
					if($this->input->post($db_write) == ''){
						$db_write=0;
					}
					if($this->input->post($db_write) == "yes"){
						$db_write=1;
					}

					$update_id=$this->Permissions_model->update_database_permissions($this->user_data['organization_id'],$user_id,$dbs->id,$db_read,$db_write);

				$locations = $this->Location_model->get_by_database($dbs->id);
				foreach($locations->result() as $locs) {
						$loc_read="loc_r_new_".$locs->id;
						$loc_write="loc_w_new_".$locs->id;
							if($this->input->post($loc_read) == ''){
								$loc_read=0;
							}
							if($this->input->post($loc_read) == "yes"){
								$loc_read=1;
							}
							if($this->input->post($loc_write) == ''){
								$loc_write=0;
							}
							if($this->input->post($loc_write) == "yes"){
								$loc_write=1;
							}
						$update_id=$this->Permissions_model->update_location_permissions($this->user_data['organization_id'],$user_id,$locs->id,$loc_read,$loc_write);
				}
			}
			
			$this->session->set_flashdata('success', 'User Edited Successfully');

				echo '{ "result": "successful_redirect", "url": "/people"}';
		}
	}
	
	function edit_single() {
		$this->authentication->require_authentication();
		
		$this->_user_edit_validation();
		
		//$user_id = $this->uri->segment(3);
		$user_id=$this->input->post('user_id');
		$user = $this->user_model->get_organization_user($user_id, $this->user_data['organization_id']);

		//does this admin own the user they are trying to edit?
		if($user->num_rows() == 0) {
			echo '<strong>An error occurred.</strong><br/><br/>You do not have access to edit this user';
		}
		
		//only validate password field if it isnt blank
		if($this->input->post('password') != "") {
			$this->form_validation->set_rules('password', 'Password', 'min_length[6]|matches[password_confirmation]');
			$password = sha1($this->input->post('password').$this->config->item('encryption_key'));
		} else {
			$password = NULL;
		}
			
		if ($this->form_validation->run() == FALSE)
		{
			$errors = array();
			
			if(form_error('first_name')!=''){$errors[]='"first_name":"'.form_error('first_name').'"';}
			if(form_error('last_name')!=''){$errors[]='"last_name":"'.form_error('last_name').'"';}
			if(form_error('password')!=''){$errors[]='"password":"'.form_error('password').'"';}
			
			$errors = implode(",", $errors);
			echo '{ "result": "validation_error", "errors": {'.$errors.'}}';

		}
		else
		{	
			$this->user_model->update($user_id, $this->input->post('first_name'), $this->input->post('last_name'), NULL, $password);
				$this->session->set_flashdata('success', 'Your information was updated successfully.');
                echo '{ "result": "successful_redirect", "url": "/dashboard"}';
			}
		}	


	function update_password() {
		global $user_data;
		$this->authentication->require_authentication();
		//var_dump(md5($this->input->post('old_password')));
		if($this->user_data['sha1_password'] != sha1($this->input->post('old_password').$this->config->item('encryption_key')))
		{
				$this->session->set_flashdata('error', 'The current password you entered was wrong.');
				redirect('/account');
		}
		if($this->input->post('new_password') != $this->input->post('new_password_confirm'))
		{
				$this->session->set_flashdata('error', 'Your passwords did not match.');
				redirect('/account');
		}
		if( strlen($this->input->post('new_password')) < 6 || strlen($this->input->post('new_password_confirm')) < 6 )
		{
				$this->session->set_flashdata('error', 'Your passwords must be atlest 6 characters each.');
				redirect('/account');
		}
		$this->User_model->change_password($this->user_data['id'],sha1($this->input->post('new_password').$this->config->item('encryption_key')));
		redirect('/login/logout');
	}
	
	function do_add() {
		$this->authentication->require_authentication('admin');
				
		$this->_user_validation();
		
		if ($this->form_validation->run() == FALSE)
		{

			if(form_error('first_name')!=''){$errors[]='"first_name":"'.form_error('first_name').'"';}
			if(form_error('last_name')!=''){$errors[]='"last_name":"'.form_error('last_name').'"';}
			if(form_error('email')!=''){$errors[]='"email":"'.form_error('email').'"';}
			
			$errors = implode(",", $errors);
			echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
		}
		else
		{
			$user = $this->input->post('user');
			
			$new_user_id = $this->user_model->create($this->input->post('first_name'), $this->input->post('last_name'), $this->input->post('email'), md5(time()), $this->user_data['organization_id']);
			
			$new_user = $this->user_model->get_user($new_user_id)->row();
                        $org_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id']);
			$admin_user=$this->User_model->get_user($org_admin->admin_user)->row();
			
			$data = array(	
				'register_url'	=> site_url().'signup/user/'.$new_user->activation_code,
				'organization_name' => $this->user_data['organization_name'],
				'admin_fname' => $admin_user->first_name,
				'admin_lname' => $admin_user->last_name,
				'admin_email' => $admin_user->email_address,
				'new_user_fname' => $this->input->post('first_name'),
				'new_user_lname' => $this->input->post('last_name')

			);

			$databases=$this->Database_model->get_from_organization();
			foreach($databases->result() as $dbs) {
				if($this->input->post('db_r_new_'.$dbs->id) == '') {
					$db_read=0;
				} if($this->input->post('db_r_new_'.$dbs->id) == "yes") {
					$db_read=1;
				}
				if($this->input->post('db_w_new_'.$dbs->id) == '') {
					$db_write=0;
				} if($this->input->post('db_w_new_'.$dbs->id) == "yes") {
					$db_write=1;
				}
				//write to database_permissions here
				$db_perm_id=$this->Permissions_model->set_database_permissions($this->user_data['organization_id'],$new_user_id,$dbs->id,$db_read,$db_write);

				$locations=$this->Location_model->get_by_database($dbs->id);
				foreach($locations->result() as $locs) {
					if($this->input->post('loc_r_new_'.$locs->id) == '') {
						$loc_read=0;
					} if ($this->input->post('loc_r_new_'.$locs->id) == "yes") {
						$loc_read=1;
					} if($this->input->post('loc_w_new_'.$locs->id) == '') {
						$loc_write=0;
					} if($this->input->post('loc_w_new_'.$locs->id) == "yes") {
						$loc_write=1;
					}
					//write loc permissions here
				$loc_perm_id=$this->Permissions_model->set_location_permissions($this->user_data['organization_id'],$new_user_id,$locs->id,$loc_read,$loc_write);
				}
			}
			
			/* start email */
			$this->load->library('postmark');
			$this->postmark->to($new_user->email_address, $new_user->first_name.' '.$new_user->last_name);
			$this->postmark->subject('You have been invited to create an account at Paper Tiger');
			$this->postmark->message_html($this->load->view('email_templates/owner_user_creation', $data, true));
			$this->postmark->send();
			
			/* end email */

			$this->session->set_flashdata('success', 'An invitation email has been sent to '.$new_user->email_address);
			echo '{ "result": "successful_redirect", "url": "/people"}';
		}
	}
	
	function _user_validation() {
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('first_name', 'First Name', 'required|max_length[12]|trim');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|max_length[12]|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|max_length[35]|trim|valid_email|callback_email_check');
	}
	
	function _user_edit_validation() {
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('first_name', 'First Name', 'required|max_length[12]|trim');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|max_length[12]|trim');
			
		$this->form_validation->set_message('matches', 'The %s fields did not match.');
	}
	
	function email_check($str) {
		$this->load->model('user_model', 'user');
		if($this->user->get_user($str, 'email_address')->num_rows() > 0) {
			$this->form_validation->set_message('email_check', 'The %s Address is already in use, and may only be associated with a single account.');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	function check_current_password($str) {
		
		if( sha1($this->input->post('password_current').$this->config->item('encryption_key')) !== $this->user_data['sha1_password'] ) {
			$this->form_validation->set_message('check_current_password', 'The %s is not valid for this account.');
			return FALSE;
		} else {
			return TRUE;		
		}
	
	}
}
?>
