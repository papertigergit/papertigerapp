<?php
class Item_model extends MY_Model {

	function __construct() {
		parent::__construct();
		$this->load->database();
		//indexes: id, category_id
	}

	function log_modifications($item_id,$user_id=null) {

		$time=time();
		$data = array(
			'last_edited_time' => $time,
			'last_edited_by' => $user_id
			);

		$this->db->where('id',$item_id);
		$insert_id=$this->db->update('items',$data);
		return $insert_id;
	}
	function get_item_by_slot($location_id=NULL,$slot_num=NULL) {
		$this->db->where('location_id',$location_id);
		$this->db->where('slot_num',$slot_num);
		return $this->db->get('items')->row();
	}

	function get_by_id($item_id,$organization_id=NULL) {
	
		if( $organization_id !== NULL ) {
			$this->db->where('organization_id', $organization_id);
		}
		
		$query = $this->db->get_where('items', array('id'=>$item_id));
		return $query->row();
	}   
	
	function merge($item_ids){
		$data = array(
				'keywords' => $this->input->post('keywords'),
				'description' => $this->input->post('notes')
		);
		$this->ci31_where_in('id', $item_ids);
		$this->db->update('items', $data);
	}
  	
	function this_logging_model($arr)
	{
		$database_id=$this->session->userdata('database_id');
		$locs = $this->Location_model->get_by_database($database_id)->result_array();
	  foreach($locs as $loc){
		  $loc_ids[] = $loc['id'];
	  }
		
		$query="select * from items where location_id IN (".implode(',',$loc_ids).") AND (name like '%$partial_name%' or keywords like '%$partial_name%' or description like '%$partial_name%')";
		$res=$this->db->query($query);
		return $res->result();

	}

	function find_similar($partial_name) {
		$user_data = $this->session->userdata["user_data"];
		$database_id=$this->session->userdata('database_id');

		$organization_admin = $this->Permissions_model->get_organization_admin($user_data['organization_id'])->admin_user;
		$package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);
		if($is_network->num_rows() == 0):
			$locs = $this->Location_model->get_by_database($database_id)->result_array();
		endif;
		if($is_network->num_rows() > 0):
			$locs = $this->Location_model->get_from_network_database($database_id)->result_array();
		endif;
		if($is_network->num_rows() > 0 && $organization_admin==$user_data['id']):
			$locs = $this->Location_model->get_by_database($database_id)->result_array();
		endif;



		  foreach($locs as $loc){
			  $loc_ids[] = $loc['id'];
		  }
		
		$query="select * from items where location_id IN (".implode(',',$loc_ids).") AND (name like '%$partial_name%' or keywords like '%$partial_name%' or description like '%$partial_name%') AND (transfer_status_id='null' OR transfer_status_id is null)";
		$res=$this->db->query($query);
		return $res->result();
	}
	
	function get_by_ids($ids, $organization_id=NULL){
		if( $organization_id !== NULL ) {
			$this->db->where('organization_id', $organization_id);
		}
		
  		$this->ci31_where_in('id', $ids);
  		return $this->db->get('items');
  }
	
	function get_from_location($loc_id, $page_number=null,$order_by=null,$order=null) {
		global $user_data;
		$loc_capacity=$this->db->get_where('locations',array('id' => $loc_id))->result_array();
		if($loc_capacity) {
			$loc_capacity=$loc_capacity[0]['capacity'];
		}
			$order_by_keys=array("slot_num","name","category","action_date","status");
			if(!in_array($order_by,$order_by_keys)) {
				$order_by="slot_num";
			}
			$order_keys=array("asc","desc");
			if(!in_array($order,$order_keys)) {
				$order="asc";
			}
		    if($order_by=="status") {
			$order_by="transfer_status_id";
		    }
		$page_size=$this->config->item('page_size');
		if($page_number==null || $page_number==1 || $page_number ==0) {
			if ($order_by == "slot_num" && $order == "desc")
			{
				$item_max_slot = $loc_capacity;
				$item_min_slot = $loc_capacity - $page_size;
			}

			if ($order_by == "slot_num" && $order == "asc"){
				$item_max_slot=$page_size;
				$item_min_slot=1;
			}
			$item_start = 0;
			$item_end = $page_size;
		} else {
			if($order_by=="slot_num" && $order=="desc"){
				$total_pages=ceil($loc_capacity/$page_size);
				$item_max_slot=$loc_capacity - (($page_number - 1) * $page_size ); 
				$item_min_slot= $item_max_slot - $page_size;
			}
			if($order_by=="slot_num" && $order=="asc"){
				$loc_capacity=$this->db->get_where('locations',array('id' => $loc_id))->result_array();
				if($loc_capacity) {
					$loc_capacity=$loc_capacity[0]['capacity'];
				}
				$total_pages=ceil($loc_capacity/$page_size);
				$item_min_slot=($page_number * $page_size ) - $page_size + 1;
				$item_max_slot=($page_number * $page_size ); 
			}
			$item_start = (($page_number-1)*$page_size); 
			$item_end = $item_start+$page_size;
		}
		
		//concat query
		//SELECT `items`.*, `pending_transfers`.`destination_location_id`, `pending_transfers`.`type`, `pending_transfers`.`merge_into`, `categories`.`name` as category, Concat(replace(replace(replace(replace(pending_transfers.type,'delete','Pending Toss'),'dupe','Pending Duplication'),'merge','Merge'),'move','Transfer'),' ', pending_transfers.destination_location_id) as status FROM (`items`) LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` LEFT JOIN `categories` ON `categories`.`id` = `items`.`category_id` WHERE `location_id` = '20672' ORDER BY `slot_num` asc LIMIT 50;
		$this->db->select('items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into, categories.name as category')->from('items')->where(array('location_id' => $loc_id))->order_by($order_by,$order)->distinct();
		$this->db->join('pending_transfers', 'pending_transfers.item_id = items.id', 'left');
		$this->db->join('categories','categories.id = items.category_id', 'left');
		if($order_by!="slot_num") {
			$this->db->limit($page_size, $item_start);
		}
		if($order_by=="slot_num"){
			$this->db->where('items.slot_num >=',$item_min_slot);
			$this->db->where('items.slot_num <=',$item_max_slot);
		}
		$query = $this->db->get();
//		exit(var_dump($this->db->last_query()));
//		var_dump($query->result());
		return $query;
	}
	function get_for_labels($loc_id=null,$start_range=null,$end_range=null) {
		global $user_data;
		
		$this->db->select('items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into, categories.name as category')->from('items')->where(array('location_id' => $loc_id,'slot_num >=' => $start_range,'slot_num <=' => $end_range))->order_by('slot_num','asc');
		$this->db->join('pending_transfers', 'pending_transfers.item_id = items.id', 'left');
		$this->db->join('categories','categories.id = items.category_id', 'left');
		$query = $this->db->get();
//		exit(var_dump($this->db->last_query()));
//		var_dump($query->result());
		return $query;
	}

	function get_for_excel_export($loc_id=null) {
		global $user_data;
		
		$this->db->select('items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into, categories.name as category')->from('items')->where(array('location_id' => $loc_id))->order_by('slot_num','asc');
		$this->db->join('pending_transfers', 'pending_transfers.item_id = items.id', 'left');
		$this->db->join('categories','categories.id = items.category_id', 'left');
		$query = $this->db->get();
//		exit(var_dump($this->db->last_query()));
//		var_dump($query->result());
		return $query;
	}
	
	function location_has_slot_available($loc_id){
		//// select empty_slots from locations where id = $loc_id
		global $user_data;
		
		$this->db->select('empty_slots')->from('locations')->where('id', $loc_id);
		$query = $this->db->get();
		$row = $query->row();
		return $row->empty_slots > 0;
	}
		
	function insert($loc_id, $position = NULL) {
	    global $user_data;	
	    $user_data = $this->session->userdata('user_data');
		if($this->input->post('action_date'))
		{
  			$action_date=strtotime($this->input->post('action_date'));	
		}
		else
		{
			$action_date=null;
		}
		if($this->session->userdata['new_category_id']==NULL)
		{
			$category_id=$this->input->post('category');
		}
		else if($this->session->userdata['new_category_id']=='')
		{
			$category_id=$this->input->post('category');
		}
		else if($this->session->userdata['new_category_id'])
		{
			$category_id=$this->session->userdata['new_category_id'];
		}
        
		$notes='';
		if($this->input->post('notes')):
			$notes=$this->input->post('notes');
		endif;	

		$data = array(
			'date_added' 			=> time(),
			'name'					=> $this->input->post('name'),
			'keywords' 				=> $this->input->post('keywords'),
			'category_id'			=>$category_id,
			'location_id' 			=> $loc_id,
			'transfer_status_id' 	=> 0,
			'description'			=> $notes,
			'action_date' 			=> $action_date,
			'organization_id' 		=> $user_data['organization_id']
    );
    
    $first_open_slot = $this->Location_model->get_first_open_slot($loc_id);
    
/*     exit(var_dump($position)); */
    
    if( $position === FALSE || !$this->Location_model->is_slot_open($loc_id, $position)) {
    	$data['slot_num'] = $first_open_slot;
    } else {
		$data['slot_num'] = $position;
    }
    
    $this->db->trans_start();
    //// insert item
    
    $this->db->insert('items', $data);
    $item_id = $this->db->insert_id();
    
    //// update locations num_items
    
    $this->db->set('num_items', 'num_items+1', false)->set('empty_slots', 'empty_slots-1', false)->where('id', $loc_id);
    $this->db->update('locations');
    
    $this->db->trans_complete();
    return array($item_id);
  }
  
 	function update($item_arr, $organization_id) {
		global $user_data;
		if($this->input->post('action_date'))
		{
  			$action_date=strtotime($this->input->post('action_date'));	
		}
		else
		{
			$action_date=null;
		}
		if(count($item_arr)==1){
	  		$data = array(
	               'name' => $this->input->post('name'),
	               'keywords' => $this->input->post('keywords'),
	               'category_id' => $this->input->post('category'),
	               'description' => $this->input->post('notes'),
	               'action_date' => $action_date
			);
    	} else {
	    	$data = array();
	    	if($this->input->post('category')!=-1){
	    		$data['category_id']=$this->input->post('category');
	    	}
		if($this->input->post('action_date'))
		{
  			$action_date=strtotime($this->input->post('action_date'));	
		}
		else
		{
			$action_date=null;
		}
	    	
			$data['action_date'] = $action_date;
    }
    
    $this->db->trans_start();
    foreach($item_arr as $item_id) {
		$this->db->where('id', $item_id);
		
		if( $organization_id !== NULL ) {
			$this->db->where('organization_id', $organization_id);
		}
		
		$this->db->update('items', $data);
		$this->log_modifications($item_id,$this->user_data['id']);
    }
    $this->db->trans_complete();
    
		return $item_arr;
  }
  
  function get_by_category($category_id){
  	global $user_data;
		
		$this->db->select('items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into')->from('items')->where(array('category_id' => $category_id));
		$this->db->join('pending_transfers', 'pending_transfers.item_id = items.id', 'left');
		$query = $this->db->get();
		return $query;
  }
    function get_location_permissions_by_user($user_id=null) {
	    $this->db->select('location_id');
	    $this->db->where('pto_umask >',0);
	    $this->db->where('user_id',$user_id);
	    $query=$this->db->get('location_permissions');
	    return $query;
    }
  function get_by_network_category($category_id){
  	global $user_data;
		$user_locations=$this->get_location_permissions_by_user($user_data['id']);
		$user_locs = array();
		foreach($user_locations->result() as $loc) {
			$user_locs[] = $loc->location_id;
		}
		if($user_locations->num_rows() > 0) {
			$this->ci31_where_in('items.location_id',$user_locs);
		} else {
			$this->db->where('items.location_id',null);
		}
		$this->db->select('items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into')->from('items')->where(array('category_id' => $category_id));
		$this->db->join('pending_transfers', 'pending_transfers.item_id = items.id', 'left');
		$query = $this->db->get();
		return $query;
  }
	//probably considering putting a logging trap here   
  function dupe_item($item_id, $new_location_id, $new_action_date, $slot_num = NULL){
	$user_data = $this->session->userdata('user_data');
  	$this->db->trans_start();

   	$orig_item = $this->get_by_id($item_id);
  	$data = array(
  			'date_added' => time(),
  			'name' => $orig_item->name,
			'transfer_status_id' => 0,
  			'keywords'=> $orig_item->keywords,
  			'action_date' => $orig_item->action_date,
  			'description' => $orig_item->description,
  			'category_id' => $orig_item->category_id,
  			'location_id' => $new_location_id,
			'organization_id'		=> $user_data['organization_id']
    );
  	
  	if($new_action_date != 0 && $new_action_date!==FALSE) {
	  	$data['action_date'] = $new_action_date;
  	}
    
    /*
    if($slot_num == NULL){
//	    $slot = $this->db->get_where('items', array('location_id' => $new_location_id, 'transfer_status_id' => $id))->row()->slot_num;
	    $data['slot_num'] = $this->Location_model->get_first_open_slot($new_location_id);
    } else {
    	$data['slot_num'] = $slot_num;
    }
    */
    $destination_item=$this->db->query("select * from items where transfer_status_id=(select transfer_status_id from items where id=".$item_id.") and organization_id=0")->row()->id;

    $this->db->where('id',$destination_item);
    $this->db->update('items', $data);
    //print_r($this->db->last_query());
    
    $this->db->set('num_items', 'num_items+1', false)->set('empty_slots', 'empty_slots-1', false)->where('id', $new_location_id);
    $this->db->update('locations');
    
    $this->db->trans_complete();
  }


  function dupe_item_no_confirmation($item_id, $new_location_id, $new_action_date, $slot_num = NULL){
	$user_data = $this->session->userdata('user_data');
  	$this->db->trans_start();

   	$orig_item = $this->get_by_id($item_id);
  	$data = array(
  			'date_added' => time(),
  			'name' => $orig_item->name,
			'transfer_status_id' => 0,
  			'keywords'=> $orig_item->keywords,
  			'description' => $orig_item->description,
  			'category_id' => $orig_item->category_id,
  			'location_id' => $new_location_id,
			'organization_id'	=> $user_data['organization_id']
    	);
  	
	if($new_action_date != 0 || $new_action_date != NULL)
	{
		$data['action_date']=$new_action_date;
	}
	else
	{
  		$data['action_date']=$orig_item->action_date;
	}
    
    $first_open_slot = $this->Location_model->get_first_open_slot($new_location_id);
    
/*     exit(var_dump($position)); */
    
    $data['slot_num'] = $first_open_slot;
    
    //$this->db->trans_start();
    //// insert item
    
    $this->db->insert('items', $data);
    $item_id = $this->db->insert_id();
    
    //// update locations num_items
    
    $this->db->set('num_items', 'num_items+1', false)->set('empty_slots', 'empty_slots-1', false)->where('id', $new_location_id);
    $this->db->update('locations');
    
    $this->db->trans_complete();
  }

  
  function move_item($item_id, $new_location_id, $new_action_date, $slot_num = NULL){
  	$this->db->trans_start();
  	
  	$orig_location_id = $this->get_by_id($item_id)->location_id;
  
  	$data = array(
  			'location_id' =>$new_location_id,
				'transfer_status_id'=>null
    );
    
    if($slot_num == NULL){
	    $data['slot_num'] = $this->Location_model->get_first_open_slot($new_location_id);
    } else {
    	$data['slot_num'] = $slot_num;
    }
  
  	if($new_action_date != 0 && $new_action_date!==FALSE) {
	  	$data['action_date'] = $new_action_date;
  	}
    
		$this->db->where('id', $item_id);
		$this->db->update('items', $data); 
		
		$this->db->set('num_items', 'num_items+1', false)->set('empty_slots', 'empty_slots-1', false)->where('id', $new_location_id);
    $this->db->update('locations');
    
    $this->db->set('num_items', 'num_items-1', false)->set('empty_slots', 'empty_slots+1', false)->where('id', $orig_location_id);
    $this->db->update('locations');
    
    $this->db->trans_complete();
  }
  
  function move_to_trash($item_arr, $organization_id=NULL) {
  	global $user_data;
  	
  	$this->db->trans_start();
  	$count = 0;
  	$location_id;
  	foreach($item_arr as $item_id) {
  		$count++;
  		$curr_item = $this->get_by_id($item_id);
  		$location_id = $curr_item->location_id;
  		$data = array(
  							'item_id'=>$item_id,
  							'name'=>$curr_item->name,
  							'database_id'=> $this->session->userdata('database_id'),
  							'date_added' => $curr_item->date_added,
  							'keywords' => $curr_item->keywords,
  							'category_id' => $curr_item->category_id,
  							'previous_location_id' => $curr_item->location_id,
  							'description' => $curr_item->description,
  							'action_date' => $curr_item->action_date
  		);
  		$this->db->insert('trash',$data);
		$this->log_modifications($item_id,$this->user_data['id']);
  		
		if( $organization_id !== NULL ) {
			$this->db->where('organization_id', $organization_id);
		}
  		$this->db->delete('items', array('id' => $item_id));
  	}
  	
  	
  	$prev_location = $this->Location_model->get_by_id($location_id);
  	$data = array(
							'num_items' => $prev_location->num_items - $count,
							'empty_slots' => $prev_location->empty_slots + $count
		);

		$this->db->where('id', $location_id);
		$this->db->update('locations', $data); 
  	
  	
  	$this->db->trans_complete();
  }
  
  
  function skip_trash($item_arr){
  	$this->db->trans_start();
  	
  	foreach($item_arr as $item_id) {
	  	$curr_item = $this->get_by_id($item_id);
	  	$location_id = $curr_item->location_id;
	  	$location = $this->Location_model->get_by_id($location_id);
	  	
	  	$data = array(
							'num_items' => $location->num_items - 1,
							'empty_slots' => $location->empty_slots + 1
			);
	  	$this->db->where('id', $location_id);
			$this->db->update('locations', $data);
			
  		$this->db->delete('items', array('id' => $item_id));
  	}
  	
  	$this->db->trans_complete();
  }
  
  function mark_for_merge($combined_item_arr, $merged_items_arr){
  	$this->db->trans_start();
  	$item = $this->get_by_id($combined_item_arr[0]);
		if($this->input->post('action_date'))
		{
  			$action_date=strtotime($this->input->post('action_date'));	
		}
		else
		{
			$action_date=null;
		}
  	$data = array(
  				'item_id'						=> $combined_item_arr[0],
  				'database_id'				=> $this->session->userdata('database_id'),
  				'origin_location_id'=> $item->location_id,
	  			'new_name' 					=> $this->input->post('name'),
			  	'new_keywords' 			=> $this->input->post('keywords'),
			  	'new_description' 	=> $this->input->post('notes'),
			  	'new_category_id' 	=> $this->input->post('category'),
			  	'new_action_date' 	=> $action_date,
			  	'type'							=> 'merge',
				'transfer_date'		=> time()
  	);
  	
  	$this->db->insert('pending_transfers',$data);
  	$merge_into_id = $this->db->insert_id();
  	
  	$this->db->where('id', $item->id);
		$this->db->update('items', array('transfer_status_id'	=> $merge_into_id));
		
  	foreach($merged_items_arr as $merged_items_id){
  		$item = $this->get_by_id($merged_items_id);
  		$data = array(
  				'item_id'						=> $merged_items_id,
  				'origin_location_id'=> $item->location_id,
  				'database_id'				=> $this->session->userdata('database_id'),
			  	'type'							=> 'merge',
			  	'merge_into'				=> $merge_into_id,
				'transfer_date'				=> time()
  		);
  		$this->db->insert('pending_transfers',$data);
  		
  		$transfer_id = $this->db->insert_id();
  	
	  	$this->db->where('id', $merged_items_id);
			$this->db->update('items', array('transfer_status_id'	=> $transfer_id));
  	}
  	
  	$this->db->trans_complete();
  }
  
  function mark_for_move($item_id, $new_location_id) {
  	$this->db->trans_start();
  	
  	$this_item = $this->get_by_id($item_id);
		if($this->input->post('action_date'))
		{
  			$action_date=strtotime($this->input->post('action_date'));	
		}
		else
		{
			$action_date=null;
		}
		$data = array(
             'item_id' => $item_id,
             'origin_location_id' => $this_item->location_id,
             'database_id'=>$this->session->userdata('database_id'),
             'destination_location_id'=>$new_location_id,
             'new_action_date' => $action_date,
             'type' => 'move',
	     'transfer_date' => time()
    );
    $this->db->insert('pending_transfers', $data);
    $transfer_id = $this->db->insert_id();
  	$this->db->update('items', array('transfer_status_id'=>$transfer_id), array('id' => $item_id));
  	
  	$data = array(
  					'date_added' =>time(),
  					'name'=>'',
  					'location_id'=>$new_location_id,
  					'transfer_status_id'=>$transfer_id,
  					'slot_num'=>$this->Location_model->get_first_open_slot($new_location_id)
  	);
  	$this->db->insert('items',$data);
  	
  	$this->db->set('empty_slots', 'empty_slots-1', false)->where('id', $new_location_id);
    $this->db->update('locations');
  	
    $this->db->trans_complete();
  }
  
  
  function mark_for_dupe($item_id, $new_location_id) {
  	$this->db->trans_start();

		if($this->input->post('action_date'))
		{
  			$action_date=strtotime($this->input->post('action_date'));	
		}
		else
		{
			$action_date=null;
		}
  	
  	$this_item = $this->get_by_id($item_id);
		$data = array(
             'item_id' => $item_id,
             'database_id'=>$this->session->userdata('database_id'),
             'origin_location_id' => $this_item->location_id,
             'destination_location_id'=>$new_location_id,
             'new_action_date' => $action_date,
             'type' => 'dupe',
	     'transfer_date' => time()
    );
    $this->db->insert('pending_transfers', $data);
    $transfer_id = $this->db->insert_id();
  	$this->db->update('items', array('transfer_status_id'=>$transfer_id), array('id' => $item_id));
  	
  	$data = array(
  					'date_added' =>time(),
  					'name'=>'',
  					'location_id'=>$new_location_id,
  					'transfer_status_id'=>$transfer_id,
  					'slot_num'=>$this->Location_model->get_first_open_slot($new_location_id)
  	);
  	$this->db->insert('items',$data);
  	
  	$this->db->set('empty_slots', 'empty_slots-1', false)->where('id', $new_location_id);
    $this->db->update('locations');
  	
    $this->db->trans_complete();
  }
  
  
  function mark_for_deletion($item_arr, $organization_id=NULL) {
  	global $user_data;
  	
  	foreach($item_arr as $item_id) {
  		$this->db->trans_start();
  		
		if( $organization_id !== NULL ) {
			$this_item = $this->get_by_id($item_id, $organization_id);
		} else {
			$this_item = $this->get_by_id($item_id);
		}	
  		$data = array(
               'item_id' => $item_id,
               'origin_location_id' => $this_item->location_id,
               'database_id'=>$this->session->userdata('database_id'),
               'destination_location_id'=>0,
               'type' => 'delete',
	       'transfer_date'	=> time()
      );
      $this->db->insert('pending_transfers', $data);
  		$this->db->update('items', array('transfer_status_id'=>$this->db->insert_id()), array('id' => $item_id));
  		$this->db->trans_complete();
  	}
  }
}
?>
