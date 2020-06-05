<?php
		$show_notes=$this->Settings_model->get_all();
		$notes_settings=array($show_notes->result_array());

		$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
		$is_network=$this->Network_model->is_network($package_id);
		if($is_network->num_rows() > 0):
      $database_permissions_handler=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$user_data['id']);
    if($database_permissions_handler) {
      $database_permissions=$database_permissions_handler->pto_umask;
    }

		endif;	
?>
<div id="table_header_bar" class="header_reminders">
<?php
$reminder_status=$this->Settings_model->get_all();
get_instance()->load->helper('text_helper');
if($reminder_status->num_rows() == 0)
{
	$reminder_days=7;
}
if($reminder_status->num_rows() > 0)
{
	foreach($reminder_status->result() as $row)
	{
		$reminder_days=$row->reminder_duration / 86400;
	}
}

?>
	<div id="header_bar_left">Reminders &mdash; Horizon of  <?=$reminder_days > 1?$reminder_days." days":"one day"?></div>
	<div id="header_bar_right">
			<select id="location_jump">
				<option value="0">Jump to a location...</option>
				<?php foreach($locations->result() as $row){?>
				<option value="<?=$this->session->userdata('database_id')?>/<?=$row->id?>"><?=character_limiter(htmlspecialchars($row->name), 18, '...')?></option>
				<?php }?>
            </select>
    <?php if ($buttons_on == true):?>
			<a href="<?php echo site_url();?>reminders/clear/" class="sprite clear_date clear_button_disabled" rel="facebox"></a>			
            <a href="<?php echo site_url();?>reminders/update/" class="sprite reminder reminder_button_disabled" rel="facebox nm"></a>
    <?php endif;?>
	</div>
	<div class="clear"></div>
</div>


<?php if($num_reminders == 0): ?>
<?php if($locations->num_rows != 0): ?>
	<div class="empty"><h2>There is nothing to remind you of at the moment.  There are currently no reminders.  Make sure your horizon is set properly!</h2></div>
<?php endif; ?>
<?php else: ?>

<?php
	$table_id="reminder_table_without_notes";
if($show_notes->num_rows() > 0) {
if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
	{
		$table_id="reminder_table";
	}
}
?>
<table cellspacing="0" id="<?=$table_id?>" class="grid">
		<thead>
		<tr class="th_row">
			<th>File</th>		
			<th>Location</th>
			<th>Item Name</th>
			<th>Keywords</th>
			<?php
			if($show_notes->num_rows() > 0) {
				if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
				{
					?>
					<th>Notes</th>
					<?php
				}
			}
			?>
			<th class="header_itemActionDate">Action Date</th>
		</tr>
		</thead>
	<tbody>
	<?php foreach($items->result() as $item):?>
		<?php 
		$can_edit="edit_false";
		$item_umask=$this->Permissions_model->get_by_location($item->location_id,$this->user_data['id']);
			if($item_umask) {
			if($item_umask->pto_umask == 1 || $item_umask->pto_umask == 3 || ($organization_admin == $this->user_data['id'])) {
			    $can_edit="edit_true";
			  }
			}
			if($organization_admin == $this->user_data['id']):
			    $can_edit="edit_true";
			endif;	
		?>
		<tr class="full_row">
			<td class="first_column reminders_fileNumber" style="width:50px;">
				<div class="first_col_text">
                <span id="item-<?=$item->id?>" class="used-<?=$can_edit?>"></span>					
                    <input type="checkbox" <?php if ($can_edit == 'edit_false'):?>disabled="disabled"<?php endif;?> class="grid_checkbox" name="item_<?=$item->id?>" value="<?=$item->id?>">
					<span><?=$item->slot_num?></span>
				</div>
			</td>
            <td class="reminders_location"><?php echo htmlspecialchars($this->Location_model->get_by_id($item->location_id)->name)?> <?php if ($can_edit == 'edit_false'):?><span class="gray">(read only)</span><?php endif;?></td>
			<td class="reminders_itemName">
			
			<a href="<?=site_url()?>locations/view/<?=$this->session->userdata('database_id')?>/<?=$item->location_id?>#item_<?=$item->id?>"><?=htmlspecialchars($item->name) ?></a></td>
<!-- 			<?php echo $item->name; ?></td> -->
			<td><span class="keywords"><?php echo htmlspecialchars($item->keywords); ?></span></td>

			<?php
			if($show_notes->num_rows() > 0) {
				if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
				{
					?>
					<td><span class="notes"><?php echo htmlspecialchars($item->description);?></span></td>
					<?php
				}
			}
			?>
			<?php
				if($item->action_date)
				{
					$action_date=date("m/d/Y",$item->action_date);
				}
				else
				{
					$action_date=null;
				}
				
			?>
			<td class="itemActionDate"><?php echo $action_date; ?></td>
		</tr>
	<?php endforeach; ?>
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
	
	$('#reminder_table tbody tr,#reminder_table_without_notes tbody tr').hover(
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
            var data2 = info.attr("class");
            var data2_content = data2.split('-');
            var edit = data2_content[1];

				var link2 = document.createElement('a')

				link2.setAttribute('href', '/reminders/mark_completed/'+id+'/');
				link2.setAttribute('title', 'Clear reminder');
				link2.setAttribute('style', 'margin-top:-8px;');				
				
				var clear = new Image();
				clear.src = '<?=site_url()?>img/clear.png';				
				
				link2.appendChild( clear );


				var link = document.createElement('a')

				link.setAttribute('href', '/reminders/update/'+id+'/');
				link.setAttribute('title', 'Update reminder');
				link.setAttribute('rel', 'facebox nm');				
				link.setAttribute('style', 'margin-left:4px;');

				var update = new Image();
				update.src = '<?=site_url()?>img/update.png';				
				
				link.appendChild( update );
				
				nub.html(link2);
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
    <?php endif; //buttons_on?>
<?php endif; ?>
