<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Logging Library
*/

class Logger {

    //private
    var $CI;
    var $user;

    function __construct()
    {
        $this->CI =& get_instance();    
        
        $this->CI->load->library('postmark');
        
        $this->user = $this->CI->session->userdata('user_data');
    }

	// --------------------------------------------------------------------

    function log_message($data) {
    	if( !isset($data['type']) ) {
    		$data['type'] = 'message';
    	}
    	    
		$this->CI->db->insert('logs', array('date' => time(), 'organization_id' => $this->user['organization_id'], 'user_id' => $this->user['id'], 'type' => $data['type'], 'action' => $data['message']));
		
		if($data['type'] == 'error') 
		{		
			$this->CI->postmark->to('fholgado@thepapertiger.com');			
			$this->CI->postmark->cc('ptapp@turesweb.com');
			$this->CI->postmark->subject('Error reported by PT App');
			$this->CI->postmark->message_html($this->CI->db->last_query());
/* 			$this->CI->postmark->send(); */
		}		
	}
}

// END Logger library

/* End of file Logger.php */
/* Location: ./system/application/libraries/Logger.php */