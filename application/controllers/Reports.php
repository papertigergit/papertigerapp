<?php
ini_set('memory_limit','128M');

	class Reports extends MY_Controller{
	
		function __construct(){
			parent::__construct();
			global $user_data;
			
			$this->load->library(array('session', 'authentication'));
			$this->load->helper(array('url', 'empty_messages'));
			$this->load->database();
			$this->load->model(array('Category_model', 'Item_model', 'Location_model', 'Transfers_model', 'Reports_model', 'Permissions_model'));			
			$user_data = $this->session->userdata('user_data');
		}
		
		function index(){
			$this->authentication->require_authentication();
            global $user_data;
            $database_id=$this->session->userdata('database_id');
            $network_utils = array();
            $network_utils = enable_buttons();
			$data['body_id'] = 'reports';
            $data['user_data'] = $user_data;
                                    $data['admin_email']= $network_utils['admin_email'];
                                    $data['admin_name'] = $network_utils['admin_name'];

            $package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
            $is_network = $this->Network_model->is_network($package_id);
            $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

			if($is_network->num_rows() == 0):
				$locations=$this->Location_model->get_from_database();
			endif;
			if($is_network->num_rows() > 0):
				$locations = $this->Location_model->get_from_network_database($database_id);
            endif;
            if(($is_network->num_rows() > 0) && ($organization_admin == $this->user_data['id'])):
				$locations = $this->Location_model->get_from_database();
            endif;
            $data['locations'] = $locations;
            // $data['locations'] = $this->Location_model->get_from_database();

			$this->load->view('header', $data);
			$this->load->view('reports/reports',$data);
            $this->load->view(empty_message_view($database_id), $data); //This loads the corresponding empty views!
			$this->load->view('footer',$data);

		}	
		function generate(){
			$this->authentication->require_authentication();
			global $user_data;
			//Empty all session variables
			$this->session->set_userdata('location_file_list','');	
			$this->session->set_userdata('category_file_list','');	
			$this->session->set_userdata('date_range','');	
			$this->session->set_userdata('sort_by','');	
			$this->session->set_userdata('keywords','');	
			$this->session->set_userdata('notes','');	
			$this->session->set_userdata('category','');	
			$this->session->set_userdata('action_date','');	
			$this->session->set_userdata('after_date','');
			$this->session->set_userdata('before_date','');

			if($this->input->post('submit')=="")
			{
				$this->session->set_flashdata('error',"You must select a Report to Generate first");
				redirect('reports');
			}
			if($this->input->post('report_type')=="transfer")
			{
				$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
				$is_network=$this->Network_model->is_network($package_id);
				$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

					if($is_network->num_rows() == 0):
						$pending_transfers=$this->Transfers_model->get_from_database();	
					endif;
					if($is_network->num_rows() > 0):
						$pending_transfers=$this->Transfers_model->get_from_network_database();	
					endif;
					if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
						$pending_transfers=$this->Transfers_model->get_from_database();	
					endif;
				if($pending_transfers->num_rows() == 0)
				{
					$this->session->set_flashdata('error',"You have no pending transfers");
					redirect('reports');
				}
				$data['user_data']=$user_data;
				$data['table_data']=$pending_transfers;
				$this->load->view('reports/transfers',$data);	
			}
			if($this->input->post('report_type')=="file_list")
			{
				$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
				$is_network=$this->Network_model->is_network($package_id);
				$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

					if($is_network->num_rows() == 0):
						$locations=$this->Location_model->get_from_database();
					endif;
					if($is_network->num_rows() > 0):
						$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));				
					endif;
					if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
						$locations=$this->Location_model->get_from_database();
					endif;
				$total_locations=$locations->num_rows();
				if($total_locations == 0)
				{
					$this->session->set_flashdata('error',"You don't have any locations to generate reports in yet");
					redirect('reports');
				}
				if($this->input->post('location_file_list')){
					$this->session->set_userdata('location_file_list',$this->input->post('location_file_list'));
				}
				if($this->input->post('category_file_list')){
					$this->session->set_userdata('category_file_list',$this->input->post('category_file_list'));
				}
				if($this->input->post('date_range')){
					$this->session->set_userdata('date_range',$this->input->post('date_range'));
				}
				if($this->input->post('sort')){
					$this->session->set_userdata('sort_by',$this->input->post('sort'));
				}
				if($this->input->post('keywords')){
					$this->session->set_userdata('keywords',$this->input->post('keywords'));
				}
				if($this->input->post('notes')){
					$this->session->set_userdata('notes',$this->input->post('notes'));
				}
				if($this->input->post('category')){
					$this->session->set_userdata('category',$this->input->post('category'));
				}
				if($this->input->post('action_date')){
					$this->session->set_userdata('action_date',$this->input->post('action_date'));
				}

				if($this->input->post('date_range')){
					$date_range=explode("-",$this->input->post('date_range'));
						if(sizeof($date_range)==1){
					$date_range[1]=$date_range[0];
					}
					$after_date=(explode("/",$date_range[0]));
					$after_date[2]=intval($after_date[2]);
					$after_date=mktime(0,0,0,$after_date[0],$after_date[1],$after_date[2]);
					$this->session->set_userdata('after_date', $after_date);
	
					$before_date=(explode("/",$date_range[1]));
					$before_date=mktime(23,59,59,$before_date[0],$before_date[1],$before_date[2]);
					$this->session->set_userdata('before_date', $before_date);
				}

				$locations = $this->Location_model->get_from_database();
				$grid_data=$this->Reports_model->file_list_report();
				$file_list_data=$this->Reports_model->file_list_report_locs();
				$data = array (
							'grid_data' => $grid_data,
							'user_data' => $user_data,
							'locations' => $locations,
							'file_list_data' => $file_list_data
				
				);
				$this->load->view('reports/file_list_report',$data);	

			}
			if($this->input->post('report_type')=="action_date")
			{

				$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
				$is_network=$this->Network_model->is_network($package_id);
				$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

					if($is_network->num_rows() == 0):
						$locations=$this->Location_model->get_from_database();
					endif;
					if($is_network->num_rows() > 0):
						$locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));				
					endif;
					if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
						$locations=$this->Location_model->get_from_database();
					endif;

				$total_locations=$locations->num_rows();
				if($total_locations == 0)
				{
					$this->session->set_flashdata('error',"You don't have any locations to generate reports in yet");
					redirect('reports');
				}
				if($this->input->post('location_file_list')){
					$this->session->set_userdata('location_file_list',$this->input->post('location_file_list'));
				}
				if($this->input->post('category_file_list')){
					$this->session->set_userdata('category_file_list',$this->input->post('category_file_list'));
				}
				if($this->input->post('date_range')){
					$this->session->set_userdata('date_range',$this->input->post('date_range'));
				}
				if($this->input->post('action_sort')){
					$this->session->set_userdata('sort_by',$this->input->post('action_sort'));
				}
				if($this->input->post('action_date')){
					$this->session->set_userdata('action_date',$this->input->post('action_date'));
				}

				if($this->input->post('date_range')){
					$date_range=explode("-",$this->input->post('date_range'));
						if(sizeof($date_range)==1){
					$date_range[1]=$date_range[0];
					}
					$after_date=(explode("/",$date_range[0]));
					$after_date[2]=intval($after_date[2]);
					$after_date=mktime(0,0,0,$after_date[0],$after_date[1],$after_date[2]);
					$this->session->set_userdata('after_date', $after_date);
	
					$before_date=(explode("/",$date_range[1]));
					$before_date=mktime(23,59,59,$before_date[0],$before_date[1],$before_date[2]);
					$this->session->set_userdata('before_date', $before_date);
				}
				else {
					$after_date=1;
					$before_date=2147483647;
					$this->session->set_userdata('after_date', $after_date);
					$this->session->set_userdata('before_date', $before_date);
				}

				$locations = $this->Location_model->get_from_database();
				$grid_data=$this->Reports_model->file_list_report();
				$file_list_data=$this->Reports_model->file_list_report_locs();
				$data = array (
							'grid_data' => $grid_data,
							'user_data' => $user_data,
							'locations' => $locations,
							'file_list_data' => $file_list_data
				
				);
				$this->load->view('reports/action_date_report',$data);	
			}

			if($this->input->post('report_type')=="file_cleanout")
			{
				$num_of_locations=$this->Location_model->get_from_database();
				$total_locations=$num_of_locations->num_rows();
				if($total_locations == 0)
				{
					$this->session->set_flashdata('error',"You don't have any locations to generate reports in yet");
					redirect('reports');
				}
				if($this->input->post('location_file_list')){
					$this->session->set_userdata('location_file_list',$this->input->post('location_file_list'));
				}
				if($this->input->post('category_file_list')){
					$this->session->set_userdata('category_file_list',$this->input->post('category_file_list'));
				}
				if($this->input->post('date_range')){
					$this->session->set_userdata('date_range',$this->input->post('date_range'));
				}
				if($this->input->post('cleanout_sort')){
					$this->session->set_userdata('sort_by',$this->input->post('cleanout_sort'));
				}
				if($this->input->post('action_date')){
					$this->session->set_userdata('action_date',$this->input->post('action_date'));
				}

				if($this->input->post('date_range')){
					$date_range=explode("-",$this->input->post('date_range'));
						if(sizeof($date_range)==1){
					$date_range[1]=$date_range[0];
					}
					$after_date=(explode("/",$date_range[0]));
					$after_date[2]=intval($after_date[2]);
					$after_date=mktime(0,0,0,$after_date[0],$after_date[1],$after_date[2]);
					$this->session->set_userdata('after_date', $after_date);
	
					$before_date=(explode("/",$date_range[1]));
					$before_date=mktime(23,59,59,$before_date[0],$before_date[1],$before_date[2]);
					$this->session->set_userdata('before_date', $before_date);
				}

				$locations = $this->Location_model->get_from_database();
				$grid_data=$this->Reports_model->file_list_report();
				$file_list_data=$this->Reports_model->file_list_report_locs();
				$data = array (
							'grid_data' => $grid_data,
							'user_data' => $user_data,
							'locations' => $locations,
							'file_list_data' => $file_list_data,
							'location_file_list' => $this->input->post('location_file_cleanout')
				
				);
                               $this->load->view('reports/file_cleanout_report',$data);	
			}
		}
	}
?>
