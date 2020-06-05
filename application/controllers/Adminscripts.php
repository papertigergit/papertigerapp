<?php
class Adminscripts extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		if( $this->user_data['id'] != 105 )
		{
			redirect('/');
			return;
		}	
	}
	
	function item_org_ids() {
		$this->authentication->require_authentication();	

		$this->db->select('items.id,items.name,databases.organization_id');
		$this->db->from('items');
		$this->db->where('items.organization_id', 0);
		$this->db->join('locations', 'locations.id=items.location_id', 'left');
		$this->db->join('databases', 'databases.id=locations.database_id', 'left');
		
		$query = $this->db->get();

		foreach($query->result() as $item) {
		
			$this->db->where('id', $item->id);
			$this->db->update('items', array('organization_id'=>$item->organization_id));
		
		}



		$data['user_data'] = $this->user_data;
		$this->load->view('header', $data);
		$this->load->view('adminscripts/item_org_ids', $data);
		$this->load->view('footer');
	}
}
?>
