	<?php
			$show_notes=$this->Settings_model->get_all();
			$notes_settings=array($show_notes->result_array());
	?>
	<?php $category_name = $this->Category_model->get_by_id($category_id)->name?>
	
	<?php if($table_data->num_rows() > 0): ?>
	
		
	<div id="table_header_bar" class="header_items">
		<div id="header_bar_left">Category: <?=htmlspecialchars($category_name)?></div>
		<div id="header_bar_right">
			<select id="category_jump">
				<option value="0">Jump to a category...</option>
				<?php foreach($categories->result() as $row){?>
				<option value="<?=$row->id?>"><?=htmlspecialchars(character_limiter($row->name, 18, '...'))?></option>
				<?php }?>
			</select>
			<a href="<?=site_url()?>items/edit/" class="sprite edit edit_button_disabled" value="<?=$category_id?>" alt="Edit" rel="facebox nm"></a>
			<span style="position:relative">
				<span class="sprite transfer transfer_button_disabled" value="<?=$category_id?>" alt="Transfer"></span>
				<div class="submenu">
					<ul>
						<li><a id="move" class="submenu_link" href="" rel="facebox nm">move</a></li>
						<li><a id="merge" class="submenu_link" href="" rel="facebox nm">merge</a></li>
						<li><a id="dupe" class="submenu_link" href="" rel="facebox nm">duplicate</a></li>
					</ul>
				</div>
			</span>
			<a href="<?=site_url()?>items/delete/" class="sprite toss toss_button_disabled" alt="Delete" rel="facebox modal"></a>
		</div>
		<div class="clear"></div>
	</div>
	<?php
			$table_id="item_table_by_category_without_notes";
	if($show_notes->num_rows() > 0) {
	if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
		{
			$table_id="item_table_by_category";
		}
	}
	?>
	
	<table cellspacing="0" id="<?=$table_id?>" class="grid">
		<thead>
		<tr class="th_row">
			<th class="header_itemNumber">Item</th>
			<th class="header_itemName">Item Name</th>
			<th class="header_itemKeywords">Keywords</th>
			<?php
			if($show_notes->num_rows() > 0) {
			if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
			{
				?>
				<th class="header_itemDescription">Notes</th>
				<?php
			}}
			?>
			<th class="header_itemLocation">Location</th>
			<th class="header_itemActionDate">Action Date</th>
			<th class="header_itemStatus">Status</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($table_data->result() as $row) { ?>
		<?php 
		$can_edit="edit_false";
		$item_umask=$this->Permissions_model->get_by_location($row->location_id,$this->user_data['id']);
			if($item_umask) {
			if($item_umask->pto_umask == 1 || $item_umask->pto_umask == 3 || ($organization_admin == $this->user_data['id'])) {
			    $can_edit="edit_true";
			  }
			}
			if($organization_admin == $this->user_data['id']):
			    $can_edit="edit_true";
			endif;	
		?>
			<?php if($row->id != NULL) {?>
				<tr class="full_row<?= (($modified_rows!==FALSE) && in_array($row->id, $modified_rows))?' modified_row':''?>" id="item_<?=$row->id?>">
					<td class="first_column">
						<span id="item-<?=$row->id?>" class="used-<?=$can_edit?>"></span>
						<div class="first_col_text">
                            <?php if(empty($row->transfer_status_id) || $row->destination_location_id!==NULL){?><input type="checkbox" <?php if($can_edit == 'edit_false'):?>disabled="disabled"<?php endif;?> class="grid_checkbox" style="margin-right: -27px;" name="item_<?=$row->id?>" value="<?=$row->id?>"><?php }?>
							<span style="margin-left:27px;"><?=$row->slot_num?></span>
						</div>
					</td>
					<td class="item_name"><?=htmlspecialchars($row->name) ?></td>
					<td><span class="keywords"><?=htmlspecialchars($row->keywords) ?></span></td>
					<?php
					if($show_notes->num_rows() > 0) {
						if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
						{
							?>
						<td><span class="notes"><?=htmlspecialchars($row->description) ?></span></td>
							<?php
						}
					}
					?>
                        <td class="itemLocation"><?=htmlspecialchars($this->Location_model->get_by_id($row->location_id)->name)?> <?php if ($can_edit == 'edit_false'):?><span class="gray">(read only)<?php endif;?></td>
					<td class="itemActionDate"><?=$row->action_date==0?'&nbsp;':date("m/d/Y",$row->action_date)?></td>
					<?php 
					$transfer = '';
					if(!empty($row->transfer_status_id) && $row->type =='delete' && $row->destination_location_id==="0"){
						$transfer = "Pending Deletion";
					} else if(!empty($row->transfer_status_id) && $row->type =='merge'){
						if($row->merge_into == 0){
							$transfer = 'Merge Destination';
						} else {
							$transfer = 'Merge Source';
						}
					} else if(!empty($row->transfer_status_id) && $row->destination_location_id===NULL) {
						$transfer = "Transfer IN Pending";
					} else if(!empty($row->transfer_status_id)) {
						if($row->type == 'move') {
							$transfer = "Transfer OUT Pending";
						} else {
							$transfer = "Pending Duplication";
						}
					}
					?>
					<td class="itemStatus"><?=$transfer?></td>
				</tr>
			<?php } ?>
		<?php }?>
	</tbody>
	</table>
	
	<?php else: ?>
		<div class="empty"><h2>There are no items in the category "<?=htmlspecialchars($category_name)?>"!</h2></div>
	<?php endif; ?>
	
<div id="nub">

</div>		
	
<script type="text/javascript">
	var nub = $('#nub');
	nub.hide();
	var last_move = 0;
	var allow_hide = 0;
	var open_time;
	
	$('#item_table_by_category tbody tr,#item_table_by_category_without_notes tbody tr').hover(
		function () { 
			//mouse in
			nub.stop(true);
			nub.hide();
			
			
			//Morris, the function that gets the item ID is broken and results in undefined, needs to be fixed for this to work!
			var info = $('td:first-child span', this);
			var pos = $('td', this);
			var row_offset = pos.offset();
			var bgcolor = $(this).css('background-color');
			nub.css('background-color', bgcolor);
			nub.css('border-right', bgcolor);
			var data = info.attr("id");
			var arr = data.split('-');
			var id = arr[1];
            var data2 = info.attr("class");
            var data2_content = data2.split('-');
            var edit = data2_content[1];

				var link2 = document.createElement('a');
				link2.setAttribute('href', '/items/delete/'+id+'/');
				link2.setAttribute('rel', 'facebox nm');
/* 						link2.appendChild( document.createTextNode('Delete') ); */

				var trash = new Image();
				trash.src = '<?=site_url()?>img/trashcan.png';
				
				link2.appendChild( trash );

				var link = document.createElement('a');
				link.setAttribute('href', '/items/edit/'+id+'/');
				link.setAttribute('rel', 'facebox nm');
				link.setAttribute('class', 'edit');
				link.appendChild( document.createTextNode('Edit') );

				//document.getElementById('nub').appendChild(link);
				nub.html(link2);
				nub.append(' ');
				nub.append(link);
				
            if (edit != "edit_false"){
			nub.css( {"top": row_offset.top} );
			nub.show();		
			$('a[rel*=facebox]').unbind('click');	
			$('a[rel*=facebox]').facebox();
			open_time = 0;
            }	
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
	
