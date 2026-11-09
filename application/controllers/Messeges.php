<?php
class Messeges extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('authentication', 'session');
		$this->load->helper(array('url', 'text', 'file'));
		$this->load->model(array('Import_model','User_model','Organization_model','Database_model','Assign_model','Logging_model','Changed_plan_model','Messeges_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
		
		
	}
	
	function index(){
		redirect('/');		
	}
	
	function show(){
		  if($this->authentication->is_authenticated())
		  {
			  global $user_data;
			  $packages=$this->Package_model->get_all();
			  $messeges=$this->Messeges_model->get_all();
				$data = array(
								'user_data' => $user_data, 
								'packages' => $packages,
								'messeges' => $messeges
				);
			if(($user_data['id'] == 3) || ($user_data['id'] == 1) || ($user_data['id'] == 105) || ($user_data['id'] == 204) || ($user_data['id'] == 6) || ($user_data['id'] == 1862)) {
				$this->load->view('header', $data);
				$this->load->view('messeges', $data);
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
	function delete()
	{
		  if($this->authentication->is_authenticated())
		  {
			$msg_id=$this->uri->segment('3');
			if($msg_id):
				$delete_id=$this->Messeges_model->delete($msg_id);
				$this->session->set_flashdata('success', "Message deleted successfully");
				redirect('messeges/show');
			endif;	
			if(!$msg_id):
				$this->session->set_flashdata('error', "There was a problem deleting the messege");
				redirect('messeges/show');
			endif;	

		  }
		  else {
				redirect('/');
		  }
	}
	function activate()
	{
		  if($this->authentication->is_authenticated())
		  {
			  $plan_id=$this->input->post('select');
			  $msg_id=$this->input->post('msg_id');
			  $activate=$this->Messeges_model->activate($plan_id,$msg_id);
			  if($activate):
					$this->session->set_flashdata('success', "Message activated successfully");
					redirect('messeges/show');
			  endif;
			  if(!$activate):
					$this->session->set_flashdata('error', "Error, plan not activated");
					redirect('messeges/show');
			  endif;
		  }
		  else {
				redirect('/');
		  }
	}
	function deactivate()
	{
		  if($this->authentication->is_authenticated())
		  {
			  $plan_id=$this->input->post('plan_id');
			  
			  $activate=$this->Messeges_model->deactivate_plan($plan_id);
			  if($activate):
					$this->session->set_flashdata('success', "Message deactivated successfully");
					redirect('messeges/show');
			  endif;
			  if(!$activate):
					$this->session->set_flashdata('error', "Error, plan not deactivated");
					redirect('messeges/show');
			  endif;
		  }
		  else {
				redirect('/');
		  }
	}
	function insert(){
		  if($this->authentication->is_authenticated())
		  {
			  $title=$this->input->post('title');
			  $body=$this->input->post('body');
			  $link_anchor=$this->input->post('link_anchor');
			  $text=$this->input->post('text');
			  $insert_id=$this->Messeges_model->insert_values($title,$body,$link_anchor,$text);
			  if($insert_id):
					$this->session->set_flashdata('success', "Message inserted successfully");
					redirect('messeges/show');
			  endif;
			  if(!$insert_id):
					$this->session->set_flashdata('error', "Message not inserted");
					redirect('messeges/show');
			  endif;
				  
		  }
		  else {
				redirect('/');
		  }
	}

}

?>
