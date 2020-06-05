<?php
class Location_model extends CI_Model {
	function __construct() {
		parent::__construct();
		$this->load->database();
		//indexes: id, review_interval_id, database_id
	}
  
	function get_by_id($loc_id, $org_id=NULL){

		/* 		
			If we don't specify and organization id, just get the location..otherwise, lets only get the location if it matches the organization 
		*/
		if($org_id == NULL) {
			$query = $this->db->get_where('locations', array('id' => $loc_id));
			return $query->row();
		} else {
			$this->db->select('locations.*');
			$this->db->from('locations');
			$this->db->join('databases', 'databases.id = locations.database_id', 'left');
			$this->db->where('databases.organization_id', $org_id);
			$this->db->where('locations.id', $loc_id);
			
	
			return $this->db->get()->row();	
		}
	}

	function log_modifications($location_id,$user_id=null) {

		$time=time();
		$data = array(
			'last_edited_time' => $time,
			'last_edited_by' => $user_id
			);

		$this->db->where('id',$location_id);
		$insert_id=$this->db->update('locations',$data);
		return $insert_id;
	}
  
  function get_first_open_slot($loc_id){
	  $location = $this->get_by_id($loc_id);
    $range = range(1,$location->capacity);
   	$maximum_pages=ceil( $location->capacity / $this->config->item('page_size') ); 
  	//$items = $this->Item_model->get_from_location($loc_id,1);
	$this->db->select('items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into, categories.name as category')->from('items')->where(array('location_id' => $loc_id));
	$this->db->join('pending_transfers', 'pending_transfers.item_id = items.id', 'left');
	$this->db->join('categories','categories.id = items.category_id', 'left');
	$items = $this->db->get();

    foreach($items->result() as $item){
    	$slots[]=$item->slot_num;
    }
    
    if(isset($slots)){
	    //array_merge is used to force the array to be 0 indexed
  	  $empty_slots = array_merge(array_diff($range, $slots));
  	  return $empty_slots[0];
  	 } else {
  	 	return 1;
  	 }
    
  }
  
	function is_slot_open($loc_id, $slot_num) {
		
		$this->db->where('location_id', $loc_id);
		$this->db->where('slot_num', $slot_num);
		
		if( $this->db->get('items')->num_rows() == 0 ) {
			return true;
		} else {
			return false;
		}
  }
  
  function get_by_ids($ids){
    	$this->db->where_in('id', $ids);
    	return $this->db->get('locations');
    }
  
  function get_from_database(){
  	//select * from locations where database_id = 123
  	global $user_data;
    $this->db->order_by("name","asc");
    return $this->db->get_where('locations', array('database_id' => $this->session->userdata('database_id')));
  }
  function get_from_network_database($database_id=null){
	    global $user_data; 
	    $query="select locations.id,locations.database_id,locations.date_added,locations.name,locations.capacity,locations.num_items,locations.empty_slots,locations.next_review,locations.review_interval_num,locations.description, locations.review_interval_string,location_permissions.organization_id, location_permissions.user_id, location_permissions.pto_umask from `locations`,`location_permissions` where locations.id=location_permissions.location_id and locations.database_id='".$database_id."' and location_permissions.pto_umask > 0 and location_permissions.user_id='".$user_data['id']."' order by locations.name asc";
            $locs=$this->db->query($query);
	    return $locs;
    }
  function get_from_network_database_readonly($database_id=null){
	    global $user_data; 
	    $query="select locations.id,locations.database_id,locations.date_added,locations.name,locations.capacity,locations.num_items,locations.empty_slots,locations.next_review,locations.review_interval_num,locations.description, locations.review_interval_string,location_permissions.organization_id, location_permissions.user_id, location_permissions.pto_umask from `locations`,`location_permissions` where locations.id=location_permissions.location_id and locations.database_id='".$database_id."' and ( location_permissions.pto_umask = 1 or location_permissions.pto_umask =3 ) and location_permissions.user_id='".$user_data['id']."' order by locations.name asc";
            $locs=$this->db->query($query);
	    return $locs;
    }
  
  function get_by_database($db_id){
    return $this->db->get_where('locations', array('database_id' => $db_id));
  }

 function insert() {
  	//insert into locations (date_added, name, capacity, num_items, empty_slots, next_review, review_interval_id, description, database_id) VALUES ($values)
  	
  	
  	if($this->input->post('review_interval')==0){
  		$interval = "weeks";
		$review_num = 1;
  	} else if ($this->input->post('review_interval')==1){
  		$interval = "weeks";
		$review_num = 2;
  	} else if ($this->input->post('review_interval')==2){
  		$interval = "months";
		$review_num = 1;
  	} else if ($this->input->post('review_interval')==3){
  		$interval = "months";
		$review_num = 3;
  	} else if ($this->input->post('review_interval')==4){
  		$interval = "months";
		$review_num = 6;
  	} else if ($this->input->post('review_interval')==-1){
		$interval = 'bad_input';
		$review_num = 'bad_input';
		
  	}
  	$next_review=0;	
  	if(($this->input->post('interval_frequency') == "") && ($this->input->post('review_interval') > -1)){
  		$next_review = strtotime('+'.$review_num." ".$interval);
  	} else 	if($this->input->post('interval_frequency') != "Specific Date..."){
			if($this->input->post('interval_frequency') != "")
			{
				$interval_frequency_arr=explode('/',$this->input->post('interval_frequency'));
				$next_review=mktime(0,0,0,$interval_frequency_arr[0],$interval_frequency_arr[1],$interval_frequency_arr[2]);
			}
	}
  	
		$data = array(
							 'date_added' => time(),
               'name' => $this->input->post('name'),
               'capacity' => $this->input->post('capacity'),
               'num_items' => 0,
               'empty_slots' => $this->input->post('capacity'),
               'next_review' => $next_review,
               'review_interval_num' =>$review_num,
               'review_interval_string'=>$interval,
               'database_id' => $this->session->userdata('database_id'),
               'description' => $this->input->post('description')
    );
    $this->db->insert('locations', $data);
    return array($this->db->insert_id());
  }
  
  function getLastItem($location_id){
  	$this->db->select_max('slot_num')->where('location_id', $location_id);
  	return $this->db->get('items')->row();
  }
  
  function update($location_id){
  	$this->db->trans_start();
  	$this_location = $this->get_by_id($location_id);
  	
  	if($this->input->post('capacity') < $this_location->capacity){
			$new_empty_slots = $this_location->empty_slots - ($this_location->capacity - $this->input->post('capacity'));
  	} else if($this->input->post('capacity') > $this_location->capacity){
  		$new_empty_slots = $this_location->empty_slots + ($this->input->post('capacity') - $this_location->capacity);
  	} else {
  		$new_empty_slots = $this_location->empty_slots;
  	}
  	
  	if($this->input->post('review_interval')==0){
  		$interval = "weeks";
		$review_num = 1;
  	} else if ($this->input->post('review_interval')==1){
  		$interval = "weeks";
		$review_num = 2;
  	} else if ($this->input->post('review_interval')==2){
  		$interval = "months";
		$review_num = 1;
  	} else if ($this->input->post('review_interval')==3){
  		$interval = "months";
		$review_num = 3;
  	} else if ($this->input->post('review_interval')==4){
  		$interval = "months";
		$review_num = 6;
  	} else if ($this->input->post('review_interval')==-1){
		$interval = 'bad_input';
		$review_num = 'bad_input';
		
  	}
  	$next_review=0;	
  	if(($this->input->post('interval_frequency') == "Specific Date...") && ($this->input->post('review_interval') > -1)){
  		$next_review = strtotime('+'.$review_num." ".$interval);
  	} else 	if($this->input->post('interval_frequency') != "Specific Date..."){
			if($this->input->post('interval_frequency') != "")
			{
				$interval_frequency_arr=explode('/',$this->input->post('interval_frequency'));
				$next_review=mktime(0,0,0,$interval_frequency_arr[0],$interval_frequency_arr[1],$interval_frequency_arr[2]);
			}
	}
  	
  	$data = array(
             'name' => $this->input->post('name'),
             'capacity' => $this->input->post('capacity'),
             'empty_slots' => $new_empty_slots,
             'description' => $this->input->post('description'),
             'review_interval_string'=>$interval,
             'review_interval_num'=>$review_num,
             'next_review'=> $next_review
    );
    
    $this->db->where('id', $location_id);
		$this->db->update('locations', $data);
		$this->db->trans_complete();
  	return array($location_id);
  }
  
  //assumptions here: location is full, no empty slots, $this->input->post('new_capacity') is a positive integer of the number of slots to add to the loc
  function update_capacity($location_id){
	  $this->db->trans_start();
  	$this_location = $this->get_by_id($location_id);
  	$data = array(
  		'capacity'=>$this_location->capacity+$this->input->post('new_capacity'),
  		'empty_slots'=>$this->input->post('new_capacity')
  	);
  	$this->db->where('id', $location_id);
		$this->db->update('locations', $data);
		$this->db->trans_complete();
  }
  
  function delete($locs){
  	$this->db->trans_start();
  	$this->db->where_in('id', $locs)->delete('locations');
  	$this->db->where_in('location_id', $locs)->delete('items');
  	$this->db->trans_complete();
  }
  
	function get_all_from_organization(){
	    $user_data = $this->session->userdata('user_data');
	    
		$this->db->select('id');
		$this->db->from('databases');
	    $this->db->where('organization_id', $user_data['organization_id']);
		//$sub_query = $this->db->_compile_select();
				
		//$this->db->_reset_select();

		$sub_query = $this->db->get_compiled_select();

		
		$this->db->where('database_id IN ('.$sub_query.')'); //gotta do it this way other than where_in to avoid it from espcaing sub_query
		
		return $this->db->get('locations');
	}

  function restore_from_trash($location_id) {
	  $this->db->trans_start();
	  $this_location = $this->get_by_id($location_id);
	  $num_items = $this_location->num_items + 1;
	  $empty_slots = $this_location->empty_slots - 1;

	  $data = array(
	  	'num_items'	=>	$num_items,
		'empty_slots'	=>	$empty_slots
  		);
	  $this->db->where('id',$location_id);
	  $this->db->update('locations',$data);
	  $this->db->trans_complete();
	  return array($location_id);
  }
}
?>
