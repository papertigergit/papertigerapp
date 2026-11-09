<?php

class Affiliates_model extends MY_Model {


    function __construct() {
        parent::__construct();
    }
    
    // Is the user an active affiliate?
    function is_affilate($user_id) {
        $this->db->where('id', $user_id);
        $this->db->from('users');
        $row = $this->db->get()->row();
        
        return ($row->is_affiliate == 1) ? TRUE : FALSE;
    }
    function get_ptd_affiliate($affiliate_code) {
	if($affiliate_code) {
		$this->db->select('affiliate_ptd_code');
		$this->db->where('affiliate_code', $affiliate_code);
		$query=$this->db->get('users');
		if($query->num_rows() > 0) {
			return $query->row()->affiliate_ptd_code;
		} else  {
			return NULL;
		}
	} else {
		return NULL;
	}
    }
    
    function modify($affiliate_code, $first_name, $last_name, $street, $street2, $city, $state, $zip, $tax, $level, $paypal) {
    	
    	$data = array(	'affiliate_first_name'	=>	$first_name,
    					'affiliate_last_name'	=>	$last_name,
    					'affiliate_street'		=>	$street,
    					'affiliate_street_2'	=>	$street2,
    					'affiliate_city'		=>	$city,
    					'affiliate_state'		=>	$state,
    					'affiliate_zip'			=>	$zip,
    					'affiliate_tax'			=>	$tax,
    					'affiliate_level'		=>	$level,
    					'affiliate_paypal'		=>	$paypal
    					
    	);
    	
    	$this->db->where('affiliate_code', $affiliate_code);
    	$this->db->update('users', $data);
    }
    
    function get_all($affiliate_code) {        
    	$this->db->select('affiliate_tracking.*, affiliate_tracking.organization_name as org_name, organizations.name as organization_name, org_user.email_address as organization_email, users.affiliate_code as user_affiliate_code, users.first_name, users.last_name, users.email_address');
		$this->db->where('affiliate_tracking.affiliate_code', $affiliate_code);
        $this->db->join('organizations', 'organizations.id = affiliate_tracking.organization_id', 'left');
        $this->db->join('users org_user', 'org_user.id = organizations.admin_user', 'left');
        $this->db->join('users', 'users.affiliate_code = affiliate_tracking.affiliate_code', 'left');
        $this->db->order_by('original_date', 'desc');
        return $this->db->get('affiliate_tracking');
    }

    function check_affiliate_code($affiliate_code) {
        $this->db->where('affiliate_code', $affiliate_code);
        $query=$this->db->get('users');
		if($query->num_rows() > 0) {
			return TRUE;
		} else {
			return NULL;
		}
    }
    
    // Process the affiliate tracking on signup
    // -- $org_id = Id of the organization that signed up
    // -- $affiliate_code = code of the affiliate that got $user_id to signup
    function process_signup($org_id, $affiliate_code) {
        $data = array(
            'organization_id'           => $org_id,
            'is_active'                 => 1,
            'affiliate_code'            => $affiliate_code,
            'original_date'             => time(),
            'start_date'                => time(),
            'tier'                      => 0
        );
        
        $this->db->insert('affiliate_tracking', $data);
        
        //Since we are doing a two-tier system, we need to check the parent now
        $this->db->where('affiliate_code', $affiliate_code);
        $this_org_id = $this->db->get('users')->row()->organization_id;
        
        $this->db->where('organization_id', $this_org_id);
        $query = $this->db->get('affiliate_tracking');
	if($query->num_rows() > 0) {
		$parent_affiliate_code=$query->row()->affiliate_code;
	} else {
		$parent_affiliate_code=NULL;
	}
        
        $this->load->library('logger');
        $this->logger->log_message(array('type' => 'affiliates', 'message' => 'Parent affiliate:'.$parent_affiliate_code));
        
/*         Column affiliate_ptd_code does not exist in the affiliate_tracking table!!!! */
        
        if($parent_affiliate_code !== NULL) {
            $data = array(
                'organization_id'           => $org_id,
                'is_active'                 => 1,
                'affiliate_code'            => $parent_affiliate_code,
	/* 	'affiliate_ptd_code'        => $affiliate_ptd_code, */
                'original_date'             => time(),
                'start_date'                => time(),
                'tier'                      => 1,
                'tier_child'                => $affiliate_code
            );
		$this->db->insert('affiliate_tracking', $data);
        }
        
        
        
    }
    
    // Resets the start_date for the affiliate's commission.
    // --To be used with the upgrade/downgrade functions
    function process_date_change($org_id, $set_active=1) {    
        $data = array(
            'start_date'                => time(),
            'is_active'                 => $set_active
            
        );
        
        $this->db->where('organization_id', $org_id);
        $this->db->update('affiliate_tracking', $data);
    }
    
    // This searches by user's affiliate code
    function get_referrals() {
        $this->db->select('affiliate_tracking.*, organizations.name');
        $this->db->where('affiliate_code', $this->user_data['affiliate_code']);
        $this->db->join('organizations', 'affiliate_tracking.organization_id = organizations.id', 'left');
        return $this->db->get('affiliate_tracking');    
    }
    
    function get_latest($limit=5) {
    	$this->db->select('affiliate_tracking.*, affiliate_tracking.organization_name as org_name, organizations.name as organization_name, org_user.email_address as organization_email, users.affiliate_code as user_affiliate_code, users.first_name, users.last_name, users.email_address');
        $this->db->where('is_active', 1);
        $this->db->where('tier', 0); //limit to direct affiliates only or this will have a lot of useless info
        $this->db->join('organizations', 'organizations.id = affiliate_tracking.organization_id', 'left');
        $this->db->join('users org_user', 'org_user.id = organizations.admin_user', 'left');
        $this->db->join('users', 'users.affiliate_code = affiliate_tracking.affiliate_code', 'left');
        $this->db->limit($limit);
        $this->db->order_by('original_date', 'desc');
        return $this->db->get('affiliate_tracking');
    }
    
    // This gets all the pending payouts
    function get_pending_payouts() {
        
        // We need within 3 months if the initial payment hasn't been made
        // We need within 1 year of last payment date if this is the second or greater payment.
        
        return $this->db->query('
            SELECT affiliate_tracking.id, affiliate_tracking.affiliate_code,users.first_name,users.last_name
                FROM affiliate_tracking
                LEFT JOIN
                	users ON users.affiliate_code = affiliate_tracking.affiliate_code
                WHERE
                    is_active = 1 AND
                    initial_payment_made = 0 AND
                    unix_timestamp((CURDATE() - INTERVAL 3 MONTH)) > start_date
                GROUP BY affiliate_tracking.affiliate_code
                
            UNION
            
            SELECT affiliate_tracking.id, affiliate_tracking.affiliate_code,users.first_name,users.last_name
                FROM affiliate_tracking
                LEFT JOIN
                	users ON users.affiliate_code = affiliate_tracking.affiliate_code
                WHERE
                    is_active = 1 AND
                    initial_payment_made = 1 AND
                    unix_timestamp((CURDATE() - INTERVAL 1 YEAR)) > last_payment_date
                GROUP BY affiliate_tracking.affiliate_code
        ');
        
        
    }
    
    // This gets the pending payouts for a specific affiliate
    function get_affiliate_pending_payouts($affiliate_code) {

        // We need within 3 months if the initial payment hasn't been made
        // We need within 1 year of last payment date if this is the second or greater payment.

        return $this->db->query('
        
            SELECT `affiliate_tracking`.`id` as tracking_id, manual_payment_amount, tier, `organization_id`, `original_date`, `start_date`, `last_payment_date`, `packages`.`price`, `packages`.`billing_cycle`, `organizations`.`name` as organization_name
            FROM `affiliate_tracking` 
            LEFT JOIN `organizations` 
                ON `organizations`.`id` = `affiliate_tracking`.`organization_id` 
            LEFT JOIN `packages` 
            ON `packages`.`id` = `organizations`.`package_id` 
            WHERE 
                `is_active` = 1 AND 
                `organization_id` IS NOT NULL AND 
                `initial_payment_made` = 0 AND
                unix_timestamp((CURDATE() - INTERVAL 3 MONTH)) > start_date AND
                `affiliate_code` = ?
        
        
            UNION
            
            SELECT `affiliate_tracking`.`id` as tracking_id, manual_payment_amount, tier, `organization_id`, `original_date`, `start_date`, `last_payment_date`, `packages`.`price`, `packages`.`billing_cycle`, `organizations`.`name` as organization_name
            FROM `affiliate_tracking` 
            LEFT JOIN `organizations` 
                ON `organizations`.`id` = `affiliate_tracking`.`organization_id` 
            LEFT JOIN `packages` 
            ON `packages`.`id` = `organizations`.`package_id` 
            WHERE 
                `is_active` = 1 AND 
                `organization_id` IS NOT NULL AND
                `initial_payment_made` = 1 AND
                unix_timestamp((CURDATE() - INTERVAL 1 YEAR)) > last_payment_date AND
                `affiliate_code` = ?
                
			UNION
			
			SELECT `affiliate_tracking`.`id` as tracking_id, manual_payment_amount, tier, `organization_id`, `original_date`, `start_date`, `last_payment_date`, `manual_payment_amount` as price, "yearly" as `billing_cycle`, `organization_name`
			FROM `affiliate_tracking`
            WHERE 
                `is_active` = 1 AND 
                `organization_id` IS NULL AND
                `initial_payment_made` = 0 AND
                unix_timestamp((CURDATE() - INTERVAL 3 MONTH)) > start_date AND
                `affiliate_code` = ?
                
			UNION
			
			SELECT `affiliate_tracking`.`id` as tracking_id, manual_payment_amount, tier, `organization_id`, `original_date`, `start_date`, `last_payment_date`, `manual_payment_amount` as price, "yearly" as `billing_cycle`, `organization_name`
			FROM `affiliate_tracking`
            WHERE 
                `is_active` = 1 AND 
                `organization_id` IS NULL AND
                `initial_payment_made` = 1 AND
                unix_timestamp((CURDATE() - INTERVAL 1 YEAR)) > last_payment_date AND
                `affiliate_code` = ?
        
        ', array($affiliate_code, $affiliate_code, $affiliate_code, $affiliate_code));

    }
    
    function get_latest_payments($num=5) {
        $this->db->limit($num);
        return $this->db->get('affiliate_payments');
    }
    
    function get_level($level) {
        $this->db->where('id', $level);
        return $this->db->get('affiliate_levels');
    }
    
    function create_payment($affiliate_code, $amount) {
        $data = array(
            'affiliate_code'    => $affiliate_code,
            'amount'            => $amount,
            'date'              => time()
        );
        
        $this->db->insert('affiliate_payments', $data);
    }
    
    function mark_as_paid($id, $keep_active=0) {
        $data = array(
            'last_payment_date'     => time(),
            'initial_payment_made'  => 1,
            'is_active'             => $keep_active
        );
    
        $this->db->where('id', $id);
        $this->db->update('affiliate_tracking', $data);
    }
    
        /*     
            This is for when a user is signing up as an affiliate 
        */
    function process_registration($user_id, $first_name, $last_name, $street, $street2, $city, $state, $zip, $tax, $paypal) {
    
        //Just generates a random code to use as the affiliate_code
        $character_set_array = array( );
        $character_set_array[ ] = array( 'count' => 7, 'characters' => 'abcdefghijklmnopqrstuvwxyz' );
        $character_set_array[ ] = array( 'count' => 1, 'characters' => '0123456789' );
        $temp_array = array( );
        foreach ( $character_set_array as $character_set )
        {
          for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
          {
            $temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
          }
        }
        shuffle( $temp_array );
        $code = implode( '', $temp_array );

        
        try {
        
        
            $data = array(
                'affiliate_code'        => $code, 
                'affiliate_level'       => 1,
                'affiliate_first_name'  => $first_name,
                'affiliate_last_name'   => $last_name,
                'affiliate_street'      => $street,
                'affiliate_street_2'    => $street2,
                'affiliate_city'        => $city,
                'affiliate_state'       => $state,
                'affiliate_zip'         => $zip,
                'affiliate_tax'         => $tax,
                'affiliate_paypal'		=> $paypal
            );
        
            $this->db->where('id', $user_id);
            $this->db->update('users', $data);
        } catch (Exception $e) {
            return $this->generate_code($user_id);
        }
        
        return $code;
    }
    
    function get_affiliate_code($expert_code){
    $affiliate_code = $expert_code;
    $this->Organization_model->get_all_experts();
    $this->db->where('affiliate_code', $affiliate_code);
    return $this->db->get_where('users', 'email_address');    
    }

    function set_approved_for_org($data, $user_organization)
	{
	$user_org = $user_organization;
	$form_data = $data;
	/* $data = array('has_expert' => '0'); */
		$this->db->where('id', $user_org);
		$this->db->update('organizations', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			return TRUE;
		}
		
		return FALSE;
	}
    function set_expert_for_org($form_data, $user_org)
	{
	$j = $this->user_data['id'];
	/* $data = array('has_expert' => '0'); */
		$this->db->where('admin_user', $j);
		$this->db->update('organizations', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			return TRUE;
		}
		
		return FALSE;
	}
	function profile_update($data, $user_id){
		$user_id = $this->user_data['id'];
		$this->db->where('id', $user_id);
		$this->db->update('users', $data);
	}

  function update_affiliate_level($data, $user_id){
		$this->db->where('id', $user_id);
		$this->db->update('users', $data);
  }

  function assign_expert($organization_id){

    $this_org_id = $organization_id;
    $this->db->where('organization_id', $this_org_id);
    $query = $this->db->get('affiliate_tracking')->row()->affiliate_code;

    return $query;
  
  }
	
/*
	var $gallery_path;
	
	function Gallery_model(){
		parent::Model();
		$this->gallery_path = realpath(APPPATH . '../images');
	}
*/
/*
	function upload_picture(){
	
	
		$config = array(
			''	=> '',
			''	=> $this->galley_path
		);
		
		$this->load->library('', $config);
		$this->upload->do_upload();
	}
*/

    
    function list_all()
    {
    	$this->db->where('affiliate_code IS NOT NULL');
		$this->db->order_by('last_name', 'asc');
		return $this->db->get('users');    
    }
    
    function manual_entry($affiliate_code, $org_id, $date, $price) {

        $data = array(
            'organization_id'           => $org_id,
            'is_active'                 => 1,
            'affiliate_code'            => $affiliate_code,
            'manual_payment_amount'		=> $price,
            'original_date'             => $date,
            'start_date'                => $date,
            'tier'                      => 0
        );
        
        $this->db->insert('affiliate_tracking', $data);
        
        //Since we are doing a two-tier system, we need to check the parent now
        $this->db->where('affiliate_code', $affiliate_code);
        $this_org_id = $this->db->get('users')->row()->organization_id;
        
        $this->db->where('organization_id', $this_org_id);
        $query = $this->db->get('affiliate_tracking');
		if($query->num_rows() > 0) {
			$parent_affiliate_code=$query->row()->affiliate_code;
		} else {
			$parent_affiliate_code=NULL;
		}
        
        $this->load->library('logger');
        $this->logger->log_message(array('type' => 'affiliates', 'message' => 'Parent affiliate:'.$parent_affiliate_code));
        
        if($parent_affiliate_code !== NULL) {
            $data = array(
                'organization_id'        	=> $org_id,
                'is_active'                 => 1,
                'affiliate_code'            => $parent_affiliate_code,
                'original_date'             => $date,
                'start_date'                => $date,
                'tier'                      => 1,
                'tier_child'                => $affiliate_code
            );
			$this->db->insert('affiliate_tracking', $data);
        }
    
    
    
    }


}
