<?php
class Import_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
    }
    
    function get_or_make_location($db_id, $loc_name,$description,$review_frequency){
    	if(strtolower($loc_name)=='trash'){
    		return 0;
    	}
   		global $slots_arr; 	
	    //do logic for revie frequencies imports
		$next_review='';
		$review_interval_num='';
		$review_interval_string='';

	    if( $review_frequency == "None" || $review_frequency == "" || $review_frequency == null )
	    {
		    $next_review=0;
		    $review_interval_num=0;
		    $review_interval_string="bad_input";
	    }
	    else if (strpbrk($review_frequency,'week'))
	    {
		    $review_dates=explode(" ",$review_frequency);
		    $next_review=strtotime('+'.$review_frequency);
		    $review_interval_num=$review_dates[0];
		    $review_interval_string=$review_dates[1];
	    }
	    else if (strpbrk($review_frequency,'month'))
	    {
		    $review_dates=explode(" ",$review_frequency);
		    $next_review=strtotime('+'.$review_frequency);
		    $review_interval_num=$review_dates[0];
		    $review_interval_string=$review_dates[1];
	    }
	    else
	    {
		    $review_dates=explode('/',$review_frequency);
		    if(count($review_dates)<3) {
			$next_review=0;
			} else {
			    $next_review=mktime(0,0,0,$review_dates[0],$review_dates[1],$review_dates[2]);
			}
		    $review_interval_num=0;
		    $review_interval_string="bad_input";
	    }

	    $this->db->trans_start();
		$loc_name = urldecode($loc_name);
    	$query = $this->db->get_where('locations', array('name' => $loc_name, 'database_id'=>$db_id));
    	if($query->num_rows()> 0){
    		$loc_id = $query->row()->id;
    	} else {
    		$data = array(
    						'date_added'	=> time(),
    						'name'			=> $loc_name,
    						'capacity'		=> 0,
    						'num_items'		=> 0,
    						'empty_slots'	=> 0,
    						'next_review'	=> $next_review,
    						'review_interval_num'	=> $review_interval_num,
    						'review_interval_string' => $review_interval_string,
    						'database_id'	=> $db_id,
    						'description'	=> $description 
    		);
    		$this->db->insert('locations', $data);
    		$loc_id = $this->db->insert_id();
		$slots_arr[$loc_id] = 1;
    	}  
		$this->db->trans_complete();
		return $loc_id;
    }
    
    function get_or_make_category($db_id, $cat_name,$description) {
	$category_id = 0;
    	$this->db->trans_start();
    	$query = $this->db->get_where('categories', array('name' => $cat_name, 'database_id'=>$db_id));
    	if($query->num_rows()> 0){
    		$category_id = $query->row()->id;
    	} else {
    		$data = array(
    						'date_added'	=> time(),
    						'name'			=> urldecode($cat_name),
    						'database_id'	=> $db_id,
    						'description'	=> $description 
    		);
    		$this->db->insert('categories', $data);
    			$category_id = $this->db->insert_id();
    	}
    	$this->db->trans_complete();
	return 	$category_id;
    }
    
    //inserting imported items

    function insert_item($location_id, $category_id, $data_arr, $database_id, $org_id) {
	    global $slots_arr;
	    $this->db->trans_start();
	    if($location_id == 0){
	    	if(trim($data_arr[7])==''){			//making sure all action dates are null and not '0'
	    		$actionDate = null;
	    	} else {
			//print_r($data_arr)
	    		$actionDate = strtotime($data_arr[7]);
	    	}
	    	$data = array(
	    		'item_id' => 0,
	    		'database_id'	=> $database_id,
	    		'date_added'	=> time(),
	    		'name'				=>urldecode($data_arr[3]),
	    		'keywords'		=> urldecode($data_arr[4]),
	    		'category_id'	=> $category_id,
	    		'previous_location_id'	=> -1,
	    		'description'	=> urldecode($data_arr[5]),
	    		'action_date'	=> $actionDate
    		);
    		$this->db->insert('trash', $data);
	    } else if($data_arr[3]=='(open)'){
    		$this->db->set('empty_slots', 'empty_slots+1', false)->set('capacity', 'capacity+1', false)->where('id', $location_id);
		    $this->db->update('locations');
		    $slots_arr[$location_id] = $slots_arr[$location_id]+1;
    	} else {
    		if(trim($data_arr[7])==''){
	    		$actionDate = null;
	    	} else {
	    		$actionDate = strtotime($data_arr[7]);
	    	}

    		$data = array(
    				'date_added'	=> time(),
    				'name'				=> urldecode($data_arr[3]),
    				'keywords'		=> urldecode($data_arr[4]),
    				'category_id'	=> $category_id,
    				'location_id'	=> $location_id,
    				'transfer_status_id'	=> 0,
    				'description'	=> urldecode($data_arr[5]),
    				'action_date'	=> $actionDate,
    				'slot_num'	=> $data_arr[10],
    				'organization_id'	=> $org_id
    		);
    		$this->db->insert('items', $data);
    		
    		$this->db->set('num_items', 'num_items+1', false)->set('capacity', 'capacity+1', false)->where('id', $location_id);
		    $this->db->update('locations');
		    $slots_arr[$location_id] = $slots_arr[$location_id]+1;
    	}

    	$this->db->trans_complete();

    }

}
?>
