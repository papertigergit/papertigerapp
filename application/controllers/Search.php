<?php
require_once(APPPATH.'../vendor/autoload.php');
class Search extends CI_Controller {
    function __construct() {
        parent::__construct();
         
	$this->load->database();
        $this->load->library('authentication', 'session');
        $this->load->helper(array('url', 'text', 'empty_messages','form'));
        $this->load->model(array('Location_model', 'Item_model', 'Search_model', 'Category_model'));
        global $user_data;
        $user_data = $this->session->userdata('user_data');
    }
     
    function index() {
        $this->authentication->require_authentication();
        global $user_data;
 
        $database_id=$this->session->userdata('database_id');
 
        $network_utils = array();
        $network_utils = enable_buttons();
         
        //reset all search terms
        $this->session->set_userdata('unformatted_search_string','');
        $this->session->set_userdata('search_string','');         
        $this->session->set_userdata('adv_category','');          
        $this->session->set_userdata('adv_location','');          
        $this->session->set_userdata('after_date','');            
        $this->session->set_userdata('before_date','');
        $this->session->set_userdata('adv_digital_files','');           
        $show_notes=$this->Settings_model->get_all();
        $notes_settings=array($show_notes->result_array());
        $notes_active=0;
        if($show_notes->num_rows() > 0) {
            if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL) {
                $notes_active=1;
            }
        }
        $this->session->set_userdata('notes_active',$notes_active);           
 
        $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
        $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
        $is_network = $this->Network_model->is_network($package_id);
            if($is_network->num_rows() == 0):
                $locations=$this->Location_model->get_from_database();
            endif;
            if($is_network->num_rows() > 0):
                $locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
            endif;
            if($is_network->num_rows() > 0 && $organization_admin==$this->user_data['id']):
                $locations=$this->Location_model->get_from_database();
            endif;
 
 
        $total_locations=$locations->num_rows();
        if($total_locations == 0)
        {
            $this->session->set_flashdata('error', "You don't have any locations to search in yet.");
            redirect('locations');
        }
        if($this->input->post('search_string')){
            $safe_string=$this->db->escape_str($this->input->post('search_string'));
            $this->session->set_userdata('unformatted_search_string', $this->input->post('search_string'));
            $this->session->set_userdata('adv_digital_files', $this->input->post('digital_files'));
            $this->session->set_userdata('search_string', $safe_string);
        }
        if($this->input->post('category')){
            $this->session->set_userdata('adv_category', $this->input->post('category'));
        }
        if($this->input->post('location')){
            $this->session->set_userdata('adv_location', $this->input->post('location'));
        }
        if($this->input->post('date_range')){
            if(preg_match('/[A-Za-z]/',$this->input->post('date_range'))){
                $this->session->set_flashdata('error', "The date field has illegal characters.");
                $referer=$_SERVER['HTTP_REFERER'];
                redirect($referer);
            }
            $date_range=explode("-",$this->input->post('date_range'));
            if(sizeof($date_range)==1){
                $date_range[1]=$date_range[0];
            }
            $after_date=(explode("/",$date_range[0]));
            $after_date[2]=intval($after_date[2]);
            $after_date=mktime(0,0,0,$after_date[0],$after_date[1],$after_date[2]);
            $this->session->set_userdata('after_date', $after_date);
 
            $before_date=(explode("/",$date_range[1]));
            $before_date=mktime(23,59,59,$before_date[0],$before_date[1],$before_date[2]);
            $this->session->set_userdata('before_date', $before_date);
 
        }
        if(($this->session->userdata('adv_category')) || ($this->session->userdata('adv_location')) || ($this->session->userdata('after_date'))) //is advanced search
        {
            $grid_data = $this->Search_model->advanced_search($this->session->userdata('search_string'));
            $location_result_data = $this->Search_model->advanced_search_locs_cats($this->session->userdata('search_string'));
        }
        else
        {
            $grid_data = $this->Search_model->search($this->session->userdata('search_string'));
            $location_result_data = $this->Search_model->search_locs($this->session->userdata('search_string'));
        }
        if(($this->session->userdata('adv_digital_files'))) //is advanced search
        {
            $digital_files=$this->input->post('digital_files');
        }
        //else
        //{
            //$grid_data = $this->Search_model->search($this->session->userdata('search_string'));
            //$location_result_data = $this->Search_model->search_locs($this->session->userdata('search_string'));
        //}

       $digital_files=$this->input->post('digital_files');
       $unconverted_search = array();
       $converted_search = array();
       $digital_results_found=false;


      if($digital_files):
        $digital_results_found=true;
            $google_users=$this->google_users_model->get_token($this->user_data['id']);
            if($google_users->num_rows() == 0) {
                $this->session->set_flashdata('error', "Your google credentials have been revoked by your administrator.");
            } else {
                
                $google_users_data=$google_users->result_array();
                $array = array('access_token' => $google_users_data[0]['oauth_token'], 'refresh_token' => $google_users_data[0]['oauth_token_secret']);
                
                $saved_oauth_token = json_encode($array);

                $client_id = '';
                $client_secret = '';
                $client = new Google_Client();
                $client->setClientId($client_id);
                $client->setClientSecret($client_secret);
                $client->addScope("https://www.googleapis.com/auth/drive.readonly");
                $client->setAccessToken($saved_oauth_token);
                $jsontoken = json_decode($saved_oauth_token, true);
                $cleantoken = $jsontoken['refresh_token'];
                $client->refreshToken($cleantoken);
                $dr_service = new Google_Service_Drive($client);
                $dr_results = $dr_service->files->listFiles(array('q' => "fullText contains '".$this->session->userdata('search_string')."'",'fields' => 'nextPageToken, files(id, name, iconLink, mimeType, createdTime, modifiedTime, webViewLink, owners)'));
/*
               
if (isset($_GET['code']))
   {
   session_start();
   print_r($_SESSION);
   $client->authenticate($_GET['code']);
   $_SESSION['token'] = $client->getAccessToken();
   $client->setAccessToken($_SESSION['token']);
   // initialize the drive service with the client.
   $services = new Google_DriveService($client);
   retrieveAllFiles($services);    

  }
  */              
            }  

            //$digital_results_found=true;

            if($dr_results->count()>0)
            {
                //$digital_results_found=true; 
                $pendingarray = array("application/pdf","application/postscript");
                $docsarray = array("application/vnd.google-apps.spreadsheet","application/vnd.google-apps.document","application/vnd.google-apps.presentation", "application/vnd.ms-powerpoint", "application/vnd.ms-excel", "application/pdf", "application/postscript", 
                "image/bmp", "application/msword", "application/excel", "image/gif", "image/jp2", "image/jpeg", "image/pict", "image/png", "image/x-macpaint", "image/x-quicktime", "text/richtext", "text/rtf" , "image/tiff", "image/png", "text/plain");
                //$unconverted_search = array();
                //$converted_search = array();
                foreach($dr_results as $item){
                     if(in_array($item->mimeType, $pendingarray)){
                       array_push($unconverted_search, array('title'=>$item->name,'createdDate'=>$item->createdTime,'modifiedDate'=>$item->modifiedTime,'mimeType'=>$item->mimeType,'owner'=>$item->owners[0]->displayName,'link'=>$item->webViewLink,'iconLink'=>$item->iconLink));
                     } else if(in_array($item->mimeType, $docsarray)){
                       array_push($converted_search, array('title'=>$item->name,'createdDate'=>$item->createdTime,'modifiedDate'=>$item->modifiedTime,'mimeType'=>$item->mimeType,'owner'=>$item->owners[0]->displayName,'link'=>$item->webViewLink,'iconLink'=>$item->iconLink));
                     }
                }
            }
            else
            {
              //echo $return;
	      echo "";
            }
            

        endif;  
         
        $network_utils = array();
        $network_utils = enable_buttons();
         
        $data = array (
            
                            'user_data' => $user_data,
                            'body_id'=>'browse',
                            'locations'=>$locations,
                            'search_string'=>$this->session->userdata('search_string'),
                            'organization_admin' => $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user,
                            'table_data'=>$grid_data,
                            'buttons_on' => $network_utils['buttons_on'],
                            'digital_results_found' => $digital_results_found,
                            'is_network' => $network_utils['is_network'],
                            'location_result_data'=>$location_result_data,
                            'unconverted_files'=>$unconverted_search,
                            'converted_files'=>$converted_search,
                            'modified_rows'=>$this->session->flashdata('modified_rows')
        );
        $this->load->view('header', $data);
        $this->load->view('search_results', $data);
        $this->load->view('footer');
    }
     
    function search_grid() {
        $this->authentication->require_authentication();
        global $user_data;
         
        if($this->input->post('search_string')){
            $safe_string=mysql_real_escape_string($this->input->post('search_string'));
            $this->session->set_userdata('search_string', $safe_string);
        }
        if($this->input->post('category')){
            $this->session->set_userdata('adv_category', $this->input->post('category'));
        }
        if($this->input->post('location')){
            $this->session->set_userdata('adv_location', $this->input->post('location'));
        }
        if($this->input->post('before_date')){
            $this->session->set_userdata('before_date', $this->input->post('before_date'));
        }
        if($this->input->post('after_date')){
            $this->session->set_userdata('after_date', $this->input->post('after_date'));
        }
         
        $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
        $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
        $is_network = $this->Network_model->is_network($package_id);
            if($is_network->num_rows() == 0):
                $locations=$this->Location_model->get_from_database();
            endif;
            if($is_network->num_rows() > 0):
                $locations = $this->Location_model->get_from_network_database($this->session->userdata('database_id'));
            endif;
            if($is_network->num_rows() > 0 && $organization_admin==$this->user_data['id']):
                $locations=$this->Location_model->get_from_database();
            endif;
 
        if(($this->session->userdata('adv_category')) || ($this->session->userdata('adv_location')) || ($this->session->userdata('after_date'))) //is advanced search
        {
            $grid_data = $this->Search_model->advanced_search($this->session->userdata('search_string'));
            $location_result_data = $this->Search_model->advanced_search_locs_cats($this->session->userdata('search_string'));
        }
        else
        {
            $grid_data = $this->Search_model->search($this->session->userdata('search_string'));
            $location_result_data = $this->Search_model->search_locs($this->session->userdata('search_string'));
        }
        $data = array(
                            'user_data' => $user_data,
                            'body_id'=>'browse',
                            'locations'=>$locations,
                            'organization_admin' => $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user,
                            'search_string'=>$this->session->userdata('search_string'),
                            'table_data'=>$grid_data,
                            'location_result_data'=>$location_result_data,
			    'unconverted_files'=>[],
                            'converted_files'=>[],
			    'digital_results_found' => false,
                            'modified_rows'=>$this->session->flashdata('modified_rows')
        );
         
        $this->load->view('search_results', $data);
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
    }

?>
