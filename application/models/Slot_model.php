<?php
class Slot_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
			//indexes: id, location_id
    }
}
?>