<?php
class Transfers_model extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->model(array('Item_model'));
		$this->load->database();
	}

	    function get_location_permissions_by_user($user_id=null) {
		    $this->db->select('location_id');
		    $this->db->where('pto_umask >',0);
		    $this->db->where('user_id',$user_id);
		    $query=$this->db->get('location_permissions');
		    return $query;
	    }

	function get_from_network_database() {
		global $user_data;
		$user_locations=$this->get_location_permissions_by_user($user_data['id']);
		$user_locs = array();
		foreach($user_locations->result() as $loc) {
			$user_locs[] = $loc->location_id;
		}
		if($user_locations->num_rows() > 0) {
			$this->db->where_in('items.location_id',$user_locs);
		} else {
			$this->db->where('items.location_id',null);
		}
    $this->db->select('pending_transfers.*, items.name')->from('pending_transfers')->where(array('pending_transfers.database_id' => $this->session->userdata('database_id')))->join('items', 'pending_transfers.item_id = items.id');
    $query = $this->db->get();
    //echo $this->db->last_query();
    return $query;
	}
	
	function get_from_database() {
    $this->db->select('pending_transfers.*, items.name')->from('pending_transfers')->where(array('pending_transfers.database_id' => $this->session->userdata('database_id')))->join('items', 'pending_transfers.item_id = items.id');
    $query = $this->db->get();
    //echo $this->db->last_query();
    return $query;
	}
	
	function get_by_id($id){
		return $this->db->get_where('pending_transfers', array('id' => $id))->row();
	}
	
	function cancel($id_arr) {
		foreach($id_arr as $id) {
			$this->db->trans_start();
				$transfer = $this->get_by_id($id);
				if($transfer->type == 'merge'){
					$this->db->where('id', $this->get_by_id($id)->item_id);
					$this->db->update('items', array('transfer_status_id'=>0));
					
					$res = $this->get_merge_sources($id);
					foreach($res->result() as $row){
						$this->db->where('id', $row->item_id);
						$this->db->update('items', array('transfer_status_id'=>0));
					}
					
					$this->db->delete('pending_transfers', array('id' => $id));
					$this->db->delete('pending_transfers', array('merge_into' => $id));
					
				} else {
					$this->db->where('id', $this->get_by_id($id)->item_id);
					$this->db->update('items', array('transfer_status_id'=>0));
					$this->db->delete('items', array('transfer_status_id' => $id));
					
					$this->db->set('empty_slots', 'empty_slots+1', false)->where('id', $transfer->destination_location_id);
				  $this->db->update('locations');
					
					$this->db->delete('pending_transfers', array('id' => $id));
				}
			$this->db->trans_complete();
		}
	}
	
	function item_is_pending($item_id){
		$query = $this->db->get_where('pending_transfers', array('item_id' => $item_id));
		return ($query->num_rows()>0);
	}
	function get_merge_sources($destination_id){
		return $this->db->get_where('pending_transfers', array('merge_into' => $destination_id));
	}
	
	function accept($id_arr){
		$this->db->trans_start();
			foreach($id_arr as $id){
				$transfer = $this->get_by_id($id);
				
				if($transfer->type == 'merge'){
				
					$this->db->where('id', $transfer->item_id);
					$data = array(
						'name' => $transfer->new_name,
						'keywords' => $transfer->new_keywords,
						'description' => $transfer->new_description,
						'category_id' => $transfer->new_category_id,
						'action_date' => $transfer->new_action_date,
						'transfer_status_id' => 0
					);
					$this->db->update('items', $data); 
					
					$res = $this->get_merge_sources($transfer->id);
					foreach($res->result() as $row){
						$destination_loc_id=$this->Item_model->get_by_id($transfer->item_id)->location_id;
						$this->db->set('empty_slots', 'empty_slots+1', false)->where('id', $destination_loc_id);
						$this->db->update('locations');
						$this->db->set('num_items', 'num_items-1', false)->where('id', $destination_loc_id);
						$this->db->update('locations');

						$this->db->delete('items', array('id' => $row->item_id));
						$this->db->delete('pending_transfers', array('id' => $row->id));
					}
					
				
				} else {
					if($transfer->destination_location_id == 0){
						$this->Item_model->move_to_trash(array($transfer->item_id));
					} else {
						if($transfer->type == 'move') {
							$slot = $this->db->get_where('items', array('location_id' => $transfer->destination_location_id, 'transfer_status_id' => $id))->row()->slot_num;
							$this->db->delete('items', array('location_id' => $transfer->destination_location_id, 'transfer_status_id' => $id));
							
							$this->db->set('empty_slots', 'empty_slots+1', false)->where('id', $transfer->destination_location_id);
					    $this->db->update('locations');
							
							$this->Item_model->move_item($transfer->item_id, $transfer->destination_location_id, $transfer->new_action_date, $slot);
						} else if($transfer->type == 'dupe') {
							$slot = $this->db->get_where('items', array('location_id' => $transfer->destination_location_id, 'transfer_status_id' => $id))->row()->slot_num;
							$this->Item_model->dupe_item($transfer->item_id, $transfer->destination_location_id, $transfer->new_action_date, $slot);
						//	$this->db->delete('items', array('location_id' => $transfer->destination_location_id, 'transfer_status_id' => $id));
							
							$this->db->set('empty_slots', 'empty_slots+1', false)->where('id', $transfer->destination_location_id);
							$this->db->update('locations');
							
							$this->db->set('transfer_status_id', null)->where('id', $transfer->item_id);
							$this->db->update('items');
							
						}
					}
				}
				
			}
			$this->db->where_in('id', $id_arr)->delete('pending_transfers');
			
		$this->db->trans_complete();
	}
	function origin_is_pending($location_id){
		$query = $this->db->get_where('pending_transfers', array('origin_location_id' => $location_id));
		//print $this->db->last_query();
		return ($query->num_rows()>0);
	}
	function destination_is_pending($location_id){
		$query = $this->db->get_where('pending_transfers', array('destination_location_id' => $location_id));
		//print $this->db->last_query();
		return ($query->num_rows()>0);
	}
	function pending_locations($location_id){
		$query = $this->db->query("select * from pending_transfers where `origin_location_id` IN ('".implode(',',$location_id)."') or `destination_location_id` IN ('".implode(',',$location_id)."')");
		return $query;
	}
}
