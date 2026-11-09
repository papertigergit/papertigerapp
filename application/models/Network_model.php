<?php
class Network_model extends MY_Model {
    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    function is_network($package_id) {
			$this->db->where('id',$package_id);
			$this->db->where('max_users <>',1);
			return $this->db->get('packages');
    }
}
?>
