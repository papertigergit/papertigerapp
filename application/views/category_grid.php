<?php
$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
$is_network=$this->Network_model->is_network($package_id);
if($is_network->num_rows() > 0):
	$database_permissions=$this->Permissions_model->get_by_database($this->session->userdata('database_id'),$this->user_data['id']);
	if($database_permissions):
		$database_permissions=$database_permissions->pto_umask;
	endif;	
endif;	
if($is_network->num_rows() == 0):
	$database_permissions=3;
endif;
?>
	<div id="table_header_bar" class="header_categories">
		<div id="header_bar_left">Categories</div>
		<div id="header_bar_right">
			<select id="location_jump">
				<option value="0">Jump to a location...</option>
				<?php foreach($locations->result() as $row){?>
				<option value="<?=$this->session->userdata('database_id')?>/<?=$row->id?>"><?=htmlspecialchars(character_limiter($row->name, 18, '...'))?></option>
				<?php }?>
				<option value="trash">Trash</option>
            </select>
            <?php if ($buttons_on == true):?>
                <a href="<?=site_url()?>categories/add/" class="sprite new_button" alt="New" rel="facebox"></a>
                <a href="<?=site_url()?>categories/edit/" class="sprite edit edit_button_disabled" alt="Edit" rel="facebox"></a>
                <a href="<?=site_url()?>categories/delete/" class="sprite delete delete_button_disabled" alt="Delete" rel="facebox modal"></a>
            <?php endif;?>
		</div>
		<div class="clear"></div>
	</div>
	<?php if($table_data->num_rows() > 0): ?>
	<table cellspacing="0" id="category_table" class="grid">
		<thead>
		<tr class="th_row">
			<th>Name</th>
			<th>Description</th>
			<th>Date Added</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$cat_names=array();
		$similar_names=$table_data->result_array();
		for($i=0;$i<$table_data->num_rows();$i++){
			$cat_names[]=$similar_names[$i]['name'];
		}
		?>
<?php $count = 0;?>
<?php foreach ($table_data->result() as $row) { 
	$can_edit="edit_false";
	if(($row->owner == $this->user_data['id']) || ($organization_admin == $this->user_data['id'])):
		$can_edit="edit_true";
	endif;	
	?>
<?php $count++;?>
		<tr class="full_row<?= (($modified_rows!==FALSE) && in_array($row->id, $modified_rows))?' modified_row':''?>" id="category_<?=$row->id?>">
			<td>
			<?php
			$cat_owner_names='';
			if($is_network->num_rows() > 0):
					$category_owner=$this->User_model->get_user($row->owner)->row();
					if($category_owner) {
						$cat_owner_names=$category_owner->first_name." ".$category_owner->last_name;
					}
			endif;	
			if($cat_owner_names==''):
				$cat_owner_names=$admin_name;
			endif;	
			?>
            <input type="checkbox" <?php if ($can_edit == 'edit_false'):?>disabled="disabled"<?php endif;?> class="grid_checkbox" name="item_<?=$row->id?>" value="<?=$row->id?>"><a href="<?=site_url()?>categories/view/<?=$row->id?>"><?=htmlspecialchars($row->name)?></a> 
			<?php echo "by ".$cat_owner_names?>
            <span id="cat-<?=$row->id?>" class="used-<?=$can_edit?>"></span>
			</td>
			<td><?=htmlspecialchars($row->description) ?></td>
			<td><?=date("m/d/Y",$row->date_added)?></td>
		</tr>
<?php }?>
		</tbody>
    </table>

	<div id="table_toolbar">
    <?php if ($buttons_on == true):?>
        <a style="margin-bottom:10px;" href="<?=site_url()?>categories/add/" alt="Add a new Category" class="sprite new_cat_button" rel="facebox nm"></a>
    <?php endif;?>

            <?php if ($is_network->num_rows() > 0):?>
                <h3 style="color:gray;">Tip: Categories that you've created are always shown. Only the person who created a category or the account administrator (<a href="mailto:<?=$admin_email?>"><?=$admin_name?></a>) can edit or delete a category.</h3>
                <?php endif;?>
    </div>

	<?php endif; ?>
	
<div id="nub">

</div>		
	
<script type="text/javascript">
	var nub = $('#nub');
	nub.hide();
	var last_move = 0;
	var allow_hide = 0;
	var open_time;
	
	$('#category_table tbody tr').hover(
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
				link2.setAttribute('href', '/categories/delete/'+id+'/');
				link2.setAttribute('rel', 'facebox nm');
/* 						link2.appendChild( document.createTextNode('Delete') ); */

				var trash = new Image();
				trash.src = '<?=site_url()?>img/trashcan.png';
				
				link2.appendChild( trash );

				var link = document.createElement('a');
				link.setAttribute('href', '/categories/edit/'+id+'/');
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
				//$('a[rel*=facebox]').facebox();
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
