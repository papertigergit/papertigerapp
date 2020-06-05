<?php
class Search_model extends CI_Model {

    function __construct() {
			parent::__construct();
			$this->load->model(array('Location_model'));
			$this->load->database();
    }
    
    function search_locs($search){
    	$loc_ids = array();



		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		if($is_network->num_rows() == 0):
			$locs=$this->Location_model->get_from_database()->result_array();
		endif;
		if($is_network->num_rows() > 0):
			$locs = $this->Location_model->get_from_network_database($this->session->userdata('database_id'))->result_array();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$locs=$this->Location_model->get_from_database()->result_array();
		endif;



	    foreach($locs as $loc){
		    $loc_ids[] = $loc['id'];
	    }
	$notes_active=$this->session->userdata('notes_active');
	$get_notes='';
	if($notes_active == 1) {
		$get_notes=" OR `description` LIKE '%".$search."%'";
	}
	    
	    $res = $this->db->query("SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$loc_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") GROUP BY `location_id`");
			return $res;
    }
    
    function search($search){
    	$loc_ids = array();
		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		if($is_network->num_rows() == 0):
			$locs=$this->Location_model->get_from_database()->result_array();
		endif;
		if($is_network->num_rows() > 0):
			$locs = $this->Location_model->get_from_network_database($this->session->userdata('database_id'))->result_array();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$locs=$this->Location_model->get_from_database()->result_array();
		endif;

	    foreach($locs as $loc){
		    $loc_ids[] = $loc['id'];
	    }
	    		$notes_active=$this->session->userdata('notes_active');
			$get_notes='';
			if($notes_active == 1) {
				$get_notes=" OR `description` LIKE '%".$search."%'";
			}
			$res = $this->db->query("SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$loc_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") ORDER BY `location_id`");
	    
			return $res;
    }
    function advanced_search($search){
    		$loc_ids = array();

		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		if($is_network->num_rows() == 0):
			$locs=$this->Location_model->get_from_database()->result_array();
		endif;
		if($is_network->num_rows() > 0):
			$locs = $this->Location_model->get_from_network_database($this->session->userdata('database_id'))->result_array();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$locs=$this->Location_model->get_from_database()->result_array();
		endif;

	    		foreach($locs as $loc){
		    	$loc_ids[] = $loc['id'];
	    	}

	    	$location_ids=array();
		$adv_location = $this->session->userdata('adv_location');
		if($this->session->userdata('adv_location'))
			{
				foreach($adv_location as $loc){
					$location_ids[] = $loc;
				}
			}
		$category_ids = array();
		if($this->session->userdata('adv_category'))
		{
			$adv_category = $this->session->userdata('adv_category');
			foreach($adv_category as $cat){
				$category_ids[] = $cat;
			}
		}
		//Handles for adv search params
		$dat_h=$this->session->userdata('after_date');
		$loc_h=$this->session->userdata('adv_location');
		$cat_h=$this->session->userdata('adv_category');
		$notes_active=$this->session->userdata('notes_active');
		$get_notes='';
			if($notes_active == 1) {
				$get_notes=" OR `keywords` LIKE '%".$search."%'";
			}
		if(($dat_h) && (!$loc_h) && (!$cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$loc_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."')  ORDER BY `location_id`";
		}
		if((!$dat_h) && ($loc_h) && (!$cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") ORDER BY `location_id`";
		}
		if((!$dat_h) && (!$loc_h) && ($cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$loc_ids).") AND `category_id` IN (".implode(',',$category_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") ORDER BY `location_id`";
		}
		if(($dat_h) && ($loc_h) && (!$cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') ORDER BY `location_id`";
		}
		if(($dat_h) && (!$loc_h) && ($cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$loc_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') ORDER BY `location_id`";
		}
		if((!$dat_h) && ($loc_h) && ($cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") ORDER BY `location_id`";
		}
		if(($dat_h) && ($loc_h) && ($cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') ORDER BY `location_id`";
		}
//		$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).") OR `category_id` IN (".implode(',',$category_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' OR `description` LIKE '%".$search."%') AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') ORDER BY `location_id`";
		$res = $this->db->query($adv_query);
		//print $this->db->last_query();
		return $res;
	}
    function advanced_search_locs_cats($search){
    		$loc_ids = array();
		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network = $this->Network_model->is_network($package_id);
		$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

		if($is_network->num_rows() == 0):
			$locs=$this->Location_model->get_from_database()->result_array();
		endif;
		if($is_network->num_rows() > 0):
			$locs = $this->Location_model->get_from_network_database($this->session->userdata('database_id'))->result_array();
		endif;
		if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
			$locs=$this->Location_model->get_from_database()->result_array();
		endif;

	    		foreach($locs as $loc){
		    	$loc_ids[] = $loc['id'];
	    	}
	    		foreach($locs as $loc){
		    	$loc_ids[] = $loc['id'];
	    	}

		$adv_location = $this->session->userdata('adv_location');
		if($this->session->userdata('adv_location'))
		{
			foreach($adv_location as $loc){
				$location_ids[] = $loc;
			}
		}
		$category_ids = array();
		if($this->session->userdata('adv_category'))
		{
			$adv_category = $this->session->userdata('adv_category');
			foreach($adv_category as $cat){
				$category_ids[] = $cat;
			}
		}
		//Handles for adv search params
		$dat_h=$this->session->userdata('after_date');
		$loc_h=$this->session->userdata('adv_location');
		$cat_h=$this->session->userdata('adv_category');
		$notes_active=$this->session->userdata('notes_active');
		$get_notes='';
			if($notes_active == 1) {
				$get_notes=" OR `description` LIKE '%".$search."%'";
			}

		if(($dat_h) && (!$loc_h) && (!$cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$loc_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') GROUP BY `location_id`";
		}
		if((!$dat_h) && ($loc_h) && (!$cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$location_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") GROUP BY `location_id`";
		}
		if((!$dat_h) && (!$loc_h) && ($cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$loc_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") GROUP BY `location_id`";
		}
		if(($dat_h) && ($loc_h) && (!$cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$location_ids).")) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') GROUP BY `location_id`";
		}
		if(($dat_h) && (!$loc_h) && ($cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$loc_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') GROUP BY `location_id`";
		}
		if((!$dat_h) && ($loc_h) && ($cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$location_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") GROUP BY `location_id`";
		}
		if(($dat_h) && ($loc_h) && ($cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$location_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`name` LIKE '%".$search."%' OR `keywords` LIKE '%".$search."%' ".$get_notes.") AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') GROUP BY `location_id`";
		}
	    	$res = $this->db->query($adv_query_locs_cats);
		return $res;
	}
}
?>
