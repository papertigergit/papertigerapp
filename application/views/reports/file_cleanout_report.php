<?php
      $packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']));
      $package=(array($packages->result()));
      if(($packages->num_rows() == 0)  || ($package[0][0]->allow_reports == 0)) {
          $this->session->set_flashdata('error',"Upgrade to Paper Tiger Pro!");
          redirect('reports');
      }
?>
<!DOCTYPE html>
<?php
$num_rows=1;
if($this->input->post('item_list_keywords')=="on"){
	$num_rows++;
}
if($this->input->post('item_list_notes')=="on"){
	$num_rows++;
}
if($this->input->post('item_list_category')=="on"){
	$num_rows++;
}
if($this->input->post('item_list_action_date')){
	$num_rows++;
}
global $user_data;
?>
<html lang="en-US">
	<head>
		<title>Paper Tiger Online - Cleanout Report</title>
		
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
		<link href="<?=site_url()?>css/reset.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="<?=site_url()?>/css/print_css.css" media="print" rel="stylesheet" type="text/css"/>
		<link href="<?=site_url()?>/css/print_css_screen.css" media="screen" rel="stylesheet" type="text/css"/>		
		 <script type="text/javascript" src="<?=site_url()?>resources/g=js"></script> 
	</head>
	<body id="browse">
		<div id="print_header">
		
			<p>If you want to print it in Landscape mode, just change your print settings from your browser's Print window.</p> 
			
			<input type="submit" name="submit" id="submit_transfer" value="Print" onclick="window.print(); return false;"/>
		
		</div>
		
		
	<div id="print_content">

		<div id="header">
			<div id="db_name">
					<?php if($this->session->userdata('database_id') && $this->uri->total_segments() > 0){?>
						<?php  print htmlspecialchars($this->Database_model->get_by_id($this->session->userdata('database_id'))->name)?> <div class="organization_name"><?php print htmlspecialchars($this->Organization_model->get_organization($user_data['organization_id'])->row()->name)?></div>
					<?php }else{?>
						<?php print htmlspecialchars($this->Organization_model->get_organization($user_data['organization_id'])->row()->name) ?>
					<?php } ?>
					
					<div id="reports_date"><?=$user_data['first_name']." ".$user_data['last_name']?> &mdash; <?php echo date("m/d/Y");?></div>
					
			</div>
		</div>
		
		<div id="table_header_bar">
			<div id="header_bar_left">Cleanout Report</div>
			<div id="header_bar_right">
				<a  class="sprite confirm confirm_button_disabled" alt="Confirm" ></a>
				<a  class="sprite cancel cancel_button_disabled" alt="Cancel" ></a>
			</div>
			<div class="clear"></div>
		</div>		
<?php
if($this->session->userdata('sort_by')=="location_id"){
	$sort_id=0;
} else if($this->session->userdata('sort_by')=="name"){
	$sort_id=1;
} else if($this->session->userdata('sort_by')=="date_added"){
	$sort_id=5;
}
else
{
	$sort_id=0;
}
?>

<?php $results_found = false;?>
<?php $row = $grid_data->first_row()?>
<?php foreach ($file_list_data->result() as $result_table) { ?>
<?php $count=1?>
		<div class="table_header_bar_2">
			<?=$this->Location_model->get_by_id($result_table->location_id)->name?> - <?=$result_table->count?> Items 
		</div>
		<table cellspacing="0" id="file_cleanout_report" class="grid reports_table">
			<thead>
				<tr class="th_row">
					<th class="header_itemNumber">Item</th>
					<th class="header_itemName">Item Name</th>
					<th class="transferReport_checkbox">Review</th>
					<th class="transferReport_checkbox">Toss</th>
					<th class="transferReport_date_completed">Transfer To</th>
					<th class="transferReport_checkbox">None</th>
					<th class="header_itemKeywords">Keywords</th>
					<th class="header_itemActionDate">Action Date</th>
				</tr>
			</thead>
			<tbody>
	<?php foreach($grid_data->result() as $sorted_items) { ?>
	<?php if($sorted_items->location_id == $result_table->location_id):  ?>
		<?php $results_found = true;?>
				<tr>
					<td class="first_column">
					<?=$sorted_items->slot_num?>
					</td>
					<td class="itemName"><?=htmlspecialchars($sorted_items->name) ?></td>
					<td class="transferReport_checkbox"><div id="report_checkbox"></div></td>
					<td class="transferReport_checkbox"><div id="report_checkbox"></div></td>
					<td class="transferReport_date_completed"><div id="report_date_completed"></div></td>
					<td class="transferReport_checkbox"><div id="report_checkbox"></div></td>
					<td><?=htmlspecialchars($sorted_items->keywords) ?></td>					
					<td class="itemActionDate"><?=$sorted_items->action_date==0?'&nbsp;':date("m/d/Y",$sorted_items->action_date)?></td>
					<?php 
					$transfer = '';
					if(!empty($sorted_items->transfer_status_id) && $sorted_items->type =='delete' && $sorted_items->destination_location_id==="0"){
						$transfer = "Pending Deletion";
					} else if(!empty($sorted_items->transfer_status_id) && $sorted_items->type =='merge'){
						if($sorted_items->merge_into == 0){
							$transfer = 'Merge Destination';
						} else {
							$transfer = 'Merge Source';
						}
					} else if(!empty($sorted_items->transfer_status_id) && $sorted_items->destination_location_id===NULL) {
						$transfer = "Transfer IN Pending";
					} else if(!empty($sorted_items->transfer_status_id)) {
						if($sorted_items->type == 'move') {
							$transfer = "Transfer OUT Pending";
						} else {
							$transfer = "Pending Duplication";
						}
					}
					?>
				</tr>
		<?php endif; ?>
	<?php }?>
			</tbody>
	</table>
<?php }?>
	<?php if(!$results_found){?>
		<table cellspacing="0" class="search_results_table empty" class="grid"><tr class="open_row"><td colspan="7">No Results</td></tr></table>
	<?php }
?>
	
</div>
</body>


	<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	try {
	var pageTracker = _gat._getTracker("UA-880063-3");
	pageTracker._trackPageview();
	} catch(err) {}</script>
	
	<script type="text/javascript">
	document.write(unescape("%3Cscript src='" + ((document.location.protocol=="https:")?"https:":"http:") + "//snapabug.appspot.com/snapabug.js' type='text/javascript'%3E%3C/script%3E"));</script><script type="text/javascript">
	SnapABug.init('6572c503-f1f7-40eb-be0b-ef2840276aa4');
	SnapABug.setUserEmail('<?=$user_data['email_address'];?>');
	</script>

</html>
