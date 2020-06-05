<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function database_exists($database_id)
{
        $ci =& get_instance();
	$get_database=$ci->Database_model->get_by_id($database_id);
	if(!$get_database):
			$ci->session->set_flashdata('error', 'You no longer have access to this database or it has been deleted.');
			redirect(site_url());
	endif;		
}		
function location_exists($location_id)
{
        $ci =& get_instance();
	$get_location=$ci->Location_model->get_by_id($location_id);
	if(!$get_location):
			$ci->session->set_flashdata('error', 'You no longer have access to this location or it has been deleted.');
			redirect(site_url());
	endif;
}
function category_exists($category_id)
{
        $ci =& get_instance();
	$get_category=$ci->Category_model->get_by_id($category_id);
	if(!$get_category):
			$ci->session->set_flashdata('error', 'You no longer have access to this category or it has been deleted.');
			redirect(site_url());
	endif;
}
function item_exists($item_id)
{
        $ci =& get_instance();
	$get_item=$ci->Item_model->get_by_id($item_id);
	if(!$get_item):
			return false;
	endif;	
	if($get_item):
		if($get_item->transfer_status_id > 0):
			return false;
		endif;
	endif;	
	return true;
}
