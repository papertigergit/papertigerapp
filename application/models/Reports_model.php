<?php
class Reports_model extends CI_Model {
    function __construct() {
			parent::__construct();
			$this->load->model(array('Location_model'));
			$this->load->database();
    }


    function file_list_report(){
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
		$location_file_list = $this->session->userdata('location_file_list');
		if($this->session->userdata('location_file_list'))
			{
				foreach($location_file_list as $loc){
					$location_ids[] = $loc;
				}
			}
		$category_ids = array();
		if($this->session->userdata('category_file_list'))
		{
			$category_file_list = $this->session->userdata('category_file_list');
			foreach($category_file_list as $cat){
				$category_ids[] = $cat;
			}
		}
		//Handles for adv search params
		$dat_h=$this->session->userdata('after_date');
		$loc_h=$this->session->userdata('location_file_list');
		$cat_h=$this->session->userdata('category_file_list');
		
		$sort_by="id";
		if($this->session->userdata('sort_by'))
		{
			$sort_by=$this->session->userdata('sort_by');
		}

		if((!$dat_h) && (!$loc_h) && (!$cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$loc_ids).")) order by ".$sort_by."";
		}
		if(($dat_h) && (!$loc_h) && (!$cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$loc_ids).")) AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') order by ".$sort_by."";
		}
		if((!$dat_h) && ($loc_h) && (!$cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).")) order by ".$sort_by."";
		}
		if((!$dat_h) && (!$loc_h) && ($cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$loc_ids).") AND `category_id` IN (".implode(',',$category_ids).")) order by ".$sort_by."";
		}
		if(($dat_h) && ($loc_h) && (!$cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).")) AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') order by ".$sort_by."";
		}
		if(($dat_h) && (!$loc_h) && ($cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$loc_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') order by ".$sort_by."";
		}
		if((!$dat_h) && ($loc_h) && ($cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) order by ".$sort_by."";
		}
		if(($dat_h) && ($loc_h) && ($cat_h))
		{
			$adv_query="SELECT DISTINCT items.*, pending_transfers.destination_location_id, pending_transfers.type, pending_transfers.merge_into  FROM `items` LEFT JOIN `pending_transfers` ON `pending_transfers`.`item_id` = `items`.`id` WHERE (`location_id` IN (".implode(',',$location_ids).") AND (`category_id` IN (".implode(',',$category_ids)."))) AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."')  order by ".$sort_by."";
		}
		$res = $this->db->query($adv_query);
		//exit(var_dump($this->db->last_query()));
		return $res;
	}


    function file_list_report_locs(){
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
		$location_file_list = $this->session->userdata('location_file_list');
		if($this->session->userdata('location_file_list'))
		{
			foreach($location_file_list as $loc){
				$location_ids[] = $loc;
			}
		}
		$category_ids = array();
		if($this->session->userdata('category_file_list'))
		{
			$category_file_list = $this->session->userdata('category_file_list');
			foreach($category_file_list as $cat){
				$category_ids[] = $cat;
			}
		}
		//Handles for adv search params
		$dat_h=$this->session->userdata('after_date');
		$loc_h=$this->session->userdata('location_file_list');
		$cat_h=$this->session->userdata('category_file_list');

		if((!$dat_h) && (!$loc_h) && (!$cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$loc_ids)."))  GROUP BY `location_id`";
		}
		if(($dat_h) && (!$loc_h) && (!$cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$loc_ids)."))  AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') GROUP BY `location_id`";
		}
		if((!$dat_h) && ($loc_h) && (!$cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$location_ids)."))  GROUP BY `location_id`";
		}
		if((!$dat_h) && (!$loc_h) && ($cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$loc_ids).") AND (`category_id` IN (".implode(',',$category_ids).")))  GROUP BY `location_id`";
		}
		if(($dat_h) && ($loc_h) && (!$cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$location_ids)."))  AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') GROUP BY `location_id`";
		}
		if(($dat_h) && (!$loc_h) && ($cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$loc_ids).") AND (`category_id` IN (".implode(',',$category_ids).")))  AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') GROUP BY `location_id`";
		}
		if((!$dat_h) && ($loc_h) && ($cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$location_ids).") AND (`category_id` IN (".implode(',',$category_ids).")))  GROUP BY `location_id`";
		}
		if(($dat_h) && ($loc_h) && ($cat_h))
		{
			$adv_query_locs_cats="SELECT count(*) as count, `location_id` FROM (`items`) WHERE (`location_id` IN (".implode(',',$location_ids).") AND (`category_id` IN (".implode(',',$category_ids).")))  AND (`action_date` <= '".$this->session->userdata('before_date')."' AND `action_date` >= '".$this->session->userdata('after_date')."') GROUP BY `location_id`";
		}
	    	$res = $this->db->query($adv_query_locs_cats);
		//print $this->db->last_query();
		return $res;
	}
}
?>
