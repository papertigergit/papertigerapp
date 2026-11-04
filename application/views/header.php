<?php
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-type: text/html; charset=UTF-8') ;

$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
$is_network = $this->Network_model->is_network($package_id);
$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id']);
$get_database=$this->Database_model->get_by_id($this->session->userdata('database_id'));
$google_users_data=$this->google_users_model->get_token($this->user_data['id']);
$digital_status=$this->google_users_model->digital_status($this->user_data['id']);
?>

<?php
//pages for which app buttons don't need to be shown. this should be the first segment of the uri (/labels/, /imports/, etc.)
$no_header_pages = array(
	"labels",
	"imports",
	"people",
	"account",
	"dashboard",
	"adminscripts",
	"status",
	"permissions",
	"reports",
	"change_plan",
	"messages",
	"payments",
	"affiliates",
	"admin"
	);

//pages for which we only want to show the dashboard actions
$dashboard_level = array(
	"dashboard",
	"messeges",
	"imports",
	"people",
	"permissions",
	"account",
	"change_plan",
	"payments",
	"status",
	"affiliates",
	"admin"
	);

$uri = $this->uri->segment(1, "dashboard");

//Check to see if current page belongs in dashboard group
function if_in_dashboard($uri, $dashboard_level) {
	
	if ((in_array($uri, $dashboard_level))) {
		return TRUE;
	}
	
	else {
		return FALSE;
	}

}
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Paper Tiger - The easiest way to manage your documents!</title>
		
		<noscript>
			<meta HTTP-EQUIV="REFRESH" content="0; url=<?=site_url()?>noscript.php"> 
		</noscript>		
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<meta http-equiv="Cache-Control" content="no-store" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Cache-Control" content="must-revalidate" />
		<meta http-equiv="Cache-Control" content="max-age=0" />
		<meta http-equiv="content-script-type" content="text/javascript">
		<link rel="icon" type="image/png" href="<?=site_url()?>img/favicon.png" />
		
		<link href="<?=site_url()?>resources/g=css" media="screen" rel="stylesheet" type="text/css"/>
		<?php 
		if($this->uri->segment(1) == "account"){ ?>
		<link href="<?=site_url()?>css/account.css" media="screen" rel="stylesheet" type="text/css"/>
		<?php }?>
		<link href="<?=site_url()?>css/print_css.css" media="print" rel="stylesheet" type="text/css"/>
		<link href="<?=site_url()?>css/tristate.css" media="screen" rel="stylesheet" type="text/css"/>

		<script type="text/javascript">var num_items = 0;<?=isset($this_location)?'var location_id ='.$this_location->id."; num_items =".($this_location->capacity - $this_location->empty_slots)."; ":""?>var site_url = '<?=site_url()?>';</script>
   		<script type="text/javascript" src="<?=site_url()?>js/master.js"></script>   		
                <script type="text/javascript" src="<?=site_url()?>js/jquery.tristate.js"></script>
	</head>

    <body id="<?=isset($body_id)?$body_id:""?>">


<?php //Show advanced search open if active ########################################################################################### ?>

    <?php
        if(($this->input->post('category')) || ($this->input->post('location')) || ($this->input->post('date_range'))):
        $grid_data = $this->Search_model->advanced_search($this->session->userdata('search_string'));
        $location_result_data = $this->Search_model->advanced_search_locs_cats($this->session->userdata('search_string'));
    ?>

        <script>
            $(document).ready(function() {
                $('#adv_search_toggle').click();
            }); 
        </script>

    <?php endif;?>



<?php //Display flashdata if necessary-------------------------------------------------------------------------------------------?>

<?php if($this->session->flashdata('success')): ?>
	<div id="flash" class="flash_success"><p><?php echo $this->session->flashdata('success'); ?></p><a href="#" id="flash_hide" class="small" style="float:right; margin-top:-10px; margin-right:10px;">Hide this</a></div>
<?php elseif($this->session->flashdata('error')): ?>
	<div id="flash" class="flash_error"><p><?php echo $this->session->flashdata('error'); ?></p><a href="#" id="flash_hide" class="small" style="float:right; margin-top:-10px; margin-right:10px;">Hide this</a></div>		
<?php endif; ?>


<?php //Display reminders notification if necessary-------------------------------------------------------------------------------------------?>

<?php 
$reminder_status=$this->Settings_model->get_all();
	if($reminder_status->num_rows() == 0)
		{   
			$reminder_days=86400;
			$remind_me=1;
		}   
		if($reminder_status->num_rows() > 0)
		{    
			foreach($reminder_status->result() as $row)
				{   
					$reminder_days=$row->reminder_duration;
					$remind_me=$row->reminders_enabled;
				}   
		}
		
	$total_reminders = $this->Reminders_model->get_network_all($reminder_days)->num_rows(); 
	
	if($total_reminders > 0)
	{
		$reminder_status=$this->Settings_model->get_all()->num_rows();
		if($reminder_status > 0)
		{
			$reminder_on=$this->Settings_model->get_all()->result();
			foreach($reminder_on as $reminders)
			{
				$this_reminder=$reminders->session_reminder;
				$session_time=$reminders->session_time;
				$reminder_time=$session_time+64800; //18 hours!
			}
		if($this_reminder==1 && $remind_me==1)
		{
			if($this->uri->segment(1) == "locations" || $this->uri->segment(1) == "categories" || $this->uri->segment(1) == "confirmations" || $this->uri->segment(1) == "reminders" || $this->uri->segment(1) == "trash") {
		?>

	<div id="flash" class="flash_warning"><p>You have <?=$total_reminders > 1?$total_reminders:"one"?> pending Action <?=$total_reminders > 1?"Items": "Item"?> today! <a href="<?=site_url()?>reminders">View reminders.</a></p> <a href="#" id="flash_hide_action_date" class="small" style="float:right; margin-top:-10px; margin-right:10px;">Hide this</a></div>
	<?php
		    }
		}
		
		$time_now = time();
		
		if($this_reminder==0 && $time_now > $reminder_time && $remind_me==1)
			{
			if($this->uri->segment(1) == "locations" || $this->uri->segment(1) == "categories" || $this->uri->segment(1) == "confirmations" || $this->uri->segment(1) == "reminders" || $this->uri->segment(1) == "trash") {
				?>
	<div id="flash" class="flash_warning"><p>You have <?=$total_reminders > 1?$total_reminders:"one"?> pending Action <?=$total_reminders > 1?"Items": "Item"?> today! <a href="<?=site_url()?>reminders">View reminders.</a></p> <a href="#" id="flash_hide_action_date" class="small" style="float:right; margin-top:-10px; margin-right:10px;">Hide this</a></div>
				<?php
				}
			}
		}
		
		if($reminder_status == 0 && $remind_me == 1)
		{
			if($this->uri->segment(1) == "locations" || $this->uri->segment(1) == "categories" || $this->uri->segment(1) == "confirmations" || $this->uri->segment(1) == "reminders" || $this->uri->segment(1) == "trash") {
			?>
	<div id="flash" class="flash_warning"><p>You have <?=$total_reminders > 1?$total_reminders:"one"?> pending Action <?=$total_reminders > 1?"Items": "Item"?> today! <a href="<?=site_url()?>reminders">View reminders.</a></p> <a href="#" id="flash_hide_action_date" class="small" style="float:right; margin-top:-10px; margin-right:10px;">Hide this</a></div>
			<?php

			}
		}
	}
?>

<?php //Display cancelled message if necessary-------------------------------------------------------------------------------------------?>

<?php if( $this->suspended_message !== FALSE): ?>

	 <p class="error"><?php echo $this->suspended_message; ?></p>

<?php endif; ?>

<?php //Begin Content-------------------------------------------------------------------------------------------?>

			<div id="header">
				<div id="header_bar">
				
				<span id="tr_nav">
					
					<?php //Show Upgrade link if you have a FREE account ###############################################################################################################?>
					<?php if ($this->Organization_model->get_package_id($this->user_data['organization_id']) == 1): ?>
					<a id="free_link" style="margin-right:7px; color:#8def8c;" href="<?=site_url()?>account">Free Account - Upgrade Now!</a>
					<?php endif;?>

					<?= $this->user_data['first_name']." ".$this->user_data['last_name']; ?> | 
					<?php
					if($is_network->num_rows() == 0): 
						$user_edit_url="people/me";
					endif;	
					if($is_network->num_rows() > 0): 
						$user_edit_url="people/me";
					endif;	
					if($is_network->num_rows() > 0 && $organization_admin == $this->user_data['id']): 
						$user_edit_url="people/edit/".$this->user_data['id'];
					endif;	
					?>
					<a href="<?=site_url()?><?=$user_edit_url?>">My Info</a> | 

										<?php if($this->user_data['affiliate_code']):?><a href="<?=site_url()?>affiliates">Affiliates Program</a> | <?php endif; ?> 
										<?php if(($this->user_data['id'] == 3) || ($this->user_data['id'] == 1) || ($this->user_data['id'] == 105) || ($this->user_data['id'] == 9132) || ($this->user_data['id'] == 5894) || ($this->user_data['id'] == 249489) || ($this->user_data['id'] == 6) || ($this->user_data['id'] == 1862) || ($this->user_data['id'] == 248436)): ?>
										<a href="<?= site_url(); ?>messeges/show">Messeges</a> | 
										<a href="<?= site_url(); ?>change_plan/plans">Change Plan</a> | 
										<a href="<?= site_url(); ?>imports/assign_owner">Client Database Imports</a> | 
										<a href="<?= site_url(); ?>payments">Payment Log</a> | 
										<a href="<?= site_url(); ?>status">App Status</a> | 
					<?php endif; ?>					
					<a href="<?= site_url()."login/logout" ?>">Sign Out</a>
					<a target="_blank" href="http://www.thepapertiger.com/support/"><img src="<?= site_url() ?>img/help_button.gif" alt="Help" height="25" width="48"></a>
				</span>			
				
				
				<?php //Show Dashboard link if not in dashboard pages ###############################################################################################################?>
				<?php if (if_in_dashboard($uri, $dashboard_level) == FALSE): ?>
					<div id="dashboard_link">
							<a href="<?php echo site_url(); ?>">Back to Dashboard</a>
					</div>
				<?php endif; ?>
				
				
				<?php //Show Org Name if not in dashboard pages ###############################################################################################################?>
					<span id="db_name">						
						<?php if (if_in_dashboard($uri, $dashboard_level) == TRUE): ?>
							<?php print htmlspecialchars($this->Organization_model->get_organization($this->user_data['organization_id'])->row()->name); ?>
						<?php else: ?>
							<?php  
							$db_name=$this->Database_model->get_by_id($this->session->userdata('database_id'));
							if($db_name):
								print htmlspecialchars($db_name->name);
							endif;	
							?> <span class="organization_name"><?php print htmlspecialchars($this->Organization_model->get_organization($this->user_data['organization_id'])->row()->name); ?></span>
						<?php endif; ?>
					</span>
					
				</div>
				<div id="tab_bar">
					
					
<?php if (if_in_dashboard($uri, $dashboard_level) == TRUE): //Left Navigation for Dashboard level ################################################################?>
<ul id="left_nav"> 
  <li id="browse_tab"><a href="<?= site_url(); ?>">Dashboard</a></li>
<?php 
	$dashboard_uri = "dashboard";
	// no first segment = the dashboard (default controller); PHP 7 only matched it because 0 == "dashboard"
	$uri_segment = $this->uri->segment(1,$dashboard_uri);
?>
<?php if($packages->num_rows() == 0): ?>
<?php
if($uri_segment == $dashboard_uri) { ?>
  <li id="imports_tab"><a href="<?=site_url()?>imports/upgrade" rel="facebox">Database Imports</a></li>
<?php } ?>
<?php endif; ?>
<?php $user_package=(array($packages->result())); ?>

<?php if($user_package[0][0]->allow_db_import == 0): ?>

<?php if($uri_segment == $dashboard_uri) { ?>
  <li id="imports_tab"><a href="<?=site_url()?>imports/upgrade" rel="facebox">Database Imports</a></li>
<?php } ?>
<?php endif; ?>
<?php if($user_package[0][0]->allow_db_import == 1): ?>
<?php if($is_network->num_rows() == 1 && $organization_admin->admin_user == $this->user_data['id']): ?> 
<?php if($uri_segment == $dashboard_uri) { ?>
  <li id="imports_tab"><a href="<?=site_url()?>imports/new_database">Database Imports</a></li>
<?php } ?>
<?php endif; ?>
<?php endif; ?>

<?php 
$org_expert = $this->Organization_model->get_org_expert($this->user_data['organization_id']); 
$user_orgs = $this->Organization_model->get_org_expert($this->user_data['organization_id']);
$e = $this->Organization_model->get_this_org_expert($org_expert);
    $user_expert = $this->Organization_model->assign_expert($this->user_data['organization_id']);
foreach ($e->result() as $d){
if (/*$user_orgs == NULL && */$uri_segment == $dashboard_uri && $d->affiliate_level != 2 && $d->affiliate_level != 3 && $organization_admin->admin_user == $this->user_data['id']): ?>
  <li id="experts_tab">
    <a href="<?= site_url(); ?>affiliates/choose_expert_page/">Click Here to Choose an Expert!</a>
  </li>
<?php
endif;
?>
<?php

 if ($user_orgs != NULL && $user_orgs != '0'): ?>
<?php if ($d->affiliate_level == 3  && $uri_segment == $dashboard_uri){ ?>
  <li id="experts_tab">
    <a href="#anchortext">View Your Expert!</a>
  </li>
<?php } endif;} ?>
</ul>   

					<?php else: //Left Navigation for Database level ################################################################?>
						<ul id="left_nav">
							<li id="browse_tab"><a href="<?= site_url(); ?>locations/index/<?= $this->session->userdata('database_id'); ?>">Browse</a></li>
							<li id="reports_tab"><a href="/reports/">Reports</a></li>
							<li id="labels_tab"><a href="/labels/">Print Tabs/Labels</a></li>
						</ul>
					<?php endif;?>
					
					<?php
						$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
					?>
					
					
					<?php if (if_in_dashboard($uri, $dashboard_level) == TRUE): //Right Navigation for Dashboard level ################################################################?>
						<ul id="right_nav">			

						<?php if($is_network->num_rows() > 0): ?>
							<?php 
								if($is_network=$this->Package_model->check_network($package_id)->num_rows() != 0) {	
									$admin_user=$this->Organization_model->get_org_by_id($this->user_data['organization_id']);
									$org_data=$admin_user->result_array();
									$org_data_arr=$org_data[0];
								}
							?>
							<?php if($org_data_arr['admin_user'] == $this->user_data['id']): ?>

								<li id="permissions_tab"><a href="<?= site_url(); ?>people">People/Permissions</a></li>
								<li id="account_tab"><a href="<?= site_url(); ?>account">Account (upgrades/invoices)</a></li>
							<?php endif; ?>
						<?php endif; ?>	
						<?php $is_network = $this->Network_model->is_network($package_id); ?>
						<?php if($is_network->num_rows() == 0): ?>
								<li id="account_tab"><a href="<?= site_url(); ?>account">Account (upgrades/invoices)</a></li>
						<?php endif; ?>		
						</ul>
					
					<?php else: //right Navigation for Database level ################################################################?>
						<ul id="right_nav">
							<li id="settings_tab"><a href="/databases/settings/" rel="facebox">Database Preferences</a></li>
						</ul>
					<?php endif; ?>
					
				</div>
				<div class="clear"></div>
			</div>
					
			<?php //second parameter tells variable to become "dashboard" if segment does not exist
			
			if (!(in_array($this->uri->segment(1, "dashboard"), $no_header_pages))) { ?>
				
			<div id="utilities">
			<?php
			      $packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($this->user_data['organization_id']));
			      $user_package=(array($packages->result()));
			      if(($packages->num_rows() == 0)  || ($user_package[0][0]->advanced_search == 0)) {
              if($this_digital=1 || $user_package[0][0]->max_items = 100) {
				      ?>
					<form id="search_form" method="POST" action="<?=site_url()?>search/">
							<a href="<?= site_url() ?>search/upgrade" class="small gray" style="float:left" rel="facebox">Show advanced search</a><br />
							<?php $search_string=''; if($this->input->post('search_string')) { $search_string=$this->input->post('search_string'); } ?>	
							<input type="text" name="search_string" id="search_string" value="<?= htmlspecialchars($search_string); ?>"/>
							<input type="image" src="<?= site_url() ?>img/search_button.png" id="search_button" value="Search" />
							<div class="clear"></div>
					</form>
					<?php
            }
			      }
			      if($user_package[0][0]->advanced_search == 1 || $user_package[0][0]->groups == 4) {
			?>



<?php
$user_setting=$this->Settings_model->get_all();
if($user_setting->num_rows()==0)
{
  $this_reminder=7;
  $this_confirmation=1;
  $this_digital=0;
  $remind_me=1;
  $show_notes=0;
}
if($user_setting->num_rows() > 0)
{
  foreach($user_setting->result() as $row)
  {

   $this_digital=$row->digital_search;
  }
}
?>


<div id="search_toggle">

<?php
if($this_digital == 1 && $digital_status->num_rows() == 1){
?>

<ul  id="one">
  <div class="handle">|||</div>
  <li>OFF</li>
  <li>ON</li>
</ul>

<ul id="two">
  <div class="handle">|||</div>
  <li>OFF</li> 
  <li>ON</li>
</ul>

<?php
}else{
echo "";
}
?>

</div>
<?php
if($this_digital == 1 && $digital_status->num_rows() == 1){


?>

  <span id="toggle_labels"><span id="paper_label">Paper Search</span> <span id="digital_label">Digital search</span></span>
<?php
}else{
echo "";
}
?>
					<form id="search_form" method="POST" action="<?= site_url(); ?>search/">
<?php if($user_package[0][0]->advanced_search == 0) {  ?>
							<a href="<?= site_url(); ?>search/upgrade" class="small gray" style="float:left" rel="facebox">Show advanced search</a><br />
<?php }else{ ?>
							<a href="#" class="small gray" id="adv_search_toggle">Show advanced search</a><br />
<?php } ?>
							<?php $search_string=''; if($this->input->post('search_string')) { $search_string=$this->input->post('search_string'); } ?>	
							<input type="text" name="search_string" id="search_string" value="<?= htmlspecialchars($search_string); ?>"/>
							<input type="image" src="<?= site_url(); ?>img/search_button.png" id="search_button" value="Search" />
													

							<div class="clear"></div>
						
						<div id="adv_search" style="display:none">
							
							<script>
							
							$(document).ready(function() {
							   //Date Range Picker for advanced search
								$('#daterangepicker').daterangepicker(); 
							});
							
							</script>
							
							<div class="field_container">
							<label for="daterangepicker">Action Date:</label>
							<?php
							$date_range_value='';
							if($this->input->post('date_range')){
								$date_range_value=$this->input->post('date_range');
							}
							?>

							<input id="daterangepicker" type="text" value="<?=$date_range_value?>"  name="date_range"/>			
							</div>						
							
							<div class="field_container">						
								<label for="location_dropdown">In location:</label>
								<?php

								$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
								$is_network=$this->Network_model->is_network($package_id);
								$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

								if($is_network->num_rows() == 0):
									$loc_data = $this->Location_model->get_from_database()->result_array();
									$num_locs = $this->Location_model->get_from_database()->num_rows();
								endif;
								if($is_network->num_rows() > 0):
									$loc_data = $this->Location_model->get_from_network_database($this->session->userdata('database_id'))->result_array();
									$num_locs = $this->Location_model->get_from_network_database($this->session->userdata('database_id'))->num_rows();
								endif;
								if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
									$loc_data = $this->Location_model->get_from_database()->result_array();
									$num_locs = $this->Location_model->get_from_database()->num_rows();
								endif;


								if($num_locs == 0){
									?>
									<select name="location[]" id="no_location" multiple="multiple">
									</select>
									<?php
								}
								else if($num_locs > 0){
								?>
								<select name="location[]" id="location_dropdown" multiple="multiple">
								<?php		
									foreach($loc_data as $loc) {
										$is_selected='';
										if($this->input->post('location')){
										foreach($this->input->post('location') as $selected_location)
										{
											if($selected_location==$loc['id'])
											{
												$is_selected="selected=\"selected\"";
												echo "";
											}
										}}	
											echo "<option $is_selected value=\"$loc[id]\">".htmlspecialchars($loc['name'])."</option>";
										}
								?>
								</select>
								<?php } ?>
							</div>
							
							<div class="field_container">
								<label for="category_dropdown">In category:</label>
								<?php
								$num_cats=$this->Category_model->get_from_database()->num_rows();
								if($num_cats == 0){
									?>
									<select name="category[]" id="no_category" multiple="multiple">
									</select>
									<?php 
								}
								else if($num_cats > 0){
								?>
								<select name="category[]" id="category_dropdown" multiple="multiple">
								<?php
									$category_data = $this->Category_model->get_from_database()->result_array();
									foreach($category_data as $category) {
										$is_selected='';
										if($this->input->post('category')){
										foreach($this->input->post('category') as $selected_category)
										{
											if($selected_category==$category['id'])
											{
												$is_selected="selected=\"selected\"";
											}
										}}	
										echo "<option $is_selected value=\"$category[id]\">".htmlspecialchars($category['name'])."</option>";
									}	
								?>
								</select>
								<?php } ?>
							</div>
							<div class="field_container">
							<?php
								if($google_users_data->num_rows() > 0) {
									$google_data=$google_users_data->result_array();
									if($google_data[0]['status']==1) {
										$user_setting=$this->Settings_model->get_all();
										if($user_setting->num_rows()==0)
										{
											$this_digital=0;
											
										}
										if($user_setting->num_rows() > 0)
										{
											foreach($user_setting->result() as $row)
											{
												$this_digital=$row->digital_search;
											}
										}
										?>
											Search Digital Tiger<br/>
								<input  type="checkbox" name="digital_files" <?= $this_digital ==1 ? "checked" : ''; ?>/>
										<?php
									}
								}
							?>
							</div>

							
							<input type="image" src="<?= site_url(); ?>img/search_button.png" id="adv_search_button" value="Advanced search" />
							
												
							<div class="clear"></div>
							
						</div>
					</form>
					
					<?php } ?>
				<ul>
					<li><a href="<?= site_url(); ?>locations/index/<?= $this->session->userdata('database_id') ?>" alt="Locations" class="sprite utilities_button" id="location"></a></li>
					<li><a href="<?= site_url(); ?>categories/index/<?= $this->session->userdata('database_id') ?>" alt="Categories" class="sprite utilities_button" id="categories"></a></li>
					<li><a href="<?= site_url(); ?>confirmations/index/<?= $this->session->userdata('database_id') ?>" alt="Confirmations" class="sprite utilities_button" id="confirmations"></a></li>
					<li><a href="<?= site_url(); ?>reminders/index/<?= $this->session->userdata('database_id') ?>" alt="Reminders" class="sprite utilities_button" id="reminders"></a></li>
					<li><a href="<?= site_url(); ?>trash/index/<?= $this->session->userdata('database_id') ?>" alt="Trash" class="sprite utilities_button" id="trash"></a></li>
				</ul>

				<div class="clear"></div>

			</div>

		<!--</div>  #app_header -->

		
		<?php } ?>

		<div id="content">	 <!-- style="padding-top:243px; -->	
