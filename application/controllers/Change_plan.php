<?php
class Change_plan extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'file'));
		$this->load->model(array('Import_model','User_model','Organization_model','Database_model','Assign_model','Logging_model','Changed_plan_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
		include APPPATH.'helpers/mcapiclass_helper.php';  
	}
	
	function index(){
		redirect('/');		
	}

	function do_change(){
		  if($this->authentication->is_authenticated())
		  {
			if(!is_numeric($this->input->post('org_id'))){
							$this->session->set_flashdata('error', "Only Organization IDS go into the little box!");
							redirect('change_plan/plans');
			}
			  global $user_data;
		          $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			  $changed_plans=$this->Changed_plan_model->get_all();
				$data = array(
								'user_data' => $user_data, 
								'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
								'is_network' => $this->Network_model->is_network($package_id),
								'packages' => $this->Package_model->get_by_id($package_id),
								'changed_plans' => $changed_plans,
								'package_plans' => $this->Package_model->get_all()
				);
			if(($user_data['id'] == 3) || ($user_data['id'] == 6) || ($user_data['id'] == 249489)) {
				if($this->input->post('submit') !== NULL)
				{
					if($this->input->post('org_id') != NULL)
					{
						$old_plan_id=$this->Organization_model->get_package_id($this->input->post('org_id'));
						$data['select']=$this->input->post('select');
						$data['org_id']=$this->input->post('org_id');
						$data['old_plan_id']=$old_plan_id;

						//change_plan
						
						if(is_numeric($this->input->post('org_id'))){
							$insert_id=$this->Changed_plan_model->change_plan($this->input->post('org_id'),$this->input->post('select'),$old_plan_id,$user_data['id']);

							//mailchimp for changed   plans
							//
							$api_key='1ddf218cb55d3d51f6be533b72fc81ae-us1';
							$listId = "a31d451a83";

						$get_users=$this->User_model->get_user_by_organization($this->input->post('org_id'))->row();	
						$api = new MCAPI($api_key);
						$fname=$get_users->first_name;
						$lname=$get_users->last_name;
						$my_email=$get_users->email_address;
						$company=$this->Organization_model->get_org_by_id($this->input->post('org_id'))->row()->name;
						$groupings = array (
								'Contact Type' => array (
								'name' => 'Contact Type',
								'groups' => $this->Package_model->get_by_id($this->input->post('select'))->row()->mailing_list,
							)
									);
							$time_now=time();
							$merge_vars = array('FNAME'=>$fname, 'LNAME'=>$lname, 'COMPA'=>$company, 'DPRCH'=>$time_now);
							$merge_vars['GROUPINGS'] = $groupings;
							$retval = $api->listSubscribe( $listId, $my_email, $merge_vars,'html', 'false', 'true' );



						} else {
							$this->session->set_flashdata('error', "Only Organization IDS go into the little box!");
							redirect('change_plan/plans');
						}

						if($insert_id) {
							$this->session->set_flashdata('success', "Plan successfully changed");
							redirect('change_plan/plans');
						}else {
							$this->session->set_flashdata('error', "Something went wrong somewhere..Get technical help!");
							redirect('change_plan/plans');
						}

					}
					else
					{
						$this->session->set_flashdata('error', "Please input Organization ID");
						redirect('change_plan/plans');
					}

				}
				else
				{
					$this->session->set_flashdata('error', "Select Organization to Change Plans.");
					redirect('change_plan/plans');
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
	
	function plans(){
		  if($this->authentication->is_authenticated())
		  {
			  global $user_data;
		          $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
			  $changed_plans=$this->Changed_plan_model->get_all();
				$data = array(
								'user_data' => $user_data, 
								'packages' => $this->Package_model->get_by_id($package_id),
								'is_network' => $this->Network_model->is_network($package_id),
								'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
								'changed_plans' => $changed_plans,
								'org_id' => $this->uri->segment('3'),
								'package_plans' => $this->Changed_plan_model->get_packages()
				);
			if(($user_data['id'] == 3) || ($user_data['id'] == 6) || ($user_data['id'] == 249489)) {
				$this->load->view('header', $data);
				$this->load->view('plans', $data);
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
								'is_network' => $this->Network_model->is_network($package_id),
								'organization_admin' => $this->Permissions_model->get_organization_admin($user_data['organization_id']),
								'packages' => $this->Package_model->get_by_id($package_id),
								'assigned_databases' => $assigned_databases,
								'databases' => $this->Database_model->get_from_organization()
				);
			if(($user_data['id'] == 3) || ($user_data['id'] == 6) || ($user_data['id'] == 249489)) {
				if($this->input->post('submit') !== NULL) {
					$data['org_name']=$this->input->post('org_name');
					$data['organizations']=$this->Assign_model->get_by_email($this->input->post('org_name'));
					$this->load->view('header', $data);
					$this->load->view('get_org_by_name_plan', $data);
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

}

?>
