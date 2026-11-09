<?php
class Package_model extends MY_Model {
	
	function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	function get_package($package_id, $field='id') {
		$this->db->where($field, $package_id);
		return $this->db->get('packages')->row();
	}
	function get_year_price($package_id) {
		$this->db->where('id',$package_id);
		$groups=$this->db->get('packages')->row()->groups;
	//	echo "xxxxxxxx";
	//	exit(var_dump($groups));
		$this->db->where('groups',$groups);
		$this->db->where('billing_cycle',"year");

		$price=$this->db->get('packages')->row();
		if($price):
			return $price->price;
		endif;
		if(!$price):
			return "0.00";
		endif;	
	}

	function get_by_name($name) {
		$this->db->where("name", $name);
		return $this->db->get('packages');
	}
	function get_by_groups($group_id) {
		$this->db->where("groups", $group_id);
		return $this->db->get('packages');
	}
	function get_by_period($plan_name) {
		$this->db->where("name", $plan_name);
		return $this->db->get('packages')->row();
	}
	function get_package_level($package_id) {
		$this->db->where('id', $package_id);
		$query = $this->db->get('packages');
		
		return $query->row()->level;
	}
	function get_by_id($package_id) {
		$this->db->where('id',$package_id);
		return $this->db->get('packages');
	}
	
	function get_all() {
		$this->db->order_by('level', 'asc');
		return $this->db->get('packages');
	}
	
	function group_by_id() {
		return $this->db->query('select count(id) as total,package_id from organizations group by package_id');
	}
	function check_network($package_id) {
		$this->db->where('id',$package_id);
		$this->ci31_where_not_in('max_users',1);
		return $this->db->get('packages');
	}
	//$package_id = package organization is currently on
	// this is based on an assigned level for each package.
	function get_downgrade_packages($package_id) {
	
		$level = $this->get_package_level($package_id);
		
		$this->db->order_by('level', 'desc');
		$this->db->where('level <', $level);
		$this->db->where('id !=', 1);
		return $this->db->get('packages');
	}
	
	//$package_id = package organization is currently on	
	function get_upgrade_packages($package_id) {

		$level = $this->get_package_level($package_id);
		
		$this->db->order_by('level');	
		$this->db->where('level >', $level);
		return $this->db->get('packages');
	}
	
}

?>
