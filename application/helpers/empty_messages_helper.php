<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('empty_message_view'))
{

    /*
        Fede:
        1
        you have a brand new db, you have write access to the db, and no locations
        so we'll show you a message saying so
        2
        you are in a db, but you can't see any locations for whatever reason but you can write to the db
        so the same thing happens
        3
        you are in a db, you have viewing access to the db but it's empty and you have no write access
        so we tell you so in the message
     */
    
    function admin_info()
    {

        $ci =& get_instance();
        

        $admin_info = array(
                    );

        return $admin_info;

    }

    function enable_buttons()
    {

        $ci =& get_instance();
        $ci->load->model('Permissions_model');
    
        //LOGIC TO TURN ON BUTTONS IN VIEW
            $buttons_on = false;
            $can_edit = false;
            
            $package_id=$ci->Organization_model->get_package_id($ci->user_data['organization_id']);
            $is_network=$ci->Network_model->is_network($package_id);
            $organization_admin = $ci->Permissions_model->get_organization_admin($ci->user_data['organization_id'])->admin_user;

            //IF YOU ARE A NETWORK ACCOUNT, ONLY SHOW BUTTONS IF YOU HAVE WRITE ACCESS
            if($is_network->num_rows() > 0):
                $database_perms=$ci->Permissions_model->get_by_database($ci->session->userdata('database_id'),$ci->user_data['id']);
		$database_permissions=null;
		if($database_perms):
			$database_permissions=$database_perms->pto_umask;
		endif;	

                if (($database_permissions == '1') || ($database_permissions == '3')) {
                    $can_edit="edit_true";
                    $buttons_on = true;
                }
            endif;

            //IF YOU ARE ADMIN (FUCKING A MORRIS!) TURN ON BUTTONS
            if(($is_network->num_rows() > 0) && ($organization_admin == $ci->user_data['id'])):
				$can_edit="edit_true";
                    $buttons_on = true;
            endif;

            //SHOW BUTTONS BY DEFAULT ON SINGLE USER PLANS
            if($is_network->num_rows() == 0):
                $can_edit="edit_true";
                $buttons_on = true;
            endif;

$ci->load->model('User_model');

		$organization_admin = $ci->Permissions_model->get_organization_admin($ci->user_data['organization_id']);
        $admin_email = $ci->User_model->get_user($organization_admin->admin_user)->row()->email_address;
        $admin_name = $ci->User_model->get_user($organization_admin->admin_user)->row()->first_name;
        $admin_name .= ' '.$ci->User_model->get_user($organization_admin->admin_user)->row()->last_name;

            $network_utils = array(
                'can_edit' => $can_edit,
                'buttons_on' => $buttons_on,
                'is_network' => $is_network,
                'admin_email' => $admin_email,
                'admin_name' => $admin_name
            );
            
            return $network_utils;
    }

    function empty_message_view($database_id)
    {

        $ci =& get_instance();
    
        $ci->load->model('Permissions_model');
    
        $organization_admin     = $ci->Permissions_model->get_organization_admin($ci->user_data['organization_id'])->admin_user;
        $database_permissions   = $ci->Permissions_model->get_by_database($ci->session->userdata('database_id'),$ci->user_data['id']);
		$organization_admin = $ci->Permissions_model->get_organization_admin($ci->user_data['organization_id'])->admin_user;

        $package_id=$ci->Organization_model->get_package_id($ci->user_data['organization_id']);
        $is_network = $ci->Network_model->is_network($package_id);

        // print_r('is_network='.$is_network->num_rows().'<br />');

			if($is_network->num_rows() == 0):
				$locations=$ci->Location_model->get_from_database();
			endif;
			if($is_network->num_rows() > 0):
				$locations = $ci->Location_model->get_from_network_database($database_id);
            endif;

            if(($is_network->num_rows() > 0) && ($organization_admin == $ci->user_data['id'])):
				$locations = $ci->Location_model->get_from_database();
            endif;

            // $locations = $ci->Location_model->get_from_network_database($database_id);
				// $locations = $ci->Location_model->get_from_database();
            
        // print_r('locations='.$locations.'<br />');
        // print_r('locations='.$locations->num_rows().'<br />');

         if ($locations->num_rows() === 0) {
        
            $db_umask=0;
                        
            if($database_permissions) {
                $db_umask=$database_permissions->pto_umask;
            }
            if($organization_admin == $ci->user_data['id']){
                $db_umask=1;
            }

            // print_r('db_umask='.$db_umask.'<br />');

            if ($db_umask == '1') {
               return 'empty_messages/no_locations';
            }

            elseif ($db_umask== '3') {
                return 'empty_messages/no_permissions';
            }

            elseif ($db_umask== '2') {
                return 'empty_messages/no_permissions';
            }

            
            return FALSE;

        }
        
        if ($locations->num_rows() > 0) {
            return 'empty_messages/allow';
        }
    }
}

?>
