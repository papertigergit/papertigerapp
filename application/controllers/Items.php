<?php
class Items extends MY_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->library(array('authentication', 'session', 'user_agent'));
		$this->load->helper(array('url','text_diff', 'access_helper'));
		$this->load->model('Item_model');
		$this->load->model('Trash_model');
		$this->load->model('Transfers_model');
		$this->load->model(array('Category_model', 'Location_model'));
		global $user_data;
		$user_data = $this->session->userdata('user_data');
	}
	function get_item_by_slot($location_id=NULL,$slot_num=NULL){
		$this->authentication->require_authentication();
		$item_data=$this->Item_model->get_item_by_slot($location_id,$slot_num);
		$item_data=json_encode($item_data);
		echo($item_data);
		return $item_data;
		}
	
	function find_first($search){
		$req_arr =  explode("/", $_SERVER['QUERY_STRING']);
		$partial_name = $search;
		$partial_name = urldecode($partial_name);
		$arr = $this->Item_model->find_similar($partial_name);
		$res = '{"num_results":'.count($arr).', "items":{';
		$first_item = true;
		if(count($arr)>0){
			if(count($arr)>1){
				foreach ($arr as $item) {
					if($first_item == false) {$res .= ',';}
					$first_item = false;
					$res .= '"item_'.$item->id.'":{"id":'.$item->id.',"name":"'.$item->name.'"}';
				}
			} else {
				$item = $arr[0];
				$res .= '"item":{"id":'.$item->id.',"name":"'.$item->name.'"}';
			}
			echo $res.'}}';
		}
	}
	
  	function upgrade(){
		  if($this->authentication->is_authenticated())
		  {
  			$this->load->view('upgrade_now');
		  }
		  else
		  {
			  $this->load->view('session_expired');
		  }
	  }
	function transfer($command=NULL, $item_id=NULL) {
		$this->authentication->require_authentication();
		global $user_data;

		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		array_shift($arr);

		
			$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
			$is_network=$this->Network_model->is_network($package_id);
		        $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
			$can_edit=false;

		foreach($arr as $item) { 	
			$location_id=$this->Item_model->get_by_id($item)->location_id;
			$loc_permissions=$this->Permissions_model->get_by_location($location_id,$this->user_data['id']);
			if($is_network->num_rows() == 0):
				$can_edit=true;
	//			$beta_edit=true;
			endif;	
			if($is_network->num_rows() > 0):
				if($loc_permissions) {
				if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3) {
					$can_edit=true;
	//				$beta_edit=true;
				  }
				}
			endif;	
			if($is_network->num_rows() > 0 && $organization_admin == $this->user_data['id']):
					$can_edit=true;
	//				$beta_edit=true;
			endif;		

				if(isset($can_edit) && !$can_edit) {
					$error_text="You do not have permissions to edit any items in this location.";
					$this->load->view('facebox_error',array('error'=>'You do not have permission to transfer any items in this location'));
							return;
				}
				if(isset($beta_edit) && $beta_edit) {
					$error_text="This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.
";
					$this->load->view('facebox_error',array('error'=>'This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.
'));
							return;
				}
		}




		$category_trans_form = false;
		$search_trans_form = false;
		$query = explode("/",$this->agent->referrer());
		if(in_array("search",$query)){
			$search_trans_form = true;
		} else if (in_array("categories",$query)){
			$category_trans_form = true;
		}
		
		if($command == NULL or $item_id == NULL){
			$this->load->view('facebox_error',array('error'=>'There was an error.'));
		} else {
			if($this->input->post('move_item') !== NULL) {
				/// process a MOVE form submission
				$location_id = $this->Item_model->get_by_id($arr[0])->location_id;
					
				// names of the item to be moved
				if(is_array($arr) && count($arr) > 1){
					$move_item_name = array();
					foreach($arr as $item){
						$item_name = $this->Item_model->get_by_id($item)->name;
						$move_item_name[] = "'".$item_name."'";
					}
					$move_item_name = implode(',',$move_item_name);// separate each item name with comma
				}else{
					// only single item is being moved
					$move_item_name = $this->Item_model->get_by_id($arr[0])->name;
					$move_item_name = "'".$move_item_name."'";
				}

				$this->load->library('form_validation');
				$this->form_validation->set_rules('new_location', 'Destination', 'is_natural_no_zero');
				$this->form_validation->set_message('is_natural_no_zero', 'You must choose a destination');
				
				if ($this->form_validation->run() === FALSE) {
					if(form_error('new_location')!=''){$errors[]='"missingFromForm":"'.form_error('new_location').'"';}
					$errors = implode(",", $errors);
					echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
				} else if($this->input->post('confirmation')===NULL){
					echo '{ "result": "validation_error", "errors": {"missingFromForm":"<p>You must choose one of the confirmation options above</p>"}}';
				} else {
					foreach($arr as $item){
						$exist_id=item_exists($item);
						if($exist_id===false):
								echo '{ "result": "validation_error", "errors": {"errors":"<p>While you were editing this item, a transfer action was performed on it. Refresh your window and try again.</p>"}}';
								return;
						endif;	
						if($this->input->post('confirmation')=="no"){
							$this->Item_model->move_item($item, $this->input->post('new_location'), strtotime($this->input->post('action_date')));
							$this->Logging_model->items(6,$item);
						} else { //confirmation==yes
							$this->Item_model->mark_for_move($item, $this->input->post('new_location'));
							$this->Logging_model->items(7,$item);
						}
					}

					if($search_trans_form){
						echo '{ "result": "successful_input", "grid_callback": "/search/search_grid/"}';
					} else if($category_trans_form){
						echo '{ "result": "successful_input", "grid_callback": "/categories/category_grid/'.$this->Item_model->get_by_id($arr[0])->category_id.'"}';
					} else {
						// new location name
						$new_location_name = $this->Location_model->get_by_id($this->input->post('new_location'))->name;
						// text to be displayed on front end 
				
						$text = "$move_item_name successfully moved to '$new_location_name' ";
						echo '{ "result": "successful_input", "grid_callback": "/locations/location_grid/'.$location_id.'","message_type": "success", "message": "'.$text.'"}';
					}
				}
			} else if($this->input->post('merge_item_select') !== NULL){
				// process a MERGE form submission
				$location_id = $this->Item_model->get_by_id($arr[0])->location_id;
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('new_location', 'destination', 'is_natural_no_zero');
				$this->form_validation->set_rules('file_to_merge', 'file', 'is_natural_no_zero');
				$this->form_validation->set_message('is_natural_no_zero', 'You must choose a %s');
				if ($this->form_validation->run() === FALSE) {
					if(form_error('file_to_merge')!=''){$errors[]='"missingFromForm":"'.form_error('file_to_merge').'"';}
					if(form_error('new_location')!=''){$errors[]='"missingFromForm":"'.form_error('new_location').'"';}
					
					$errors = implode(",", $errors);
					echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
					return;
				}
				
				$merge_data = array(
					'merge_data' => $arr,
					'new_action_date' => $this->input->post('action_date'),
				    'merge_time' => $this->input->post('merge_time'),
					'confirmation' => $this->input->post('confirmation')
				);
				$this->session->set_flashdata($merge_data);
				echo '{ "result": "merge_forward", "item_id": '.$this->input->post('file_to_merge').'}';
			} else if($this->input->post('merge_item') !== NULL){
				
			} else if($this->input->post('dupe_item') !== NULL){
				/// process a DUPE form submission
				$location_id = $this->Item_model->get_by_id($arr[0])->location_id;
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('new_location', 'Destination', 'is_natural_no_zero');
				$this->form_validation->set_message('is_natural_no_zero', 'You must choose a destination');
				
				if ($this->form_validation->run() === FALSE) {
					if(form_error('new_location')!=''){$errors[]='"missingFromForm":"'.form_error('new_location').'"';}
					$errors = implode(",", $errors);
					echo '{ "result": "validation_error", "errors": {'.$errors.'}}';
				} else if($this->input->post('confirmation')===NULL){
					echo '{ "result": "validation_error", "errors": {"missingFromForm":"<p>You must choose one of the confirmation options above</p>"}}';
				} else {
					foreach($arr as $item){
						if($this->input->post('confirmation')=="no"){
							$this->Logging_model->items(4,$item);
							$this->Item_model->dupe_item_no_confirmation($item, $this->input->post('new_location'), strtotime($this->input->post('action_date')));
						} else { //confirmation==yes
							$this->Logging_model->items(5,$item);
							$this->Item_model->mark_for_dupe($item, $this->input->post('new_location'));
						}

						$item_name = $this->Item_model->get_by_id($item)->name;
						$dupe_items_name[] = $item_name;
					}
					
					
					if($search_trans_form){
						echo '{ "result": "successful_input", "grid_callback": "/search/search_grid/"}';
					} else if($category_trans_form){
						echo '{ "result": "successful_input", "grid_callback": "/categories/category_grid/'.$this->Item_model->get_by_id($arr[0])->category_id.'"}';
					} else {
						$new_location_name = $this->Location_model->get_by_id($location_id)->name;
						$dupe_items_name = implode(', ',$dupe_items_name);
						
						$text = "$dupe_items_name successfully duplicated to '$new_location_name' ";
						echo '{ "result": "successful_input", "grid_callback": "/locations/location_grid/'.$location_id.'","message_type": "success", "message": "'.$text.'"}';
					}
				}
			} else {
				/// load an ajax view
				$pending =false;
				foreach($arr as $item){
					if($this->Transfers_model->item_is_pending($item)){	
						$pending = true;
					}
				}
				
				if($pending){
					//$this->session->set_flashdata('error', 'There was an error with your transfer. Please try again');
					//redirect();
					$this->load->view('facebox_error',array('error'=>'You cannot transfer an item that is already pending transfer.'));
				} else {
					if($is_network->num_rows() == 0):
						$locations=$this->Location_model->get_from_database();
					endif;
					if($is_network->num_rows() > 0):
						$locations=$this->Location_model->get_from_network_database_readonly($this->session->userdata('database_id'));
					endif;
					if($is_network->num_rows() > 0 && $organization_admin == $this->user_data['id']):
						$locations=$this->Location_model->get_from_database();
					endif;
					$data = array(
									'locations' => $locations,
									'item_ids'		=> $arr,
									'num_items'	 => count($arr)
					);
					if($command=="move"){$this->load->view('move_item', $data);}else
					if($command=="dupe"){$this->load->view('dupe_item', $data);}else
					if($command=="merge"){$this->load->view('merge_item', $data);}
				}
			}	
		}
	}
	
	function merge_view() {
		$this->authentication->require_authentication();
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		$item_id = $arr[0];
		
		$category_data = $this->Category_model->get_from_database();
		$item = $this->Item_model->get_by_id($item_id);
		
		$merge_data = $this->session->flashdata('merge_data');
    $merge_time = $this->session->flashdata('merge_time');
    // print_r(unix_to_human($merge_time));
    // print_r('merge time is '.$merge_time.' ');
		$merge_keywords = '';
		$merge_notes = '';
		$merge_ids = array();
    $collision_items = ''; 
    $merge_collision = FALSE;
		foreach($merge_data as $merge_id){
			$merge_item = $this->Item_model->get_by_id($merge_id);
			$merge_keywords.=$merge_item->keywords.' ';
			$merge_notes.=$merge_item->description.' ';
			$merge_ids[] = $merge_id;
      $last_edit_db_time = 0;
      $last_edit_db_time=$merge_item->last_edited_time;
      // print_r($merge_item);
      // print_r(' '.unix_to_human($last_edit_db_time).' ');
      // print_r('item edit time is '.$last_edit_db_time.' ');
      if ($merge_time <= $last_edit_db_time){
						$last_user_edit=$this->User_model->get_user($merge_item->last_edited_by)->row(); 
						$f_name=$last_user_edit->first_name;
						$l_name=$last_user_edit->last_name;
        $merge_collision = TRUE;
      $collision_items .='<li>'.$item->name.' edited by '.$f_name.' '.$l_name.'</li>';
      }
		}

    if ($merge_time <= $item->last_edited_time){
      $last_user_edit=$this->User_model->get_user($item->last_edited_by)->row(); 
      $f_name=$last_user_edit->first_name;
      $l_name=$last_user_edit->last_name;
      $merge_collision = TRUE;
      $collision_items .='<li>'.$item->name.' edited by '.$f_name.' '.$l_name.'</li>';
    }

    // print_r($collision_items);
		$data = array(
					'categories'=>$category_data, 
					'this_item'=>$item, 
					'multiple_items'=>false,
					'items'=>$arr,
					'merge_keywords' => $merge_keywords,
					'merge_notes' => $merge_notes,
					'merged_items' => $merge_ids,
					'confirmation' => $this->session->flashdata('confirmation')
		);
		if(trim($this->session->flashdata('new_action_date'))!=''){
			$data['new_action_date'] = $this->session->flashdata('new_action_date');
		}
    if($merge_collision == TRUE){
      $this->load->view('facebox_error',array('error'=>'<p>One of the items you are attempting to merge was just edited by another user. Please refresh this page and try again!</p><br /><p>The item(s) edited were:<ul id="facebox_list">'.$collision_items.'</ul></p>'));
			return;
    }
    else{
      $this->load->view('merge_edit_item', $data);
    }
	}
	
	function find_first_view($item_id = NULL){
		$this->authentication->require_authentication();
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);
		$item_id = $arr[0];
		
		
		if(count($arr)>1){
			$this->session->keep_flashdata('ff_keywords');
			$this->session->keep_flashdata('ff_notes');
			$data = array(
					'items' => $this->Item_model->get_by_ids($arr)->result()
			);
			$this->load->view('find_first_grid', $data);
		} else {
			$category_data = $this->Category_model->get_from_database();
			
			$item = $this->Item_model->get_by_id($item_id);
			$data = array(
						'categories'=>$category_data, 
						'this_item'=>$item, 
						'multiple_items'=>false, 
						'items'=>$arr,
						'ff_keywords'=>$this->session->flashdata('ff_keywords'),
						'last_edit_time' => time(), // fix for merging collision error issue
						'ff_notes'=>$this->session->flashdata('ff_notes')
			);
			$this->load->view('find_first_merge', $data);	
		}
	}
	
	function edit($order_by=null,$order=null,$item_id=NULL){
		$this->authentication->require_authentication();
		$arr = $this->uri->segment_array();
		if($order_by==null) {
			$order_by="slot_num";
		}
		if($order==null) {
			$order="asc";
		}
		//array_shift($arr);
		//shift for search grid
		$order_by_terms=array("slot_num","name","action_date","category");
		$order_terms=array("asc","desc");
		if(in_array($this->uri->segment(3),$order_by_terms) || in_array($this->uri->segment(4),$order_terms)) {
			array_shift($arr);
			array_shift($arr);
		}
		array_shift($arr);
		array_shift($arr);

		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
		$can_edit=false;

		foreach($arr as $item) { 	
			//check to make sure postion exists
			$position=$this->Item_model->get_by_id($item)->slot_num;
			$location_id=$this->Item_model->get_by_id($item)->location_id;
			$loc_permissions=$this->Permissions_model->get_by_location($location_id,$this->user_data['id']);
			if($is_network->num_rows() == 0):
				$can_edit=true;
		//		$beta_edit=true;
			endif;	
			if($is_network->num_rows() > 0):
				if($loc_permissions) {
				if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3) {
					$can_edit=true;
		//			$beta_edit=true;
				  }
				}
			endif;	
			if($is_network->num_rows() > 0 && $organization_admin == $this->user_data['id']):
					$can_edit=true;
					$beta_edit=false; 
					//$beta_edit=true; //I suppose it should be false
			endif;		

				if(isset($can_edit) && !$can_edit) {
					$error_text="You do not have permissions to edit any item in this location.";
					$this->load->view('facebox_error',array('error'=>'You do not have permission to edit any item in this location.'));
							return;
				}
				if(isset($beta_edit) && $beta_edit) {
					$error_text="This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.
";
					$this->load->view('facebox_error',array('error'=>'This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.
'));
							return;
				}
		}

		$page_size=$this->config->item('page_size');
		if(!isset($item)){
			$item=$this->input->post('items');
			$item=$item[0];
			$position=$this->Item_model->get_by_id($item)->slot_num ?? 0;
		}
		$location_id = $this->Item_model->get_by_id($item)->location_id ?? 0;
		$page_number=1;
		$loc_capacity=$this->Location_model->get_by_id($location_id)->capacity ?? 0;
		$total_pages = ceil($loc_capacity / $page_size);
		if($order_by=="slot_num" && $order=="asc") {
			$page_number=ceil($position / $page_size );
		}
		if($order_by=="slot_num" && $order=="desc") {
			$page_number=$total_pages - ceil($position/$page_size) + 1;
		}
		if($order_by!="slot_num") {
			$page_number=ceil($position / $page_size );
		}

		
		$category_edit_form = false;
		$search_edit_form = false;
		$query = explode("/",$this->agent->referrer());
		if(in_array("search",$query)){
			$search_edit_form = true;		} else if (in_array("categories",$query)){
			$category_edit_form = true;
		}
		
		$pending =false;
		foreach($arr as $item){
		if($this->Transfers_model->item_is_pending($item)){	
			$pending = true;
			}
		}

		if($pending){
			$this->load->view('facebox_error',array('error'=>'One or more items already has a transfer that is pending. Cancel or Confirm this transfer before editing.'));
		}
		else
		{

			
		
		if($this->input->post('edit_item') !== NULL) {

				foreach($this->input->post('items') as $item) {


				$is_network=$this->Network_model->is_network($package_id);
				$location_id=$this->Item_model->get_by_id($item)->location_id;
				$loc_permissions=$this->Permissions_model->get_by_location($location_id,$this->user_data['id']);
				if($is_network->num_rows() == 0):
					$can_edit=true;
				endif;	
				if($is_network->num_rows() > 0):
					if($loc_permissions) {
					if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3) {
						$can_edit=true;
					  }
					}
				endif;	
				if($is_network->num_rows() > 0 && $organization_admin == $this->user_data['id']):
						$can_edit=true;
				endif;		

					if(!$can_edit) {
							$this->session->set_flashdata('error', 'You no longer have access to this item.');
							echo '{ "result": "successful_redirect", "url": "/dashboard"}';
							return;
							//echo '{ "result": "validation_error", "errors": {"errors":"<p>While you were editing this item, your user rights were revoked.</p>"}}';
							//return;
					}


					$exist_id=item_exists($item);
					if($exist_id===false):
							echo '{ "result": "validation_error", "errors": {"errors":"<p>While you were editing this item, a transfer action was performed on it. Refresh your window and try again.</p>"}}';
							return;
					endif;	
					$last_edit_user_id=$this->Item_model->get_by_id($item)->last_edited_by;
					$last_edit_db_time=$this->Item_model->get_by_id($item)->last_edited_time;

						$item_data=$this->Item_model->get_by_id($item);
				
					if($this->input->post('edit_time') < $last_edit_db_time) {

						$last_user_edit=$this->User_model->get_user($last_edit_user_id)->row(); 
						$f_name=$last_user_edit->first_name;
						$l_name=$last_user_edit->last_name;
                        $db_edit_time=unix_to_human($last_edit_db_time);

                        $new_item_category = '0';
                        $posted_item_category = '0';


                        //check for items with no categories
                        if ($item_data->category_id !== 0) {
                            $new_item_category = $item_data->category_id;
                        }
                        
                        if ($this->input->post('category') !== 0) {
                            $posted_item_category = $this->input->post('category');
                        }
                        //check for items with no action date
                        //
                        
                        if ($item_data->action_date !== NULL) {
                            $new_item_action_date = date("m/d/Y",$item_data->action_date); 
                        }

                        else {
                            $new_item_action_date = '';
                        }

                        if ($this->input->post('action_date') !== NULL) {
                            $posted_item_action_date = $this->input->post('action_date'); 
                        }

                        else {
                            $new_item_action_date = '';
                        }

                        //run diffs on changed strings
                        $item_name_diff = urlencode(htmlDiff($this->input->post('name'), $item_data->name));
                        $keywords_diff = urlencode(htmlDiff($this->input->post('keywords'), $item_data->keywords));
                        $notes_diff = urlencode(htmlDiff($this->input->post('notes'), $item_data->description));
                        $category_diff = urlencode(htmlDiff($posted_item_category, $new_item_category));
                        $action_date_diff = urlencode(htmlDiff($posted_item_action_date, $new_item_action_date));
			//check if item was either tossed or transferred during the edit process
					
						if(($this->input->post('name') != $item_data->name) || ($this->input->post('keywords') != $item_data->keywords) || ($this->input->post('action_date') != $item_data->action_date) ||  ($this->input->post('category') != $item_data->category_id) || ($this->input->post('notes') != $item_data->description)):
                            $error_text="This item has been edited by $f_name $l_name while you were editing it. Use the links below to browse through the history of changes and select which information you would like to save.";
						echo 
                            '{ 
                                "result": "collision_error",
                                "errors": {
                                    "name":"'.$error_text.'"
                                },
                                "edited_by": "'.$f_name.' '.$l_name.'",
                                "merged_item_name": "'.$item_name_diff.'",
                                "merged_item_keywords":"'.$keywords_diff.'",
                                "merged_item_notes": "'.$notes_diff.'",
                                "merged_item_category": "'.substr_replace($category_diff ,"",-1)./*removing last character because the diff always adds spaces to the strings*/'", 
                                "merged_item_action_date": "'.$action_date_diff.'",
                                "new_item_name": "'.urlencode($item_data->name).'",
                                "new_item_keywords":"'.urlencode($item_data->keywords).'",
                                "new_item_notes": "'.urlencode($item_data->description).'",
                                "new_item_category": "'.urlencode($item_data->category_id).'",
                                "new_item_action_date": "'.urlencode($new_item_action_date).'",
                                "original_item_name": "'.urlencode($item_data->name).'",
                                "original_item_keywords":"'.urlencode($item_data->keywords).'",
                                "original_item_notes": "'.urlencode($item_data->description).'",
                                "original_item_category": "'.urlencode($item_data->category_id).'",
                                "original_item_action_date": "'.urlencode($item_data->action_date).'",
                                "your_item_name": "'.urlencode($this->input->post('name')).'",
                                "your_item_keywords":"'.urlencode($this->input->post('keywords')).'",
                                "your_item_notes": "'.urlencode($this->input->post('notes')).'",
                                "your_item_category": "'.urlencode($posted_item_category).'",
                                "your_item_action_date": "'.urlencode($this->input->post('action_date')).'"
                    
                            }';
							return;
						endif;		
					}
				}

			if($this->input->post('merge_edit') !== NULL) {
				$items = $this->input->post('items');
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('name', 'Name', 'trim|required');
				
				if (count($items)==1 && $this->form_validation->run()==FALSE) {
					echo '{ "result": "validation_error", "errors": {"name":"'.form_error('name').'"}}';
				} else {
					$pending = false;					
										
					foreach($items as $item){			
					
					if($this->Transfers_model->item_is_pending($item)){	
						$pending = true;
						}

					}
						if($pending){
							echo '{ "result": "validation_error", "errors": {"name":"<p>There was an error with the merge. Please close this window and refresh your page.</p>"}}';
					}




					else
					{
					
					if($this->input->post('confirmation')=='no'){
						$org_data=$this->session->userdata['user_data'];
						$this->Logging_model->merge_source($this->input->post('merged'));
						$this->Item_model->skip_trash($this->input->post('merged'));
						$modified_rows = $this->Item_model->update($items,$org_data['organization_id']);
						$this->Logging_model->items(9,$modified_rows);
						$this->session->set_flashdata('modified_rows', $modified_rows);
					} else {
						$this->Item_model->mark_for_merge($items, $this->input->post('merged'));
					}
					
				        $location_id=$this->input->post('location_id');
				        $location_name=$this->Location_model->get_by_ids($location_id)->row()->name;
					$slot_num=$this->Item_model->get_by_id($items[0])->slot_num;

					$text = "Item added to '".$this->input->post('name')."' in '".$location_name."' slot ".$slot_num;					
					
					if($search_edit_form){
						echo '{ "result": "successful_input", "grid_callback": "/search/search_grid/", "message_type": "success", "message": "'.$text.'"}';						
					} else if($category_edit_form){
						echo '{ "result": "successful_input", "grid_callback": "/categories/category_grid/'.$this->Item_model->get_by_id($items[0])->category_id.'", "message_type": "success", "message": "'.$text.'"}';				
						
					} else {
						echo '{ "result": "successful_input", "grid_callback": "/locations/location_grid/'.$this->Item_model->get_by_id($items[0])->location_id.'/'.$items[0].'/'.$page_number.'/'.$order_by.'/'.$order.'", "message_type": "success", "message": "'.$text.'"}';						
					}
				}
				}
			} else {
				//form submission for single & multiple items
				$items = $this->input->post('items');
				
				$this->load->library('form_validation');
				$this->form_validation->set_rules('name', 'Name', 'trim|required');
				
				if (count($items)==1 && $this->form_validation->run()==FALSE) {
					echo '{ "result": "validation_error", "errors": {"name":"'.form_error('name').'"}}';
				} else {
					$modified_rows = $this->Item_model->update($items, $this->user_data['organization_id']);
					$this->Logging_model->items(1,$modified_rows);
					$this->session->set_flashdata('modified_rows', $modified_rows);
					
					$text = count($items)>1?'Items':'Item';
					
					$text = $text." edited successfully.";					

					$location_id = $this->Item_model->get_by_id($items[0])->location_id;
					$location_name = $this->Location_model->get_by_ids($location_id)->row()->name;

					if($this->input->post('merge')=="yes")
					{
						$text="Item added to '".$this->input->post('name')."' in ".$location_name;
					}
					
					if($search_edit_form){
						echo '{ "result": "successful_input", "grid_callback": "/search/search_grid/", "message_type": "success", "message": "'.$text.'"}';						
					} else if($category_edit_form){
						echo '{ "result": "successful_input", "grid_callback": "/categories/category_grid/'.$this->Item_model->get_by_id($items[0])->category_id.'", "message_type": "success", "message": "'.$text.'"}';				
						
					} else {
						echo '{ "result": "successful_input", "grid_callback": "/locations/location_grid/'.$this->Item_model->get_by_id($items[0])->location_id.'/'.$items[0].'/'.$page_number.'/'.$order_by.'/'.$order.'", "message_type": "success", "message": "'.$text.'"}';						
					}
				}
			}
		} else if($this->input->post('find_first_flag') !== NULL){
			if(count($arr)==1) { 
				$ff_data = array(
				   'ff_keywords'  => $this->input->post('keywords'),
				   'ff_notes'     => $this->input->post('notes'),
				);
				$this->session->set_flashdata($ff_data);
				//as we are considering arr having one argument taking item_id to be its first element.
				//echo '{ "result": "ff_forward", "multiple_items":"false", "item_id": "'.$item_id.'"}';
				echo '{ "result": "ff_forward", "multiple_items":"false", "item_id": "'.$arr[0].'"}';
			} else {
				$ff_data = array(
				   'ff_keywords'  => $this->input->post('keywords'),
				   'ff_notes'     => $this->input->post('notes'),
				);
				$this->session->set_flashdata($ff_data);
				$ids = '';
				$first_item = true;
				foreach($arr as $id){
					if(!$first_item){$ids .= ',';}
					$first_item = false;
					$ids .= $id;
				}
				echo '{ "result": "ff_forward", "multiple_items":"true", "ids": ['.$ids.']}';
			}
		} else {
			// attempting to load ajax view
			if(count($arr)==0) {
				//no items selected
				$this->load->view('facebox_error',array('error'=>'Select an item first.'));
			} else if(count($arr)==1) {
				// one item selected
				$item_id = $arr[0];
				$category_data = $this->Category_model->get_from_database();
			
				$item = $this->Item_model->get_by_id($item_id, $this->user_data['organization_id']);
				
				if( !$item ) {
					$this->load->view('facebox_error',array('error'=>'You are not authorized to view this item.'));
					return;
				}

				$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
				$is_network=$this->Network_model->is_network($package_id);
				
					$location_name = $this->Location_model->get_by_ids($location_id)->row()->name;
				$data = array('categories'=>$category_data, 'this_item'=>$item, 'multiple_items'=>false, 'items'=>$arr,'last_edit_time' => time(),'is_network' => $is_network,'order_by'=>$order_by,'order'=>$order,'location_name' =>$location_name);
				$this->load->view('edit_item', $data);
			} else {
				//multiple items selected
				$category_data = $this->Category_model->get_from_database();
				$data = array('categories'=>$category_data, 'multiple_items'=>true, 'items'=>$arr,'last_edit_time' => time(),'is_network' => $is_network,'order_by'=>$order_by,'order'=>$order);
				$this->load->view('edit_item', $data);
			}
		}
		}
	}
	
	function add($location_id=NULL,$position=null,$order_by=null,$order=null) {
		//$beta_edit=true;
		if(isset($beta_edit) && $beta_edit) {
			$error_text="This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.";
			$this->load->view('facebox_error',array('error'=>'This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.'));
			return;
		}

		$page_size=$this->config->item('page_size');

		$this->authentication->require_authentication();
					
		$location = $this->uri->segment(3);

		if($position==null) {
			$position = $this->uri->segment(4);
		}
		if(!$position)
		{
			$position = $this->input->post('position');
		}
		if($position=="first") {
			$order_by="slot_num";
			$order="asc";
		}
		if(!$position || $position=="first")
		{
			$position = $this->Location_model->get_first_open_slot($location_id);
		}

		if($order_by==null) {
			$order_by="slot_num";
		}
		if($order==null) {
			$order="asc";
		}
		// lets make sure this location is owned by the user		
		$location = $this->Location_model->get_by_id($location_id, $this->user_data['organization_id']);
		//lets make sure we got a valid location to work with.
		if( !isset($location)) {
			$this->load->view('facebox_error',array('error'=>'You are not authorized to add an item to the specified location.'));
			return;
		}
		
		$page_number=1;
		$loc_capacity=$location->capacity;
		$total_pages = ceil($loc_capacity / $page_size);
		if($order_by=="slot_num" && $order=="asc") {
			$page_number=ceil($position / $page_size );
		}
		if($order_by=="slot_num" && $order=="desc") {
			$page_number=$total_pages - ceil($position/$page_size) + 1;
		}
		if($order_by!="slot_num") {
			$page_number=ceil($position / $page_size );
		}
		
		
		if($this->input->post('name') === NULL) {
			if( !$location ) {
				$this->load->view('facebox_error',array('error'=>'Unable to add an item to the specified location.'));
				return;
			}
		}
		

		$slot_open = $this->Location_model->is_slot_open($location_id, $position);

		if($position !== FALSE && !$slot_open && $this->input->post('name') !== NULL) 
		{
				$location_name= $this->Location_model->get_by_id($location_id)->name;
				$next_avail_slot=$this->Location_model->get_first_open_slot($location_id);
				$error_text="Slot Number ".$position." has been occupied while you were adding your item.";
				$error_text.="The next available slot is ".$next_avail_slot.".";
	                       echo '{ "result": "slot_collision_error", "errors": {"errors":"'.$error_text.'"}, "new_slot_number": "'.$next_avail_slot.'", "location_name": "'.$location_name.'"}';
			       return;
		}
			
		if($position !== FALSE && !$slot_open)
		{
			$this->load->view('facebox_error',array('error'=>'You are attempting to add an item to a slot which already has an item. Please refresh this page and try again!.'));
			return;
		}

			
		if($this->input->post('name') !== NULL) {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'Name', 'trim|required');

		if($this->input->post('action_date')){
			$end_of_day=$this->input->post('action_date')." 23:59";
                if(time() > strtotime($end_of_day)) //action date in the past
	                {   
	                       echo '{ "result": "validation_error", "errors": {"action_date":"<p>All action dates must be in the future.</p>"}}';
			       return;
	                } 
		}	

			
			if ($this->form_validation->run() == FALSE) {
				
				echo '{ "result": "validation_error", "errors": {"name":"'.form_error('name').'"}}';
			} else {
				if($this->Item_model->location_has_slot_available($location_id)){
			 	   if($this->input->post('new_category') != NULL){
					   if($this->input->post('new_category') != ''){
						   if($this->input->post('new_category')){
							   //Create new category
							   $category_details=$this->Category_model->new_category($this->input->post('new_category'));
							   foreach($category_details as $row)
							   {
							   	$this->session->set_userdata('new_category_id',$row);
							   }
						   }
					   }
				   }

					$modified_rows = $this->Item_model->insert($location_id, $position);
					$this->Logging_model->items(0,$modified_rows);
					$this->session->set_flashdata('modified_rows', $modified_rows);
					//Did the click "add" or "add another"?
				/*
					if( isset($_POST['submit_another']) ) {
						echo '{ "result": "validation_error", "errors": {"name":"lol broke"}}';
					} else {
						echo '{ "result": "successful_input", "grid_callback": "/locations/location_grid/'.$location_id.'", "message_type": "success", "message": "Item added successfully."}';
						
					}
				*/
					
					$item_name = htmlspecialchars($this->input->post('name'));
					echo '{ "result": "successful_input", "grid_callback": "/locations/location_grid/'.$location_id.'/'.$modified_rows[0].'/'.$page_number.'/'.$order_by.'/'.$order.'", "message_type": "success", "message": "\"'.$item_name.'\" added successfully into item '.$position.'."}';

				} else {
					echo '{ "result": "validation_error", "errors": {"missingFromForm":"<p>There are no available slots in this location.</p>"}}';
				}
			}
		} else{
			// ajax view
			$category_data = $this->Category_model->get_from_database();
			$data = array('categories'=>$category_data, 'this_location'=>$location, 'position'=>$position,'order_by'=>$order_by,'order'=>$order);
			$this->load->view('add_item', $data);
		}
	}

	/*
		add category while editing items
	*/
	function edit_add_new_category(){
		if(isset($beta_edit) && $beta_edit) {
			$error_text="This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.";
			$this->load->view('facebox_error',array('error'=>'This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.'));
			return;
		}
		$this->authentication->require_authentication();
		if($this->input->post('new_category') != NULL){
		   if($this->input->post('new_category') != ''){
			   if($this->input->post('new_category')){
				   //Create new category
				   $category_details=$this->Category_model->new_category($this->input->post('new_category'));
				   foreach($category_details as $row)
				   {
					$new_category_id =  $row;
					$this->session->set_userdata('new_category_id',$row);
				   }
				   $item_name = htmlspecialchars($this->input->post('new_category'));
					echo '{ "result": "successful_input", "message_type": "success", "message": "Category \"'.$item_name.'\" added successfully",
							"new_category_name":"'.$item_name.'","new_category_id":"'.$new_category_id.'"}';
			   }
		   }
		}
	
	}
	
	function file_list($loc_id){
		$this->authentication->require_authentication();
		$items = $this->Item_model->get_from_location($loc_id);
		$out = '{"items": {';
		$i = 0;
		foreach($items->result() as $item){
		
			//if (($item->transfer_status_id !==NULL) && ($item->transfer_status_id !== 0 )) {
			$transfer_status_id='0';	
			if($item->transfer_status_id){
			$transfer_status_id=$item->transfer_status_id;	
			}
			$out .= '"'.$i++.'":{'.
			'"id":'.$item->id.
			',"slot":'.$item->slot_num.
			',"tsid":'.$transfer_status_id.
			',"name":"'.htmlspecialchars($item->name).
			'"},';
			
			//} 
		}
		$out = substr($out,0,strlen($out)-1);
		$out .= '}}';
		echo $out;

//		print_r($items);
		
		//echo '{ "result": "validation_error", "errors": {"missingFromForm":"There are no available items"}}';
	}
	
	function delete(){
		$this->authentication->require_authentication();
		$arr = $this->uri->segment_array();
		array_shift($arr);
		array_shift($arr);



			$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
			$is_network=$this->Network_model->is_network($package_id);
		        $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
			$can_edit=false;

		foreach($arr as $item) { 	
			$location_id=$this->Item_model->get_by_id($item)->location_id;
			$loc_permissions=$this->Permissions_model->get_by_location($location_id,$this->user_data['id']);
			if($is_network->num_rows() == 0):
				$can_edit=true;
//				$beta_edit=true;
			endif;	
			if($is_network->num_rows() > 0):
				if($loc_permissions) {
				if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3) {
					$can_edit=true;
//					$beta_edit=true;
				  }
				}
			endif;	
			if($is_network->num_rows() > 0 && $organization_admin == $this->user_data['id']):
					$can_edit=true;
//					$beta_edit=true;
			endif;		

				if(isset($can_edit) && !$can_edit) {
					$error_text="You do not have permissions to edit any item in this location.";
					$this->load->view('facebox_error',array('error'=>'You do not have permission to toss any items in this location'));
							return;
				}
				if(isset($beta_edit) && $beta_edit) {
					$error_text="This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.
";
					$this->load->view('facebox_error',array('error'=>'This is a BETA version of Paper Tiger/Digital Tiger.  For your protection, you will not be able to enter data into your Paper Tiger database using this BETA version.  Please go to your regular Paper Tiger account (the non-BETA version) to enter your data and/or make edits.
'));
							return;
				}
		}



		
		$category_delete_form = false;
		$search_delete_form = false;
		$query = explode("/",$this->agent->referrer());
		if(in_array("search",$query)){
			$search_delete_form = true;
		} else if (in_array("categories",$query)){
			$category_delete_form = true;
		}
		
		if($this->input->post('delete') !== NULL) {
			$items = $this->input->post('items');
			$location_id = $this->Item_model->get_by_id($items[0], $this->user_data['organization_id'])->location_id;
			$category_id=$this->Item_model->get_by_id($items[0])->category_id;
			if($this->input->post('confirmation')!==NULL){
				if($this->input->post('confirmation')=="no"){
					$this->Logging_model->items(2,$items);
					$this->Item_model->move_to_trash($items);
				} else { //confirmation==yes
					$this->Logging_model->items(3,$items);
					$this->Item_model->mark_for_deletion($items);
					$category_id=$this->Item_model->get_by_id($items[0], $this->user_data['organization_id'])->category_id;
				}
				
				
				
				$text = count($items)>1?'Items':'Item';
				
				$text = $text." tossed successfully.";
				
					if($search_delete_form){
						echo '{ "result": "successful_input", "grid_callback": "/search/search_grid/", "message_type": "success", "message": "'.$text.'"}';						
					} else if($category_delete_form){
						echo '{ "result": "successful_input", "grid_callback": "/categories/category_grid/'.$category_id.'", "message_type": "success", "message": "'.$text.'"}';
					} else {
						echo '{ "result": "successful_input", "grid_callback": "/locations/location_grid/'.$location_id.'", "message_type": "success", "message": "'.$text.'"}';
					}				
				

			} else {
				echo '{ "result": "validation_error", "errors": {"missingFromForm":"<p>You must choose one of the options above</p>"}}';
			}
		} else {
		
			$is_pending = false;
			foreach($arr as $item){
				if($this->Transfers_model->item_is_pending($item)){
					$is_pending = true;
				}
			}
					
			if(count($arr)==0){
				$this->load->view('facebox_error',array('error'=>'Select an Item first.'));
			} else if($is_pending){
				$this->load->view('facebox_error',array('error'=>'One or more of the selected items have pending action. You cannot toss an item with pending actions.'));
			} else {
			
				//lets make sure they are authorized to delete this
				$items = $this->Item_model->get_by_ids($arr, $this->user_data['organization_id']);
				
				if($items->num_rows() == 0) {
					$this->load->view('facebox_error',array('error'=>'You are not authorized to delete this item.'));
					return;
				}
				
				if($items->num_rows() != count($arr)) {
					$this->load->view('facebox_error',array('error'=>'You are not authorized to delete one or more of the items selected.'));
					return;
				}
			
				$data = array(
									'items' => $items->result_array()
				);
				

				
				$this->load->view('item_delete_confirmation', $data);
			}
		}
	}
}
?>
