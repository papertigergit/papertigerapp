<?php
class Logging_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->database();
			//indexes: id, organization_id
    }
    function get_signup_logs($start_date=null,$stop_date=null) {
	    if($start_date == ''){
		    $year=date('Y',time());
		    $month=date('m',time());
		    $date=date('j',time());
		    $start_date=mktime(0,0,0,$month,$date,$year);
	    }
	    if($stop_date == ''){
		    $stop_date=time();
	    }
	    $this->db->where('date >',$start_date);
	    $this->db->where('date <',$stop_date);
	    $this->db->order_by('id','desc');
	    $query=$this->db->get('logs');
	    return $query;
    }
    function database($action_id,$database_id){
  	$user_data = $this->session->userdata('user_data');
    	$data = array(
		'action'=>$action_id,
		'database_id'=>$database_id,
		'action_time'=>time(),
		'by_who_id'=>$user_data['id'],
    		'name'=>$this->input->post('name')??0,
    		'date_added'=>time(),
    		'organization_id' => $user_data['organization_id'],
		'status' => 1
    	);
    	$this->db->insert('logs_databases', $data);
    }
    function assign_db($action_id,$database_id,$org_id){
  	$user_data = $this->session->userdata('user_data');
    	$data = array(
		'action'=>$action_id,
		'database_id'=>$database_id,
		'action_time'=>time(),
		'by_who_id'=>$user_data['id'],
    		'name'=>$this->input->post('name')??0,
    		'date_added'=>time(),
    		'organization_id' => $org_id,
		'status' => 1
    	);
    	$this->db->insert('logs_databases', $data);
    }
    function database_imports($action_id,$database_id,$org_id){
  	$user_data = $this->session->userdata('user_data');
    	$data = array(
		'action'=>$action_id,
		'database_id'=>$database_id,
		'action_time'=>time(),
		'by_who_id'=>$user_data['id'],
    		'name'=>$this->input->post('name')??0,
    		'date_added'=>time(),
    		'organization_id' => $org_id,
		'status' => 1
    	);
    	$this->db->insert('logs_databases', $data);
    }

	function delete($db_id){
		$where = array('id'=>$db_id);
		$db_data=$this->db->get_where('databases', $where)->row();

  	$user_data = $this->session->userdata('user_data');
    	$data = array(
		'action'=>1,
		'database_id'=>$db_id,
		'action_time'=>time(),
		'by_who_id'=>$user_data['id'],
    		'name'=>$db_data->name,
    		'date_added'=>$db_data->date_added,
    		'organization_id' => $db_data->organization_id,
		'status' => 1
    	);
    	$this->db->insert('logs_databases', $data);
}

	function update($database_name,$db_id){
		$where = array('id'=>$db_id);
		$db_data=$this->db->get_where('databases', $where)->row();

  	$user_data = $this->session->userdata('user_data');
	$database_name=$database_name."-".$db_data->name;
    	$data = array(
		'action'=>1,
		'database_id'=>$db_id,
		'action_time'=>time(),
		'by_who_id'=>$user_data['id'],
    		'name'=>$database_name, //old name
    		'date_added'=>$db_data->date_added,
    		'organization_id' => $db_data->organization_id,
		'status' => 1
    	);
    	$this->db->insert('logs_databases', $data);
}


	 function locations($action_id,$location_ids) {
		$user_data = $this->session->userdata('user_data');
	          $this->db->where_in('id', $location_ids);
		  $locations_data=$this->db->get('locations');
		 // print_r($locations_data->result());
			foreach($locations_data->result() as $row)
			{
			$data = array(
				'location_id' => $row->id,
				'action' =>$action_id,
				'action_time'=>time(),
				'by_who'=>$user_data['id'],
		               'date_added' => $row->date_added,
		               'name' => $row->name,
		               'capacity' => $row->capacity,
		               'num_items' => $row->num_items,
		               'empty_slots' => $row->empty_slots,
		               'next_review' => $row->next_review,
		               'review_interval_num' =>$row->review_interval_num,
		               'review_interval_string'=>$row->review_interval_string,
		               'database_id' => $row->database_id,
    				'organization_id' => $user_data['organization_id'],
		               'description' => $row->description
			);  
			$this->db->insert('logs_locations', $data);
		}
  }
	 function categories($action_id,$category_ids) {
		$user_data = $this->session->userdata('user_data');
	          $this->db->where_in('id', $category_ids);
		  $category_data=$this->db->get('categories');
			foreach($category_data->result() as $row)
			{
			$data = array(
				'category_id' => $row->id,
				'action' =>$action_id,
				'action_time'=>time(),
				'by_who'=>$user_data['id'],
		               'date_added' => $row->date_added,
		               'name' => $row->name,
    				'organization_id' => $user_data['organization_id'],
		               'database_id' => $row->database_id
			);  
			$this->db->insert('logs_categories', $data);
		}
  }
  


        function trash($action_id,$trash_id){
  	        $user_data = $this->session->userdata('user_data');
		$database_id = $this->session->userdata('database_id');
                $this->db->where_in('id', $trash_id);
                $items_data=$this->db->get('trash');
 	foreach($items_data->result() as $row)
	{   
                $data = array(
			'item_id'			=>$row->item_id,
			'action'			=>$action_id,
			'action_time'			=>time(),
                        'date_added'                    =>$row->date_added,
                        'name'                          =>$row->name,
                        'keywords'                      =>$row->keywords,
			'by_who'			=>$user_data['id'],
                        'category_id'                   =>$row->category_id,
                        'location_id'                   =>$row->previous_location_id,
                        'transfer_status_id'    	=>null,
                        'description'                   =>"null",
                        'action_date'                   =>null,
			'database_id'			=>$row->database_id,
                        'organization_id'               =>$user_data['organization_id']
    		);  
		$this->db->insert('logs_items', $data);
	  }
	}


        function items($action_id,$loc_ids){
  	        $user_data = $this->session->userdata('user_data');
		$database_id = $this->session->userdata('database_id');
                $this->db->where_in('id', $loc_ids);
                $items_data=$this->db->get('items');
 	foreach($items_data->result() as $row)
	{   
                $data = array(
			'item_id'			=>$row->id,
			'action'			=>$action_id,
			'action_time'			=>time(),
                        'date_added'                    =>$row->date_added,
                        'name'                          =>$row->name,
                        'keywords'                      =>$row->keywords,
			'by_who'			=>$user_data['id'],
                        'category_id'                   =>$row->category_id,
                        'location_id'                   =>$row->location_id,
                        'transfer_status_id'    	=>$row->transfer_status_id,
                        'description'                   =>$row->description,
                        'action_date'                   =>$row->action_date,
			'database_id'			=>$database_id,
                        'organization_id'               =>$row->organization_id
    		);  
		$this->db->insert('logs_items', $data);
	  }
	}
	function merge_source($items)
	{
		$user_data = $this->session->userdata('user_data');
		$database_id = $this->session->userdata('database_id');
                $this->db->where_in('id', $items);
                $items_data=$this->db->get('items');
	foreach($items_data->result() as $row)
		{
                	$data = array(
			'item_id'			=>$row->id,
			'action'			=>8,
			'action_time'			=>time(),
                        'name'                          =>$row->name,
			'by_who'			=>$user_data['id'],
			'database_id'			=>$database_id,
                        'organization_id'               =>$user_data['organization_id']
			);
		$this->db->insert('logs_items', $data);
		}
	}

//actions : 0 - new , 1-edited ,2-tossed, 3-pending toss, 4 - dupe, 5 pending dupe, 6 - moved, 7 - pending move, 8 - merge source, 9 - merge dest,10--empty trash,11-delete from trash, 12-restore from trash
        function accept($uri_segment) {
		$time_now=time();
  	        $user_data = $this->session->userdata('user_data');
		$database_id = $this->session->userdata('database_id');
		$this->db->where_in('id',$uri_segment);
		$items_data=$this->db->get('pending_transfers');
                foreach($items_data->result() as $row)
                     {       
			$action='';
			if($row->type=="delete")
			{
					$this->db->where('id',$row->item_id);	
					$items_to_be_deleted=$this->db->get('items');
					foreach($items_to_be_deleted->result() as $items_to_be_deleted_data)
					{
						$name=$items_to_be_deleted_data->name;
					}
					$data = array(
							'item_id'	=>$row->item_id,
							'action'	=>2,
							'action_time'	=>$time_now,
							'name'		=>$name,
							'by_who'	=>$user_data['id'],
							'database_id'	=>$database_id,
				                        'organization_id' =>$user_data['organization_id']
					    		);  
					$this->db->insert('logs_items',$data);
			}
			if($row->type=="move")
			{
					$this->db->where('id',$row->item_id);	
					$items_to_be_moved=$this->db->get('items');
					foreach($items_to_be_moved->result() as $items_to_be_moved_data)
					{
						$name=$items_to_be_moved_data->name;
					}
					$data = array(
							'item_id'	=>$row->item_id,
							'action'	=>6,
							'action_time'	=>$time_now,
							'name'		=>$name,
							'by_who'	=>$user_data['id'],
							'database_id'	=>$database_id,
				                        'organization_id' =>$user_data['organization_id']
					    		);  
					$this->db->insert('logs_items',$data);
			}
			if($row->type=="dupe")
			{
					$this->db->where('id',$row->item_id);	
					$items_to_be_duped=$this->db->get('items');
					foreach($items_to_be_duped->result() as $items_to_be_duped_data)
					{
						$name=$items_to_be_duped_data->name;
					}
					$data = array(
							'item_id'	=>$row->item_id,
							'action'	=>6,
							'action_time'	=>$time_now,
							'name'		=>$name,
							'by_who'	=>$user_data['id'],
							'database_id'	=>$database_id,
				                        'organization_id' =>$user_data['organization_id']
					    		);  
					$this->db->insert('logs_items',$data);
			}
			if($row->type=="merge")
			{
				//process source items first
				$this->db->where_in('merge_into',$uri_segment);
				$source_merge_data=$this->db->get('pending_transfers');
				foreach($source_merge_data->result() as $source_data)
				{
					$this->db->where('id',$source_data->item_id);	
					$items_to_be_merged=$this->db->get('items');
					foreach($items_to_be_merged->result() as $items_to_be_merged_data)
					{
						$name=$items_to_be_merged_data->name;
					}
					$data_source = array(
							'item_id'	=>$source_data->item_id,
							'action'	=>8,
							'action_time'	=>$time_now,
							'name'		=>$name,
							'by_who'	=>$user_data['id'],
							'database_id'	=>$database_id,
				                        'organization_id' =>$user_data['organization_id']
					    		);  
					$this->db->insert('logs_items',$data_source);
				}
				
				$this->db->where('id', $row->item_id);
				$item_name=$this->db->get('items');
				foreach($item_name->result() as $items_data)
				{
					$name=$items_data->name;
				}
        		        	$data = array(
						'item_id'			=>$row->item_id,
						'action'			=>9,
						'action_time'			=>$time_now,
			                        'name'                          =>$name,
						'by_who'			=>$user_data['id'],
						'database_id'			=>$database_id,
			                        'organization_id'               =>$user_data['organization_id']
				    		);  
					$this->db->insert('logs_items', $data);
		       }
              	}      
	}
}
?>
