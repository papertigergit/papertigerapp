<?php
class Authentication_model extends CI_Model {

    function __construct() {
		parent::__construct();
    }
	    
	function get_failed_attempts($email_address=NULL, $interval="3 minute") {
		$this->db->where('(FROM_UNIXTIME(date) > DATE_SUB(NOW(), INTERVAL '.$interval.')) AND (email_address=\''.$email_address.'\' OR ip_address=\''.$this->input->ip_address().'\')');
		return $this->db->get('failed_logins');
	}
    

}
?>
