<?php
class Tutorials extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		global $user_data;
		$user_data = $this->session->userdata('user_data');

	}
	
	function index() {
		$this->authentication->require_authentication();
		global $user_data;
		$this->load->view('tutorial');
	}
}
?>
