<?php

    /*
    //functions to check if db or location is writable
     function get_database_permissions(){
       $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
      $database_permissions=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id']);
        $db_umask=0;
        if($database_permissions) {
          $db_umask=$database_permissions->pto_umask;
        }
        if($organization_admin == $this->user_data['id']){
          $db_umask=1;
        }
      return $db_umask;
    }
     function get_location_permissions($location_id){
       $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
      $location_permissions=$this->Permissions_model->get_by_location($location_id,$this->user_data['id']);
        $loc_umask=0;
        if($location_permissions) {
          $loc_umask=$location_permissions->pto_umask;
        }
        if($organization_admin == $this->user_data['id']){
          $loc_umask=1;
        }
      return $loc_umask;
    }
    */


class Databases extends CI_Controller {
  function __construct() {
    parent::__construct();
    
    $this->load->library('authentication', 'session');
    $this->load->helper(array('url', 'text'));
    $this->load->model(array('Database_model', 'user_model'));
    global $user_data;
    $user_data = $this->session->userdata('user_data');
    include APPPATH.'libraries/PHPExcel.php';  
    include APPPATH.'libraries/PHPExcel/Writer/Excel2007.php';  
    
  //  $this->authentication->require_authentication();
  }
  function index($database_id=null,$function_name=null,$function_id=null) {
    $this->authentication->require_authentication();
    if($this->authentication->is_authenticated())
    {
      global $user_data; //this one function combines with url routing
    }
    else
    {
      $this->load->view('session_expired');
    }
  }

  function use_db($db_id=NULL){
    global $user_data;
    $database = $this->Database_model->get_by_id($db_id, $this->user_data['organization_id']);
          $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
    $is_network = $this->Network_model->is_network($package_id);
    $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id']);
    if($organization_admin) {
      $organization_admin = $organization_admin->admin_user;
    }

    if($organization_admin == $this->user_data['id']):
      $db_pto_umask=null;
      $db_mask=0;
    endif;  
    if($organization_admin != $this->user_data['id']):
      $db_pto_umask=$this->Permissions_model->get_by_database($db_id,$user_data['id']);
      $db_mask=$db_pto_umask->pto_umask;
    endif;  
    if($db_pto_umask || $is_network->num_rows() == 0 || ($organization_admin == $this->user_data['id'])) { //if he has permissions, or he is not in network or he is admin of network
    
      if( ($is_network->num_rows() == 0) || ($is_network->num_rows() > 0 && $db_mask > 0) || ($organization_admin == $this->user_data['id']) )  
      { 
        if(!is_null($db_id) AND $database) {
          $this->session->set_userdata('database_id', $db_id);
          redirect('/locations/index/'.$db_id);
        } else {
          redirect('/');      
        }
      } else {
        $this->session->set_flashdata('error', 'You do not have access to this database.');
        redirect('/');
        }
      } else {
        $this->session->set_flashdata('error', 'You do not have access to this database.');
        redirect('/');
      }
  }

  function add(){
    global $user_data;
    $this->load->library('form_validation');
    $this->form_validation->set_rules('name','Database Name', 'required');
    $database_amount=$this->Database_model->count_dbs_from_organization_by_id($user_data['organization_id']);
    $package_id=$this->Organization_model->get_package_id($user_data['organization_id']);
if ($package_id != 9 && $package_id != 10 && $package_id != 1){
if($this->form_validation->run() == FALSE) {
        echo '{ "result": "validation_error", "errors": {"name":"You must enter a database name."}}';
        redirect('/dashboard', 'refresh');
  }
  else{
    $database_id=$this->Database_model->insert();
    $this->Logging_model->database(0,$database_id);
    $this->Permissions_model->set_database_permissions($user_data['organization_id'],$user_data['id'],$database_id,1,1);
      $this->session->set_flashdata('success', 'Your database \''.$this->input->post('name').'\' was successfully created.');
      echo '{ "result": "successful_redirect", "url": "'.site_url().'"}';
      redirect('/dashboard', 'refresh');   
     }
}
else if ($package_id != 9 && $package_id != 10 && $package_id != 1 && $database_amount <= 1){
 if($this->form_validation->run() == FALSE) {
        echo '{ "result": "validation_error", "errors": {"name":"You must enter a database name."}}';

        redirect('/dashboard', 'refresh');
  }
  else{
    $database_id=$this->Database_model->insert();
    $this->Logging_model->database(0,$database_id);
    $this->Permissions_model->set_database_permissions($user_data['organization_id'],$user_data['id'],$database_id,1,1);
      $this->session->set_flashdata('success', 'Your database \''.$this->input->post('name').'\' was successfully created.');
      echo '{ "result": "successful_redirect", "url": "'.site_url().'"}';
      redirect('/dashboard', 'refresh');   
     }

} 
else if ($package_id == 9 && $database_amount < 1 || $package_id == 10 && $database_amount < 1 || $package_id == 1 && $database_amount < 1){
 if($this->form_validation->run() == FALSE) {
        echo '{ "result": "validation_error", "errors": {"name":"You must enter a database name."}}';

        redirect('/dashboard', 'refresh');
  }
  else{
    $database_id=$this->Database_model->insert();
    $this->Logging_model->database(0,$database_id);
    $this->Permissions_model->set_database_permissions($user_data['organization_id'],$user_data['id'],$database_id,1,1);
      $this->session->set_flashdata('success', 'Your database \''.$this->input->post('name').'\' was successfully created.');
      echo '{ "result": "successful_redirect", "url": "'.site_url().'"}';
      redirect('/dashboard', 'refresh');   
     }

}
else {
echo '{ "result": "validation_error", "errors": {"new_capacity":"Upgrade to Paper Tiger Pro to have more than 1 Database."}}';
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
  
  function add_new(){
    if($this->authentication->is_authenticated())
    {
      $this->load->view('database_new_form');
    }
    else
    {
      $this->load->view('session_expired');
    }
  }
  
  function settings(){
  
    global $user_data;
    if(!$this->authentication->is_authenticated())
    {
    $this->load->view('session_expired');
    }
    else
    {
    $data['google_users_data']  = $this->google_users_model->get_token($this->user_data['id']);
    $data['database'] = $this->Database_model->get_by_id($this->session->userdata('database_id'));
    $user = $this->user_model->get_organization_user($user_data['id'], $this->user_data['organization_id']);  
    
    if($user->num_rows() == 0) {
    $this->session->set_flashdata('error', 'This database does not belong to you!');
    $this->load->view('settings/error');
    return;
    }
    else {
     
      $this->load->view('settings/index', $data);
    }
    }
  }
  
  function modify(){
    
  global $user_data;
  $this->authentication->require_authentication();
  if($this->authentication->is_authenticated())
  {
    $this->load->library('form_validation');

    $organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
    $editable=false;
    if($organization_admin == $this->user_data['id']) {
    $editable=true;
    }
    
    if($editable==true && $this->input->post('db_name') == ""){
      echo '{ "result": "validation_error", "errors": {"name":"You must enter a database name."}}';
    return;
    }
    else{
      $id = $this->session->userdata('database_id');
    if($this->input->post('db_name') !="") {
      $db_name = $this->input->post('db_name');
    } else {
      $db_name=$this->Database_model->get_by_id($this->session->userdata('database_id'))->name;
    }
      if($this->input->post('timezone') != ""){
       $db_id = $this->session->userdata('database_id');
       $this->Database_model->update_timezone($this->input->post('timezone'),$db_id);
      } 
    //check if reminders are set, insert otherwise
    $user_setting=$this->Settings_model->get_all();
    
    if($user_setting->num_rows()==0)
    {
      $duration=604800;
      if($this->input->post('reminder_days'))
      {
      $duration=($this->input->post('reminder_days')*86400);
      }
      $confirmation=1;
      if($this->input->post('confirmation') == 0 || $this->input->post('confirmation') == 1)
      {
      $confirmation=$this->input->post('confirmation');
      
      }
      if($this->input->post('remind_me') == 0 || $this->input->post('remind_me') == 1)
      {
      $remind_me=$this->input->post('remind_me');
      }
      if($this->input->post('show_notes') == 0 || $this->input->post('show_notes') == 1)
      {
      $show_notes=$this->input->post('show_notes');
      }
      $this->Settings_model->insert($confirmation,1,$duration,$remind_me,$show_notes,$this->input->post('digital_search'));
    }
    if($user_setting->num_rows() > 0)
    {
      $duration=($this->input->post('reminder_days')*86400);
      $this->Settings_model->update_duration($duration);
      $this->Settings_model->update_confirmations($this->input->post('confirmation'));
      $this->Settings_model->update_reminders($this->input->post('remind_me'));
      $this->Settings_model->update_notes($this->input->post('show_notes'));
      $this->Settings_model->update_digital($this->input->post('digital_search'));
    }
    
      if($db_name != $this->Database_model->get_by_id($this->session->userdata('database_id'))->name) {
      $this->Logging_model->update($db_name, $id);
    }
      $this->Database_model->update($db_name, $id);
      $this->session->set_flashdata('success', 'The database preferences have been updated.');
    $previous_page=$_SERVER['HTTP_REFERER'];
      echo '{ "result": "successful_redirect", "url": "'.$previous_page.'"}';}
  } else  {
    $this->load->view('session_expired');
   }
}
  
  function delete(){
global $user_data;
$this->authentication->require_authentication();
if($this->authentication->is_authenticated())
{
    $data['database'] = $this->Database_model->get_by_id($this->session->userdata('database_id'));
    
    $user = $this->user_model->get_organization_user($user_data['id'], $this->user_data['organization_id']);
    
    if($user->num_rows() == 0) {
    $this->session->set_flashdata('error', 'This database does not belong to you!');
    $this->load->view('settings/error');
    return;
  }
  else{
    $this->load->view('settings/delete', $data);
  }
} else {
    $this->load->view('session_expired');
   }
}
function cancel_import($db_id=null){
global $user_data;
$this->authentication->require_authentication();
if($this->authentication->is_authenticated())
{

    $data['database'] = $this->Database_model->get_by_id($db_id);
    
    $user = $this->user_model->get_organization_user($user_data['id'], $this->user_data['organization_id']);  
    
  if($user->num_rows() == 0) {
    $this->session->set_flashdata('error', 'This database does not belong to you!');
    $this->load->view('settings/error');
    return;
  }
  else {
      $this->load->view('settings/cancel',$data);
    }
} else {
    $this->load->view('session_expired');
   }
}  
  function do_delete(){
  $this->authentication->require_authentication();
    if($this->authentication->is_authenticated())
    {
      global $user_data;
      $id = $this->session->userdata('database_id');

    $this->load->library('form_validation');
  
    if(($user_data['sha1_password']) != sha1($this->input->post('db_delete_password').$this->config->item('encryption_key')))
    {
      echo '{ "result": "validation_error", "errors": {"name":"<p>The password wrong, You must provide a valid password to delete a database.</p>"}}';
    }
    else
    {
    $this->Logging_model->delete($id);
      $this->Database_model->delete($id);
      $this->session->set_flashdata('success', 'The database has been deleted.');
      echo '{ "result": "successful_redirect", "url": "'.site_url().'"}';
    }
  }
  else
  {
      $this->load->view('session_expired');
  }
  }
  function delete_unimported($db_id=null){
$this->authentication->require_authentication();
  if($this->authentication->is_authenticated())
  {
    global $user_data;
    
    $this->Database_model->delete($db_id);
    
    $this->session->set_flashdata('success', 'Import cancelled successfully.');
    echo '{ "result": "successful_redirect", "url": "'.site_url().'"}';
} else {
    $this->load->view('session_expired');
   }
}
  function delete_failed($db_id){

  $this->authentication->require_authentication();
    global $user_data;
    
    $this->Database_model->delete($db_id);
    
    $this->session->set_flashdata('success', 'Failed database import deleted successfully.');
    echo '{ "result": "successful_redirect", "url": "'.site_url().'"}';
  }
  function delete_timedout($db_id){

  $this->authentication->require_authentication();
    global $user_data;
    
    $this->Database_model->delete($db_id);
    
    $this->session->set_flashdata('success', 'Timed out database deleted successfully.');
    echo '{ "result": "successful_redirect", "url": "'.site_url().'"}';
  }
  function delete_import($db_id){

  $this->authentication->require_authentication();
    global $user_data;

    $data['database'] = $this->Database_model->get_by_id($db_id);
    
    $user = $this->user_model->get_organization_user($user_data['id'], $this->user_data['organization_id']);  
    
  if($user->num_rows() == 0) {
    $this->session->set_flashdata('settings_error', 'This database does not belong to you!');
    $this->load->view('settings/error');
    return;
  }
  else {
      $this->load->view('settings/transfers_pending',$data);
    }
  }  
  function delete_import_review($db_id){

  $this->authentication->require_authentication();
    global $user_data;

    $data['database'] = $this->Database_model->get_by_id($db_id);
    
    $user = $this->user_model->get_organization_user($user_data['id'], $this->user_data['organization_id']);  
    
  if($user->num_rows() == 0) {
    $this->session->set_flashdata('settings_error', 'This database does not belong to you!');
    $this->load->view('settings/error');
    return;
  }
  else {
      $this->load->view('settings/frequency_reviews',$data);
    }
  }  
  function delete_timedout_import($db_id){

  $this->authentication->require_authentication();
    global $user_data;

    $data['database'] = $this->Database_model->get_by_id($db_id);
    
    $user = $this->user_model->get_organization_user($user_data['id'], $this->user_data['organization_id']);  
    
  if($user->num_rows() == 0) {
    $this->session->set_flashdata('settings_error', 'This database does not belong to you!');
    $this->load->view('settings/error');
    return;
  }
  else {
      $this->load->view('settings/timedout',$data);
    }
  }  
  function remove_reminder(){

  $this->authentication->require_authentication();
    global $user_data;

  $db_id=$this->session->userdata['database_id'];

    $data['database'] = $this->Database_model->get_by_id($db_id);
    
    $user = $this->user_model->get_organization_user($user_data['id'], $this->user_data['organization_id']);  
    
  if($user->num_rows() == 0) {
    $this->session->set_flashdata('settings_error', 'This database does not belong to you!');
    $this->load->view('settings/error');
    return;
  }
  else {
    $reminder_status=$this->Settings_model->get_all()->num_rows();
    if($reminder_status == 0)
    {
      $this->Settings_model->insert(1,0,604800);  
    }
    if($reminder_status > 0)
    {
        $this->Settings_model->update_session(0);
    }
         }
  } 

  function excel_export(){
    if(!$this->authentication->is_authenticated()){
      $this->load->view('session_expired');
    } else {
      //let's begin
      function GenerateSafeFileName($filename) {
          $filename = str_replace("#","_",$filename);
          $filename = str_replace(" ","_",$filename);
          $filename = str_replace("'","",$filename);
          $filename = str_replace('"',"",$filename);
          $filename = str_replace("__","_",$filename);
          $filename = str_replace("&","and",$filename);
          $filename = str_replace("/","_",$filename);
          $filename = str_replace("\\","_",$filename);
          $filename = str_replace("?","",$filename);
         return $filename;
      }
      function GenerateSafeSheetName($sheetname) {
        //Remove * ; / \ ? ] [
        $sheetname = str_replace("*","_",$sheetname);
        $sheetname = str_replace(";","_",$sheetname);
        $sheetname = str_replace("/","_",$sheetname);
        $sheetname = str_replace("\\","_",$sheetname);
        $sheetname = str_replace("?","_",$sheetname);
        $sheetname = str_replace("[","_",$sheetname);
        $sheetname = str_replace("]","_",$sheetname);
        return $sheetname;
      }
      date_default_timezone_set('America/New_York');
      $this->load->model("location_model");
      $this->load->model("item_model");
      $this->load->model("category_model");
      $this->load->model("transfers_model");
      $this->load->model("trash_model");
      
      $databasename = $this->Database_model->get_by_id($this->session->userdata('database_id'))->name;
      $objPHPExcel = new PHPExcel();
      $time_now=date('m/d/Y g:i:s A',now());
      //write meta data
      $objPHPExcel->getProperties()->setCreator($this->user_data['first_name']." ".$this->user_data['first_name']);
      $objPHPExcel->getProperties()->setLastModifiedBy($this->user_data['first_name']." ".$this->user_data['first_name']);
      $objPHPExcel->getProperties()->setTitle($databasename." exported on ".$time_now." from Paper Tiger Online");
      $objPHPExcel->getProperties()->setSubject($databasename." exported on ".$time_now." from Paper Tiger Online");
      $objPHPExcel->getProperties()->setDescription($databasename." exported on ".$time_now." from Paper Tiger Online");
      //write the data
      $export_locations=$this->Location_model->get_by_database($this->session->userdata('database_id'));
      $current_sheet=0;
      foreach($export_locations->result_array() as $loc_result) {
        $objPHPExcel->setActiveSheetIndex($current_sheet);
        $sheet_title=GenerateSafeSheetName($loc_result['name']);
        $objPHPExcel->getActiveSheet()->setTitle($sheet_title);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Location');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Item');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Keywords');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Notes');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Category');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Action Date');
            $export_items=$this->Item_model->get_for_excel_export($loc_result['id']);
              $current_item_index=2;
              foreach($export_items->result_array() as $item_result) {
                $category_name=$this->Category_model->get_by_id($item_result['category_id'])->name;
                if($item_result['action_date'] != null || $item_result['action_date'] != 0) {
                  $action_date=date('m/d/Y',$item_result['action_date']);
                } else {
                  $action_date='';
                }
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$current_item_index, utf8_encode($sheet_title));
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$current_item_index, utf8_encode($item_result['slot_num']));
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$current_item_index, utf8_encode($item_result['name']));

                //$objPHPExcel->getActiveSheet()->SetCellValue('D'.$current_item_index, utf8_encode($item_result['keywords']));
                // Treat values as strings instead of formulae or disable calculation engine $objWriter->setPreCalculateFormulas(false); 
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$current_item_index, utf8_encode($item_result['keywords']));

                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$current_item_index, utf8_encode($item_result['description']));
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$current_item_index, utf8_encode($category_name));
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$current_item_index, utf8_encode($action_date));
              $current_item_index++;
              }
        $objPHPExcel->createSheet();
        $current_sheet++;
      }
      $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
      //$objWriter->save("/tmp/file.xlsx");
      $excel_file_name=GenerateSafeFileName($databasename); 
      $excel_file_name=$excel_file_name.".xlsx";
      header('Content-type: application/ms-excel');
      header('Content-Disposition: attachment; filename='.$excel_file_name);
      $objWriter->save('php://output');
    }
  }
  
  function export(){
    if(!$this->authentication->is_authenticated()){
      $this->load->view('session_expired');
    }
    else{
    
      $this->load->model("location_model");
      $this->load->model("item_model");
      $this->load->model("category_model");
      $this->load->model("transfers_model");
      $this->load->model("trash_model");
      
      $databasename = $this->Database_model->get_by_id($this->session->userdata('database_id'))->name;
      
      header("Content-type: application/xml");
      header("Content-disposition: attachment;filename=".str_replace(" ","_", $databasename)."_".date("m_d_Y", time()).".xml");
      
      $export = "<?xml version=\"1.0\"?>";
      $export .= "\n<database name=\"".htmlspecialchars($databasename)."\">\n";
      
      $locations = $this->location_model->get_from_database()->result();
      
      foreach($locations as $location):
        
        $items = $this->item_model->get_from_location($location->id);
        
        $export .= "\t<location name=\"".$location->name."\" date_added=\"".$location->date_added."\" next_review=\"".$location->next_review."\">";
          $export .= "\n\t\t<location_info>";
            $export .= "\n\t\t\t<capacity>".htmlspecialchars($location->capacity)."</capacity>";
            $export .= "\n\t\t\t<num_items>".htmlspecialchars($location->num_items)."</num_items>";
          $export .= "\n\t\t</location_info>";
          $export .= "\n\t\t<items>";
            foreach($items->result() as $item):
              $export .= "\n\t\t\t<item id=\"".$item->id."\" date_added=\"".$item->date_added."\">";
                $export .= "\n\t\t\t\t<name>".htmlspecialchars($item->name)."</name>";
                $export .= "\n\t\t\t\t<slot_num>".htmlspecialchars($item->slot_num)."</slot_num>";
                $export .= "\n\t\t\t\t<keywords>".htmlspecialchars($item->keywords)."</keywords>";
                $export .= "\n\t\t\t\t<description>".htmlspecialchars($item->description)."</description>";
                $export .= "\n\t\t\t\t<action_date>".htmlspecialchars($item->action_date)."</action_date>";
                if($item->category_id)
                  $export .= "\n\t\t\t\t<category>".htmlspecialchars($this->category_model->get_by_id($item->category_id)->name)."</category>";
                $export .= "\n\t\t\t\t<status>";
                if(!$item->transfer_status_id)
                  $export .= "1";
                else
                  $export .= "2";
                $export .= "</status>";
                if($item->transfer_status_id != 0 || $item->transfer_status_id != 1):
                  $transfer = $this->transfers_model->get_by_id($item->transfer_status_id);
                  if($transfer):
                  $export .= "\n\t\t\t\t<pending_transfer id=\"".$transfer->id."\">";
                    $export .= "\n\t\t\t\t\t<original_dest>".htmlspecialchars($this->location_model->get_by_id($transfer->origin_location_id)->name)."</original_dest>";
                    $export .= "\n\t\t\t\t\t<new_dest>";
                    
                    if($transfer->destination_location_id > 0):
                      $export .= htmlspecialchars($this->location_model->get_by_id($transfer->destination_location_id)->name);
                    endif; 
                    
                    $export .= "</new_dest>";
                    $export .= "\n\t\t\t\t\t<type>".htmlspecialchars($transfer->type)."</type>";
                    $export .= "\n\t\t\t\t\t<new_name>".htmlspecialchars($transfer->new_name)."</new_name>";
                    $export .= "\n\t\t\t\t\t<new_keywords>".htmlspecialchars($transfer->new_keywords)."</new_keywords>";
                    $export .= "\n\t\t\t\t\t<new_description>".htmlspecialchars($transfer->new_description)."</new_description>";
                    $export .= "\n\t\t\t\t\t<new_category_id>".htmlspecialchars($transfer->new_category_id)."</new_category_id>";
                    $export .= "\n\t\t\t\t\t<new_action_date>".htmlspecialchars($transfer->new_action_date)."</new_action_date>";
                    $export .= "\n\t\t\t\t\t<merge_into merge_id=\"".$transfer->merge_into."\">";
                    if($transfer->merge_into > 0):
                    
                      $export .= $this->transfers_model->get_by_id($transfer->merge_into)->new_name;
                    endif;
                    
                    $export .= "</merge_into>";
                    $export .= "\n\t\t\t\t\t<transfer_date>".htmlspecialchars($transfer->transfer_date)."</transfer_date>";
                  $export .= "\n\t\t\t\t</pending_transfer>";
                  endif; 
                endif;
              $export .= "\n\t\t\t</item>";
            endforeach;
          $export .="\n\t\t</items>";
        $export .= "\n\t</location>\n";
      endforeach;
      
      $categories = $this->category_model->get_from_database();
      
      $export .= "\n\t<categories>";
      foreach($categories->result() as $category):
        $export .= "\n\t\t<category name=\"".htmlspecialchars($category->name)."\">";
          $export .= "\n\t\t\t<category_description>".htmlspecialchars($category->description)."</category_description>";
        $export .= "\n\t\t</category>";
      endforeach;
      $export .= "\n\t</categories>";
      
      $trashItems = $this->trash_model->get_from_database()->result();
      
      $export .= "\n\t<trash>";
        foreach($trashItems as $trash):
          $export .= "\n\t\t<trash_item>";
            $export .= "\n\t\t\t<name>".htmlspecialchars($trash->name)."</name>";
            $export .= "\n\t\t\t<keywords>".htmlspecialchars($trash->keywords)."</keywords>";
            $export .= "\n\t\t\t<category>";
            if($trash->category_id)
            $export .= htmlspecialchars($this->category_model->get_by_id($trash->category_id)->name);
            $export .= "</category>";
            $export .= "\n\t\t\t<previous_location_id>";
            if($trash->previous_location_id > 0):
            $export .= htmlspecialchars($this->location_model->get_by_id($trash->previous_location_id)->name);
            endif;
            $export .= "</previous_location_id>";
            $export .= "\n\t\t\t<description>".htmlspecialchars($trash->description)."</description>";
          $export .= "\n\t\t</trash_item>";
        endforeach;
      $export .= "\n\t</trash>";
      $export .= "\n</database>\n";
      
      echo $export;
    }
  }
}
?>
