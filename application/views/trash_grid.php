<?php
	$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
	$show_notes=$this->Settings_model->get_all();
	$notes_settings=array($show_notes->result_array());

	$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
	$is_network=$this->Network_model->is_network($package_id);
	if($is_network->num_rows() > 0):
		$db_perms=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id']);
		$database_permissions=null;
		if($db_perms):
			$database_permissions=$db_perms->pto_umask;
		endif;	
	endif;	
?>
	<div id="table_header_bar" class="header_trash">
		<div id="header_bar_left">Trash - <?=count($table_data->result_array())?> Item<?=count($table_data->result_array())==1?"":"s"?></div>
		<div id="header_bar_right">
			<select id="location_jump">
				<option value="0">Jump to a location...</option>
				<?php foreach($locations->result() as $row){?>
				<option value="<?=$this->session->userdata('database_id')?>/<?=$row->id?>"><?=character_limiter(htmlspecialchars($row->name), 18, '...')?></option>
				<?php }?>
				<option value="trash">Trash</option>
			</select>
            <?php if ($buttons_on == true):?>
                <a href="<?=site_url()?>trash/restore/" class="sprite restore restore_button_disabled" alt="Restore" rel="facebox nm"></a>
                <a href="<?=site_url()?>trash/delete" class="sprite delete_trash delete_trash_button_disabled" alt="Delete" rel="facebox nm"></a>
                <?php if($organization_admin == $this->user_data['id']): ?>
                <a href="<?=site_url()?>trash/empty_trash/" class="sprite empty_trash empty_trash_button" alt="empty trash" rel="facebox nm"></a>
                <?php endif; ?>
            <?php endif;?>
		</div>
		<div class="clear"></div>
	</div>
	
<?php
	$table_id="trash_table_without_notes";
if($show_notes->num_rows() > 0) {
if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
	{
		$table_id="trash_table";
	}
}
?>
	
	<?php if($table_data->num_rows() > 0): ?>
	<table cellspacing="0" id="<?=$table_id?>" class="grid">
		<thead>
		<tr class="th_row">		
			<th class="trash_header_itemName">Item Name</th>
			<th class="header_itemKeywords">Keywords</th>
			<?php
			if($show_notes->num_rows() > 0) {
				if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
				{
					?>
			<th class="header_itemDescription">Notes</th>
					<?php
				}
			}
			?>
			<th class="header_itemLocation">Prev. Location</th>					
			<th class="header_itemCategory">Category</th>
			<th class="header_itemActionDate">Action Date</th>
		</tr>
		</thead>
		<tbody>
<?php $count = 0;?>
<?php foreach ($table_data->result() as $row) { ?>
<?php
$loc_permissions=$this->Permissions_model->get_by_location($row->previous_location_id,$this->user_data['id']);
$can_edit="edit_false";
if($loc_permissions) {
if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3 || ($organization_admin == $this->user_data['id']) ) {
    $can_edit="edit_true";
  }
}
if ($organization_admin == $this->user_data['id']):
    $can_edit="edit_true";
endif;	
?>
<?php $count++;?>
		<?php
		$prev_location= $this->Location_model->get_by_id($row->previous_location_id);
		if($prev_location)
		{
			$prev_location_name=htmlspecialchars($this->Location_model->get_by_id($row->previous_location_id)->name);
		}
		else
		{
			$prev_location_name="<div class='gray'>(Deleted)</div>";
		}
		$prev_category=$this->Category_model->get_by_id($row->category_id);
		if($prev_category)
		{
			$prev_category_name=htmlspecialchars($this->Category_model->get_by_id($row->category_id)->name);
		}
		else
		{
			$prev_category_name="<div class='gray'>(Deleted)</div>";
		}
		?>
		<tr class="full_row" id="trash_<?=$row->id?>">
			<?php
				if($row->action_date)
				{
					$action_date=date("m/d/Y",$row->action_date);
				}
				else
				{
					$action_date=NULL;
				}
			?>
			<td class="trash_itemName">
				<span id="item-<?=$row->id?>" class="used-<?=$can_edit?>"></span>
                <input type="checkbox" <?php if ($can_edit == 'edit_false'):?>disabled="disabled"<?php endif;?>class="grid_checkbox" <?php if ($buttons_on == false):?>disabled="disabled"<?php endif;?> name="trash_<?=$row->id?>" value="<?=$row->id?>"><?=htmlspecialchars($row->name)?>
			</td>
			<td class="trash_itemKeywords"><span class="keywords"><?=htmlspecialchars($row->keywords) ?></span></td>
			<?php
			if($show_notes->num_rows() > 0) {
				if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
				{
					?>
					<td class="trash_itemDescription"><span class="notes"><?=htmlspecialchars($row->description) ?></span></td>
					<?php
				}
			}
			?>
			<td class="trash_itemPrevLocation"><?=$row->previous_location_id!=-1?$prev_location_name:''?></td>
			<td class="trash_itemCategory"><?=$row->category_id==0?'&nbsp;':$prev_category_name?></td>
			<td class="itemActionDate"><?=$action_date?></td>
		</tr>
<?php }?>
		</tbody>
	</table>
	
<div id="nub">

</div>
<?php if ($buttons_on == true):?>	
<script type="text/javascript">
	var nub = $('#nub');
	nub.hide();
	var last_move = 0;
	var allow_hide = 0;
	var open_time;
	
	$('#trash_table tbody tr,#trash_table_without_notes tbody tr').hover(
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

				var link = document.createElement('a');
				link.setAttribute('href', '/trash/restore/'+id+'/');
				link.setAttribute('rel', 'facebox nm');
				link.setAttribute('class', 'edit');
				link.setAttribute('style', 'margin-left:3px;');				
				link.appendChild( document.createTextNode('Restore') );

				//document.getElementById('nub').appendChild(link);
				nub.html(link);
				
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
    <?php endif;?>
	
	<?php else: ?>
		<?php if($locations->num_rows != 0): ?>
			<div class="empty"><h2>You like to keep things tidy!  Your trash is empty.</h2></div>
		<?php endif; ?>
<?php endif; ?>
