<?php
?>
	<div id="table_header_bar">
        <div id="header_bar_left">Confirmations</div>
        <?php if ($buttons_on == true):?>
		<div id="header_bar_right">
			<a href="<?=site_url()?>confirmations/accept" class="sprite confirm confirm_button_disabled" alt="Confirm" rel="facebox"></a>
			<a href="<?=site_url()?>confirmations/cancel" class="sprite cancel cancel_button_disabled" alt="Cancel" rel="facebox"></a>
            </div>
        <?php endif;?>
		<div class="clear"></div>
	</div>
	<?php
	if($table_data->num_rows() > 0)
	{
		$confirm_move=$confirm_delete=$confirm_merge=$confirm_dupe=0;
		foreach($table_data->result() as $confirmation_type)
		{
			if($confirmation_type->type == "delete") { $confirm_delete++; }
			if($confirmation_type->type == "move") { $confirm_move++; }
			if($confirmation_type->type == "merge") { $confirm_merge++; }
			if($confirmation_type->type == "dupe") { $confirm_dupe++; }
		}
	}
	?>
	<?php if($table_data->num_rows() > 0): ?>
	<?php if($confirm_move > 0) { ?>
	<div class="table_header_bar_2">Items to be Moved</div>
	<table cellspacing="0" class="confirmation_table" class="grid">
		<thead>
		<tr class="th_row">
			<th class="header_itemName">Item Name</th>
			<th class="header_transferFrom">From</th>
			<th class="header_transferTo">To</th>
			<th>Transfer Date</th>
<!-- 			<th>By</th> -->
		</tr>
		</thead>
		<tbody>
		<?php $count = 0;?>
		<?php foreach ($table_data->result() as $row) { ?>
			<?php if($row->type!='merge' && $row->type=='move'){?>
				<?php $count++?>
	
				<tr class="full_row" id="item_<?=$row->item_id?>">
					<td>
					<span id="item-<?=$row->id?>" rel="used"></span>
					<input type="checkbox" class="grid_checkbox" style="margin-right:10px;" name="item_<?=$row->id?>" value="<?=$row->id?>"><?=htmlspecialchars($row->name) ?>
					</td>
					<?php
					$slot_id='';
						$items = $this->Item_model->get_from_location($row->origin_location_id);
						foreach($items->result() as $item){
							if($item->transfer_status_id==$row->id){
								$slot_id = $item->slot_num;
							}
						}
					?>
					<td class="from_col"><?= $this->Location_model->get_by_id($row->origin_location_id)->name.' '.$slot_id?></td>
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
					<td>
						<?=$row->transfer_date==null ? '' : date('m/d/Y g:i A',$row->transfer_date)?>
					</td>
				</tr>
			<?php }?>
		<?php }?>
		</tbody>
	</table>
	<?php } ?>

<!--items to be duplicated -->
	<?php if($confirm_dupe > 0) { ?>
	<div class="table_header_bar_2">Items to be Duplicated</div>
	<table cellspacing="0" class="confirmation_table" class="grid">
		<thead>
		<tr class="th_row">
			<th class="header_itemName">Item Name</th>
			<th class="header_transferFrom">From</th>
			<th class="header_transferTo">To</th>
			<th>Transfer Date</th>
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
					<span id="item-<?=$row->id?>" rel="used"></span>
					<input type="checkbox" class="grid_checkbox" style="margin-right:10px;" name="item_<?=$row->id?>" value="<?=$row->id?>"><?=$row->name ?></td>
					<?php
						$items = $this->Item_model->get_from_location($row->origin_location_id);
						foreach($items->result() as $item){
							$slot_id='';
								//$slot_id = $item->slot_num;
								//$row->item_id
								$slot_id=$this->Item_model->get_by_id($row->item_id)->slot_num;
						}
					?>
					<td class="from_col"><?= $this->Location_model->get_by_id($row->origin_location_id)->name.' '.$slot_id?></td>
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
					<td>
						<?=$row->transfer_date==null ? '' : date('m/d/Y g:i A',$row->transfer_date)?>
					</td>
				</tr>
			<?php }?>
		<?php }?>
		</tbody>
	</table>
	<?php } ?>	
<!--Items Going to the trash -->

	<?php if($confirm_delete > 0 ) { ?>
	<div class="table_header_bar_2">Items to be Tossed</div>
	<table cellspacing="0" class="confirmation_table" class="grid">
		<thead>
		<tr class="th_row">
			<th class="header_itemName">Item Name</th>
			<th class="header_transferFrom">From</th>
			<th class="header_transferTo">To</th>
			<th>Transfer Date</th>
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
					<span id="item-<?=$row->id?>" rel="used"></span>
					<input type="checkbox" class="grid_checkbox" style="margin-right:10px;" name="item_<?=$row->id?>" value="<?=$row->id?>"><?=htmlspecialchars($row->name) ?></td>
					<?php
						$items = $this->Item_model->get_from_location($row->origin_location_id);
						foreach($items->result() as $item){
							if($item->transfer_status_id==$row->id){
								$slot_id = $item->slot_num;
							} 
						}
					?>
					<td class="from_col"><?= $this->Location_model->get_by_id($row->origin_location_id)->name.' '.($slot_id??"")?></td>
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
					<td>
						<?=$row->transfer_date==null ? '' : date('m/d/Y g:i A',$row->transfer_date)?>
					</td>
				</tr>
			<?php }?>
		<?php }?>
		</tbody>
	</table>
	<?php } ?>

	
	<?php $merge_items_present = false;?>
	<?php foreach ($table_data->result() as $row3) { 
		if($row3->type=='merge'){$merge_items_present=true;}
	 } ?>
	<?php if($merge_items_present){?>
		<div class="table_header_bar_2">Items to be Merged</div>
		<table cellspacing="0" class="confirmation_table" class="grid">
			<thead>
			<tr class="th_row">
				<th class="header_itemName">Destination Item</th>			
				<th class="header_transferFrom">Source Item(s)</th>	
				<th class="header_transferTo">To</th>						
				<th>Transfer Date</th>
			</tr>
			</thead>
			<tbody>
			<?php $count = 0;?>
			<?php foreach ($table_data->result() as $row) { ?>
				<?php if($row->type=='merge' && $row->merge_into == 0){?>
					<?php $count++?>
	
					<tr class="full_row" id="item_<?=$row->item_id?>">	
					
						<td>
							<span id="item-<?=$row->id?>" rel="used"></span>
							<input type="checkbox" class="grid_checkbox" style="margin-right:10px;" name="item_<?=$row->id?>" value="<?=$row->id?>">						
							<?=htmlspecialchars($row->name) ?>
						</td>								
						
						<?php 
							$res = $this->Transfers_model->get_merge_sources($row->id);
							$sources = '';
							foreach($res->result() as $row2){
								
								$items = $this->Item_model->get_from_location($row2->origin_location_id);
								unset($slot_id);
								foreach($items->result() as $item){
									if($item->transfer_status_id == $row2->id){
										$slot_id = $item->slot_num;
									}
								       if(!isset($slot_id)) {
										$slot_id=$this->Item_model->get_by_id($row->item_id)->slot_num;
                                                        		}

								}
								$sources.=$this->Item_model->get_by_id($row2->item_id)->name.' <strong>'.$slot_id.'</strong><br /> ';
							}
							$sources = substr($sources,0,strlen($sources)-2);
						?>
						<td class="from_col">						
						<?=$sources?>
						</td>
						
						<?php
							$items = $this->Item_model->get_from_location($row->origin_location_id);
								foreach($items->result() as $item){
									if($item->transfer_status_id == $row->id){
										$slot_id = $item->slot_num;
									}
								}
						?>
						
						<td><?=$this->Location_model->get_by_id($row->origin_location_id)->name.' <strong>'.$slot_id.'</strong>'?></td>
						
						<td>
						<?=$row->transfer_date==null ? '' : date('m/d/Y g:i A',$row->transfer_date)?>
						</td>
					</tr>
				<?php }?>
			<?php }?>
			</tbody>
		</table>	
		
	<?php }?>

	<div id="nub">

</div>		
	
<script type="text/javascript">
	var nub = $('#nub');
	nub.hide();
	var last_move = 0;
	var allow_hide = 0;
	var open_time;
	
	$('.confirmation_table tbody tr').hover(
		function () { 
			//mouse in
			nub.stop(true);
			nub.hide();
			
			var info = $('td:first-child span', this);
			var pos = $('td', this);
			var row_offset = pos.offset();
			var bgcolor = $(this).css('background-color');
			nub.css('background-color', bgcolor);
			nub.css('border-right', bgcolor);
			var data = info.attr("id");
			var arr = data.split('-');
			var id = arr[1];

				var link2 = document.createElement('a')

				link2.setAttribute('href', '/confirmations/accept_single/'+id+'/');
				link2.setAttribute('title', 'Accept Transfer');
				
				var confirm = new Image();
				confirm.src = '<?=site_url()?>img/clear.png';				
				
				link2.appendChild( confirm );


				var link = document.createElement('a')

				link.setAttribute('href', '/confirmations/cancel_single/'+id+'/');
				link.setAttribute('title', 'Cancel Transfer');
				link.setAttribute('style', 'margin:0 2px 0 4px;');

				var cancel = new Image();
				cancel.src = '<?=site_url()?>img/cancel.png';				
				
				link.appendChild( cancel );
				
				nub.html(link2);
				nub.append(link);
				
			nub.css( {"top": row_offset.top} );
			nub.show();		
			$('a[rel*=facebox]').unbind('click');	
			$('a[rel*=facebox]').facebox();
			open_time = 0;
			
		},
		function () {
			//mouse out
			open_time = new Date().getTime();
			setTimeout( hideNub, 500 );
		}
	);
	
	nub.hover (
		function() {
			nub.show();
			open_time = 0;
		},
		function () {
			open_time = new Date().getTime();
			setTimeout( hideNub, 500 );
		}
	);
	
	//delay before closing nub
	function hideNub() 
	{
		var time_now = new Date().getTime();
		
		if(  open_time != 0 && time_now - open_time > 500 )
		{
			nub.hide();
		}
		else
		{
			setTimeout( hideNub, 500 );
		}
	}
</script>	
	
	<?php else: ?>
		<?php if($locations->num_rows != 0): ?>
			<div class="empty"><h2>Good Job! It looks like you are caught up for now. There are no pending items to confirm.</h2></div>
		<?php endif; ?>	
<?php endif; ?>
