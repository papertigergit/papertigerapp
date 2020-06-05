<?php
      $packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']));
      $package=(array($packages->result()));
      if(($packages->num_rows() == 0)  || ($package[0][0]->allow_reports == 0)) {
          $this->session->set_flashdata('error',"Upgrade to Paper Tiger Pro!");
          redirect('reports');
      }
?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Paper Tiger Online - BETA (Watch out for them bugs...)</title>
		
		<noscript>
			<meta HTTP-EQUIV="REFRESH" content="0; url=<?php echo site_url(); ?>noscript.php"> 
		</noscript>		
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<meta http-equiv="Cache-Control" content="no-store" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta http-equiv="Cache-Control" content="must-revalidate" />
		<meta http-equiv="Cache-Control" content="max-age=0" />
		<meta http-equiv="content-script-type" content="text/javascript">
		<link rel="icon" type="image/png" href="<?php echo site_url(); ?>img/favicon.png" />
		<link href="<?=site_url()?>css/reset.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="<?=site_url()?>/css/print_css.css" media="print" rel="stylesheet" type="text/css"/>
		<link href="<?=site_url()?>/css/print_css_screen.css" media="screen" rel="stylesheet" type="text/css"/>		
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
						<?php  print $this->Database_model->get_by_id($this->session->userdata('database_id'))->name?> <div class="organization_name"><?php print $this->Organization_model->get_organization($user_data['organization_id'])->row()->name?></div>
					<?php }else{?>
						<?php print $this->Organization_model->get_organization($user_data['organization_id'])->row()->name ?>
					<?php } ?>
					
					<div id="reports_date"><?=$user_data['first_name']." ".$user_data['last_name']?> &mdash; <?php echo date("m/d/Y");?></div>
					
			</div>
		</div>
	
		<div id="table_header_bar">
			<div id="header_bar_left">Transfer Report</div>
			<div id="header_bar_right">
				<a  class="sprite confirm confirm_button_disabled" alt="Confirm" ></a>
				<a  class="sprite cancel cancel_button_disabled" alt="Cancel" ></a>
			</div>
			<div class="clear"></div>
		</div>
		<?php if($table_data->num_rows() > 0): ?>
		<div class="table_header_bar_2">Files to be Moved</div>
		<table cellspacing="0" class="confirmation_table" class="grid">
			<thead>
			<tr class="th_row">
				<th class="transferReport_date">Transfer Date</th>
				<th>Item Name</th>
				<th>Old Location</th>
				<th>New Location</th>
				<th class="transferReport_checkbox">Completed</th>
				<th class="transferReport_date_completed">Date</th>				
			</tr>
			</thead>
			<tbody>
			<?php $count = 0;?>
			<?php foreach ($table_data->result() as $row) { ?>
				<?php if($row->type=='move'){?>
					<?php $count++?>
		
					<tr class="full_row" id="item_<?=$row->item_id?>">
						<td><?=$row->transfer_date==null ? '' : date('m/d/Y g:i A',$row->transfer_date)?></td>
						<td><input type="checkbox" class="grid_checkbox" style="margin-right:10px;" name="item_<?=$row->id?>" value="<?=$row->id?>"><?=$row->name ?></td>
						<?php
							$items = $this->Item_model->get_from_location($row->origin_location_id);
							foreach($items->result() as $item){
								if($item->transfer_status_id==$row->id){
									$slot_id = $this->Item_model->get_by_id($row->item_id)->slot_num;
								}
							}
						?>
						<td class="from_col"><?= $this->Location_model->get_by_id($row->origin_location_id)->name.' <strong>'.$slot_id.'</strong'?></td>
						<?php if($row->destination_location_id == 0){
							$destination = "trash";
						} else {
							$destination_loc = $this->Location_model->get_by_id($row->destination_location_id);
							$items = $this->Item_model->get_from_location($row->destination_location_id);
							foreach($items->result() as $item){
								if($item->transfer_status_id==$row->id){
									$slot_id = $item->slot_num;
								}
							}
							$destination = $destination_loc->name.' <strong>'.$slot_id.'</strong>';
						} ?>
						<td><?=$destination?></td>
						<td class="transferReport_checkbox"><div id="report_checkbox"></div></td>
						<td class="transferReport_date_completed"><div id="report_date_completed"></div></td>						
					</tr>
				<?php }?>
			<?php }?>
			</tbody>
		</table>
		<?php $merge_items_present = false;?>
		<?php foreach ($table_data->result() as $row3) { 
			if($row3->type=='merge'){$merge_items_present=true;}
		 } ?>
		<?php if($merge_items_present){?>
			<div class="table_header_bar_2">Items to be Merged</div>
			<table cellspacing="0" class="confirmation_table" class="grid">
				<thead>
				<tr class="th_row">
					<th class="transferReport_date">Transfer Date</th>
					<th>Destination Item Name</th>
					<th>Source Item Name(s)</th>
					<th>Destination Location</th>
					<th class="transferReport_checkbox">Completed</th>
					<th class="transferReport_date_completed">Date</th>
				</tr>
				</thead>
				<tbody>
				<?php $count = 0;?>
				<?php foreach ($table_data->result() as $row) { ?>
					<?php if($row->type=='merge' && $row->merge_into == 0){?>
						<?php $count++?>
		
						<tr class="full_row" id="item_<?=$row->item_id?>">
						<td><?=$row->transfer_date==null ? '' : date('m/d/Y g:i A',$row->transfer_date)?></td>
							<td><?=$row->name ?></td>
							<?php 
								$res = $this->Transfers_model->get_merge_sources($row->id);
								$sources = '';
								foreach($res->result() as $row2){
									
									$items = $this->Item_model->get_from_location($row2->origin_location_id);
									foreach($items->result() as $item){
										if($item->transfer_status_id == $row2->id){
											$slot_id = $item->slot_num;
										}
									}
									$sources.=$this->Item_model->get_by_id($row2->item_id)->name.' <strong>'.$slot_id.'</strong>, ';
								}
								$sources = substr($sources,0,strlen($sources)-2);
							?>
							<td class="from_col"><?=$sources?></td>
							<?php
								$items = $this->Item_model->get_from_location($row->origin_location_id);
									foreach($items->result() as $item){
										if($item->transfer_status_id == $row->id){
											$slot_id = $item->slot_num;
										}
									}
							?>
							<td><?=$this->Location_model->get_by_id($row->origin_location_id)->name.' <strong>'.$slot_id.'</strong>'?></td>
							<td><div id="report_checkbox"></div></td>
							<td><div id="report_date_completed"></div></td>
						</tr>
					<?php }?>
				<?php }?>
				</tbody>
			</table>
		<?php }?>
		<?php $dupe_items = false; ?>
		<?php foreach($table_data->result() as $row4) {
			if($row4->type=='dupe') {$dupe_items=true;}
		}?>
		<!--start dupe-->
		<?php if($dupe_items){?>
			<div class="table_header_bar_2">Items to be Duplicated</div>

		<table cellspacing="0" class="confirmation_table" class="grid">
		<thead>
		<tr class="th_row">
			<th>Transfer Date</th>
			<th class="header_itemName">Item Name</th>
			<th class="header_transferFrom">From</th>
			<th class="header_transferTo">To</th>
			<th class="transferReport_checkbox">Completed</th>
			<th class="transferReport_date_completed">Date</th>				
<!-- 			<th>By</th> -->
		</tr>
		</thead>
		<tbody>
		<?php $count = 0;?>
		<?php foreach ($table_data->result() as $row) { ?>
			<?php if($row->type!='merge' && $row->type=='dupe'){?>
				<?php $count++?>
	
				<tr class="full_row" id="item_<?=$row->item_id?>">
					<td>
						<?=$row->transfer_date==null ? '' : date('m/d/Y g:i A',$row->transfer_date)?>
					</td>
					<td><?=$row->name ?></td>
					<?php
						$items = $this->Item_model->get_from_location($row->origin_location_id);
						foreach($items->result() as $item){
							$slot_id='';
								//$slot_id = $item->slot_num;
								//$row->item_id
								$slot_id=$this->Item_model->get_by_id($row->item_id)->slot_num;
						}
					?>
					<td class="from_col"><?= $this->Location_model->get_by_id($row->origin_location_id)->name.' '."<b>".$slot_id."</b>"?></td>
					<?php if($row->destination_location_id == 0){
						$destination = "trash";
					} else {
						$destination_loc = $this->Location_model->get_by_id($row->destination_location_id);
						$items = $this->Item_model->get_from_location($row->destination_location_id);
						foreach($items->result() as $item){
							if($item->transfer_status_id==$row->id){
								$slot_id = $item->slot_num;
							}
						}
						if($destination_loc)
						{
							$destination = $destination_loc->name.' '."<b>".$slot_id."</b>";
						}	
					} ?>
					<td><?=$destination?></td>
					<td><div id="report_checkbox"></div></td>
					<td><div id="report_date_completed"></div></td>
				</tr>
			<?php }?>
		<?php }?>
		</tbody>
	</table>
		<?php }?>
		<!--end dupe-->

		<?php $tossed_items = false; ?>
		<?php foreach($table_data->result() as $row5) {
			if($row5->type=='delete') {$tossed_items=true;}
		}?>
		<!--start tossed-->
		<?php if($tossed_items) {?>
			<div class="table_header_bar_2">Items to be Tossed</div>

	<table cellspacing="0" class="confirmation_table" class="grid">
		<thead>
		<tr class="th_row">
			<th>Transfer Date</th>
			<th class="header_itemName">Item Name</th>
			<th class="header_transferFrom">From</th>
			<th class="header_transferTo">To</th>
			<th class="transferReport_checkbox">Completed</th>
			<th class="transferReport_date_completed">Date</th>				
<!-- 			<th>By</th> -->
		</tr>
		</thead>
		<tbody>
		<?php $count = 0;?>
		<?php foreach ($table_data->result() as $row) { ?>
			<?php if($row->type!='merge' && $row->type=='delete'){?>
				<?php $count++?>
	
				<tr class="full_row" id="item_<?=$row->item_id?>">
					<td>
						<?=$row->transfer_date==null ? '' : date('m/d/Y g:i A',$row->transfer_date)?>
					</td>
					<td><?=htmlspecialchars($row->name)?></td>
					<?php
						$items = $this->Item_model->get_from_location($row->origin_location_id);
						foreach($items->result() as $item){
							if($item->transfer_status_id==$row->id){
								$slot_id = $item->slot_num;
							}
						}
					?>
					<td class="from_col"><?= $this->Location_model->get_by_id($row->origin_location_id)->name.' '."<b>".$slot_id."</b>"?></td>
					<?php if($row->destination_location_id == 0){
						$destination = "trash";
					} else {
						$destination_loc = $this->Location_model->get_by_id($row->destination_location_id);
						$items = $this->Item_model->get_from_location($row->destination_location_id);
						foreach($items->result() as $item){
							if($item->transfer_status_id==$row->id){
								$slot_id = $item->slot_num;
							}
						}
						if($destination_loc)
						{
							$destination = $destination_loc->name.' '.$slot_id;
						}	
					} ?>
					<td><?=$destination?></td>
					<td><div id="report_checkbox"></div></td>
					<td><div id="report_date_completed"></div></td>
				</tr>
			<?php }?>
		<?php }?>
		</tbody>
	</table>

		<!--end tossed-->

<?php } ?>
		<?php else: ?>
	<?php endif; ?>
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
