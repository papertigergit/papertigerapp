<?php
class Imports extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'file'));
		$this->load->model(array('Import_model','User_model','Organization_model','Database_model','Assign_model','Logging_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
		
		
	}
	
	function index(){
		// prev value was: 3.82.66.154 | 35.168.29.158
		$remote_address = $_SERVER['REMOTE_ADDR'];
		$conversion_server_address = array("50.17.213.37","104.6.6.17","80.90.82.59","75.52.82.18");
		if(!in_array($remote_address,$conversion_server_address)) {
			exit(die("Outside DMZ!"));
		}

		$filename = $this->input->post('url');
		$db_id = substr($filename, 0, strlen($filename)-4);
		$log = fopen('db_imports/import.log','a');
		fwrite($log, "".date('m-d-Y-g:i:sa :: '));
		foreach($_POST as $key => $value) {
  		$value = $this->input->post($key);
  		fwrite($log,'&'.$key.'='.$value);
		}
		fclose($log);
		
		$imports_hash=$this->Database_model->get_imports_hash($db_id)->result();
		print_r($imports_hash);
		foreach($imports_hash as $key)
		{
			$this_db_hash=$key->md5sum;
		}
		
		if(($this->input->post('conversion')==1 || $this->input->post('conversion')==7) && $this_db_hash == $this->input->post('hash')){
			$ch = curl_init();
			$current_env=site_url();
			if($current_env=="http://app.thepapertiger.com/")
			{
				curl_setopt($ch, CURLOPT_URL,'http://50.17.213.37/ConversionFiles/Output/'.$filename);
			}
			elseif($current_env=="http://appdev2.thepapertiger.com/")
			{
				curl_setopt($ch, CURLOPT_URL,'http://50.17.213.37/ConversionFilesTest/Output/'.$filename);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERPWD, 'ptonline:800PT-it0795');
			$csv =  curl_exec($ch);
			if (!write_file('db_imports/'.$filename, $csv)) {
				$this->Database_model->mark_for_error($db_id);
				echo 'Unable to write the file';
			}
			curl_close($ch);

			$this->db->where('id', $db_id);
			$org_id = $this->db->get('databases')->row()->organization_id;
			
			$log = fopen('db_imports/import.debug.log','a');
			fwrite($log, date('m-d-Y-g:i:sa :: '));
	  		fwrite($log,'db id: '.$db_id.' <> org id: '.$org_id);
			fclose($log);
			
			
			try{
				if (($handle = fopen("db_imports/".$filename, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle)) !== FALSE) {
			    if(urldecode($data[18])=="end of month" || urldecode($data[18])=="end of year")
			    {
				//        $data = array(
			          //    		       'status' => 5
				//	                );  
				 //        $this->db->update('databases', $data, "id = $db_id");
				//exit(0);
			    }
			    else
			    {
		    		$this->_process_row($db_id, $org_id, $data);
			    }	
		    }
		    fclose($handle);
			}
			}catch(Exception $e){
				var_dump($e);
			}
			$this->Logging_model->database_imports(13,$db_id,$org_id);
			$this->Database_model->mark_clean($db_id);
			echo "DB imported successfully";
				

			$locations=$this->Location_model->get_by_database($db_id)->result();
			
			$db_data=$this->User_model->get_users_by_db_id($db_id);
			$database_data=$db_data->result();
			$org_name=$this->Organization_model->get_org_by_id($org_id)->result();

			$data = array (
				'database_name'	=>	$this->Database_model->get_by_id($db_id, $this->user_data['organization_id'])->name,
				'date_added'	=>	$this->Database_model->get_by_id($db_id, $this->user_data['organization_id'])->date_added,
				'organization_admin' => $this->Permissions_model->get_organization_admin($this->user_data['organization_id']),
				'locations'	=>	$locations,
				'database_data'	=>	$database_data,
				'org_name' 	=>	$org_name[0]->name
			);

			foreach($database_data as $user_contacts)
			{
				$email_address=$user_contacts->email_address;
				$first_name=$user_contacts->first_name;
				$last_name=$user_contacts->last_name;
			}


                        /* start email */
                        $this->load->library('postmark');
                        $this->postmark->to($email_address, $first_name." ".$last_name);
                        $this->postmark->subject('Your Paper Tiger database imported successfully');
                        $this->postmark->message_html($this->load->view('email_templates/import_report', $data, true));
                        $this->postmark->send();    
                        /* end email */

			
		} else {
			$filename = $this->input->post('url');
			$db_id = substr($filename, 0, strlen($filename)-4);
			$this->Database_model->mark_for_error($db_id);
			echo "There was a conversion error. Log written";
		}
	}

  	function upgrade(){
		  if($this->authentication->is_authenticated())
		  {
  			$this->load->view('upgrade_now');
		  }
		  else
		  {
			  $this->load->view('session_expired');
		  }
	  }
	
	//this should probably be moved to a lib
	function _process_row($db_id, $org_id, $data_arr){
		$location_id = -1;
		if(trim(urldecode($data_arr[2]))!=''){
			//the url decode removes carriage return from end of name
			$location_id = $this->Import_model->get_or_make_location($db_id, urldecode($data_arr[2]),urldecode($data_arr[13]),urldecode($data_arr[18]));

		}
		$category_id = 0;
		if(trim($data_arr[6])!=''){
			$category_id = $this->Import_model->get_or_make_category($db_id, urldecode($data_arr[6]),urldecode($data_arr[11]));
		}

		if($location_id != -1){
			$this->Import_model->insert_item($location_id, $category_id, $data_arr, $db_id, $org_id);
		}	
	}
	
	//this should probably be moved to a lib
	function _do_upload() {	
		$config['upload_path'] = 'uploads/';
		$config['max_size']	= '500000';
		$config['allowed_types'] = 'bak|ptd';
		$db_id = $this->Database_model->_new($this->input->post('name'));
		$config['file_name'] = $db_id;
		
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('file')){
			$this->Database_model->mark_for_error($db_id);
			return array(
					'result' => 'error',
					'msg' => $this->upload->display_errors('','')
			);
		}	else {
			//print_r($this->upload->data());
			$data = $this->upload->data();
			
			//logic to send imports to correct environment based on where they came from!

			$current_env = site_url();
      //Conversion Server Internal Amazon IP: prev: 35.168.29.158
      $conn = ssh2_connect('50.17.213.37', 22);
      ssh2_auth_password($conn, 'ptonline', '800PT-it0795');
			if ($current_env == "http://appdev2.thepapertiger.com/") {
        ssh2_scp_send($conn, $data['full_path'], '/cygdrive/c/Inetpub/wwwroot/PaperTigerNet/ConversionFilesTest/'.$data['file_name']);
			}
			else {
        ssh2_scp_send($conn, $data['full_path'], '/cygdrive/c/Inetpub/wwwroot/PaperTigerNet/ConversionFiles/'.$data['file_name']);
			}
			//ssh2_scp_send($conn, $data['full_path'], '/cygdrive/c/Inetpub/wwwroot/PaperTigerNet/ConversionFiles/'.$data['file_name']);
			$md5_sum=md5_file('uploads/'.$data['file_name']);
			//Log hash to the database
			$this->Database_model->imports_hash($db_id,$md5_sum);

			return array('result' => 'success');
		}
	}

	function do_assign(){
		  if($this->authentication->is_authenticated())
		  {
			  global $user_data;
				if(!is_numeric($this->input->post('org_id'))){
								$this->session->set_flashdata('error', "Only Organization IDS go into the little box!");
								redirect('imports/assign_owner');
				}
		          $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			  $assigned_databases=$this->Assign_model->get_all();
				$data = array(
								'user_data' => $user_data, 
								'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
								'packages' => $this->Package_model->get_by_id($package_id),
								'assigned_databases' => $assigned_databases,
								'is_network' => $this->Network_model->is_network($package_id),
								'databases' => $this->Database_model->get_from_organization()
				);
			if(($user_data['id'] == 3) || ($user_data['id'] == 1) || ($user_data['id'] == 5894) || ($user_data['id'] == 249489) || ($user_data['id'] == 204) || ($user_data['id'] == 6) || ($user_data['id'] == 1862) || ($user_data['id'] == 9132) || ($user_data['id'] == 248436)) {
				if($this->input->post('submit') !== NULL)
				{
					if($this->input->post('org_id') != NULL)
					{
						$data['select']=$this->input->post('select');
						$data['assign_org_id']=$this->input->post('org_id');
						
						//check if org id and select are numbers
						//reassign db
						//reassign items
						//log entries in users log
						$changed_db_id=$this->Assign_model->assign_database($user_data['id'],$user_data['organization_id'],$this->input->post('org_id'),$this->input->post('select'));
						$this->Logging_model->assign_db(15,$this->input->post('select'),$this->input->post('org_id'));
						$get_locations=$this->Assign_model->get_locations($this->input->post('select'));
						foreach($get_locations->result() as $loc_id) {
							$item_id=$this->Assign_model->assign_items($user_data['id'],$this->input->post('org_id'),$loc_id->id);
						}
						

						$this->session->set_flashdata('success', "Database Assigned Successfully.");
						redirect('imports/assign_owner');
					}
					else
					{
						$this->session->set_flashdata('error', "Please input Organization ID");
						redirect('imports/assign_owner');
					}

				}
				else
				{
					$this->session->set_flashdata('error', "Select Database to assign.");
					redirect('imports/assign_owner');
				}
			}
			else {
				redirect('/');
			     }
		  }
		  else {
				redirect('/');
		       }
	}
	
	function assign_owner(){
		  if($this->authentication->is_authenticated())
		  {
			  global $user_data;
		          $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			  $assigned_databases=$this->Assign_model->get_all();
				$data = array(
								'user_data' => $user_data, 
								'packages' => $this->Package_model->get_by_id($package_id),
								'is_network' => $this->Network_model->is_network($package_id),
								'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
								'assigned_databases' => $assigned_databases,
								'org_id' => $this->uri->segment('3'),
								'databases' => $this->Database_model->get_from_organization()
				);
			if(($user_data['id'] == 3) || ($user_data['id'] == 1) || ($user_data['id'] == 5894) || ($user_data['id'] == 204) || ($user_data['id'] == 249489) || ($user_data['id'] == 6) || ($user_data['id'] == 1862) || ($user_data['id'] == 9132) || ($user_data['id'] == 248436)) {
				$this->load->view('header', $data);
				$this->load->view('assign_owner', $data);
				$this->load->view('footer', $data);
			}
			else {
				redirect('/');
			}
		  }
		  else {
				redirect('/');
		  }
	}

	function search(){
		  if($this->authentication->is_authenticated())
		  {
			  global $user_data;
		          $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			  $assigned_databases=$this->Assign_model->get_all();
				$data = array(
								'user_data' => $user_data, 
								'packages' => $this->Package_model->get_by_id($package_id),
								'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
								'is_network' => $this->Network_model->is_network($package_id),
								'assigned_databases' => $assigned_databases,
								'databases' => $this->Database_model->get_from_organization()
				);
			if(($user_data['id'] == 3) || ($user_data['id'] == 1) || ($user_data['id'] == 5894) || ($user_data['id'] == 204) || ($user_data['id'] == 249489) || ($user_data['id'] == 6) || ($user_data['id'] == 1862) || ($user_data['id'] == 9132) || ($user_data['id'] == 248436)) {
				if($this->input->post('submit') !== NULL) {
					$data['org_name']=$this->input->post('org_name');
					$data['organizations']=$this->Assign_model->get_by_email($this->input->post('org_name'));
					$this->load->view('header', $data);
					$this->load->view('get_org_by_name', $data);
					$this->load->view('footer', $data);
				} else {
					redirect('imports/assign_owner');
				}
			}
			else {
				redirect('/');
			}
		  }
		  else {
				redirect('/');
		  }
	}
	
	function new_database(){
	
		$this->authentication->require_authentication();
		global $user_data;

      		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		
		if($this->input->post('new_db')!==NULL) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			
			$errors = array();
			if ($this->form_validation->run() == FALSE) {
				$errors[] = "Name is required";
			}
			
			$success_msg = '';
			if(count($errors)==0) {
				$upload_res = $this->_do_upload();
				if($upload_res['result']=='error'){
					$errors[] = $upload_res['msg'];
				} else {
					/* $success_msg = 'Your file was uploaded successfully!</p><p>It may take several minutes to fully import.'; */
					$this->session->set_flashdata('success', "Please wait, your database should import shortly.");
					redirect('/');
				}
			}
			
			$data = array(
							'name' => $this->input->post('name'),
							'user_data' => $user_data, 
							'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
							'body_id'=>'imports',
							'errors'=>$errors,
	      						'packages' => $this->Package_model->get_by_id($package_id),
							'databases' => $this->Database_model->get_from_organization(),
							'success_msg' => $success_msg
			);
			$this->load->view('header', $data);
			$this->load->view('new_db_import', $data);
			$this->load->view('footer', $data);
			
		} else {
		
			$data = array(
							'user_data' => $user_data, 
							'body_id'=>'imports',
							'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
	      						'packages' => $this->Package_model->get_by_id($package_id),
							'databases' => $this->Database_model->get_from_organization(),
							'errors' => array()
			);
			$this->load->view('header', $data);
			$this->load->view('new_db_import', $data);
			$this->load->view('footer', $data);
		}
	}	


	function db_status(){

		if(!$this->authentication->is_authenticated())
		{
		//	$this->load->view('session_expired');
			echo "session_expired";
		}
		else
		{
		$this->authentication->require_authentication();
		global $user_data;

			$databases=$this->Database_model->get_from_organization();
			if($databases->num_rows() == 0)
			{
			   $this->session->set_flashdata('error', 'This database does not belong to you!');
			   $this->load->view('settings/error');
		           return;
			}
			else
			{
                        foreach($databases->result() as $db)
			{
				//start db ajax view
		       $db_pto_umask=$this->Permissions_model->get_by_database($db->id,$user_data['id']);
		       $package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		       $is_network = $this->Network_model->is_network($package_id);
		       $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id']);

			$pto_db_perms=0;
		       if($is_network):
			       if($db_pto_umask):
				       $pto_db_perms=$db_pto_umask->pto_umask;
			       endif;       
		       if( ($is_network->num_rows() == 0) || ($is_network->num_rows() > 0 &&  $pto_db_perms > 0) || ($organization_admin->admin_user == $this->user_data['id'] )):  
			?>
			<?php if($db->status == 1) {?>
				<li><a href="<?=site_url()?>databases/use_db/<?=$db->id?>"><?=htmlspecialchars($db->name)?></a></li>
			<?php } else if($db->status == 0)
				{
				$date_added_plus_ten_mins=$db->date_added + 600;  //ten mins
				$time_now=time();
				if($date_added_plus_ten_mins > $time_now)
					{?>
					<li><img class="spinner" src="<?php echo site_url();?>img/spinner.gif" />Importing <?=htmlspecialchars($db->name)?><br />
					<span class="small gray db_cancel"><a href="<?=site_url()?>databases/cancel_import/<?=$db->id?>" class="red" rel="facebox">Cancel Import</a></span>
					</li>
				<?php 
					} 
				if($date_added_plus_ten_mins < $time_now)
					{?>
					<li><?=$db->name?>
					(<a href="<?=site_url()?>databases/delete_timedout_import/<?=$db->id?>" class="red" rel="facebox">Import failed</a>)</li>
					<?php
					}	
				}
			else if($db->status == 2){?>
				<li><?=$db->name?>
				(<a href="<?=site_url()?>databases/delete_import/<?=$db->id?>" class="red" rel="facebox">Import failed</a>)</li>
			<?php } else if($db->status == 3){?>
				<li><?=$db->name?>
				(<a href="<?=site_url()?>databases/delete_import/<?=$db->id?>" class="red" rel="facebox">Delete Import</a>)</li>
			<?php } else if($db->status == 4){?>
				<li><?=$db->name?>
				(<a href="<?=site_url()?>databases/delete_import/<?=$db->id?>" class="red" rel="facebox">Delete Import</a>)</li>
			<?php } else if($db->status == 5){?>
				<li><?=$db->name?>
				(<a href="<?=site_url()?>databases/delete_import_review/<?=$db->id?>" class="red" rel="facebox">Import failed</a>)</li>
			<?php }
			?>
	<?php 	    endif;
		endif;
		       if(!$is_network):
			?>
			<?php if($db->status == 1) {?>
				<li><a href="<?=site_url()?>databases/use_db/<?=$db->id?>"><?=htmlspecialchars($db->name)?></a></li>
			<?php } else if($db->status == 0)
				{
				$date_added_plus_ten_mins=$db->date_added + 600;  //ten mins
				$time_now=time();
				if($date_added_plus_ten_mins > $time_now)
					{?>
					<li><img class="spinner" src="<?php echo site_url();?>img/spinner.gif" />Importing <?=htmlspecialchars($db->name)?><br />
					<span class="small gray db_cancel"><a href="<?=site_url()?>databases/cancel_import/<?=$db->id?>" class="red" rel="facebox">Cancel Import</a></span>
					</li>
				<?php 
					} 
				if($date_added_plus_ten_mins < $time_now)
					{?>
					<li><?=$db->name?>
					(<a href="<?=site_url()?>databases/delete_timedout_import/<?=$db->id?>" class="red" rel="facebox">Import failed</a>)</li>
					<?php
					}	
				}
			else if($db->status == 2){?>
				<li><?=$db->name?>
				(<a href="<?=site_url()?>databases/delete_import/<?=$db->id?>" class="red" rel="facebox">Import failed</a>)</li>
			<?php } else if($db->status == 3){?>
				<li><?=$db->name?>
				(<a href="<?=site_url()?>databases/delete_import/<?=$db->id?>" class="red" rel="facebox">Delete Import</a>)</li>
			<?php } else if($db->status == 4){?>
				<li><?=$db->name?>
				(<a href="<?=site_url()?>databases/delete_import/<?=$db->id?>" class="red" rel="facebox">Delete Import</a>)</li>
			<?php } else if($db->status == 5){?>
				<li><?=$db->name?>
				(<a href="<?=site_url()?>databases/delete_import_review/<?=$db->id?>" class="red" rel="facebox">Import failed</a>)</li>
			<?php }
			?>
	<?php 	    endif; 
				//end db ajax view
                	 } //endfor
			}

		}
	}

}

?>
