<!--
the variable for database permission is "$database_permissions"
0 - cannot read or write
1 - write
2 - read only
3 - means 1+2 (i.e can read and write) in database
-->
	<div id="table_header_bar" class="header_locations">
		<div id="header_bar_left">Locations  
		
		
<!--
		<div id="search_toggle">
    <tr class="on_off paper_digital_toggle">
      <th class="paper_toggle"><label for="on_off">Paper Search</label></th>
      <td id="paper_check">
        <input type="checkbox" checked="checked" id="on_off" />
      </td>
    </tr>
    <tr class="on_off paper_digital_toggle">
      <th class="digital_toggle"><label for="on_off_on digital_toggle">Digital Search</label></th>
      <td id="digital_check">
        <input type="checkbox" checked="checked" id="on_off_on"/>
      </td>
    </tr>
  </div>
-->
  
  
  
  </div>
        <div id="header_bar_right">
            <select id="location_jump">
				<option value="0">Jump to a location...</option>
                <?php 
		foreach($table_data->result() as $row){
                    $loc_permissions=$this->Permissions_model->get_by_location($row->id,$this->user_data['id']);
                    $can_edit="edit_false";
                    if($loc_permissions) {
                    if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3  || ($organization_admin == $this->user_data['id'])  ) {
			$can_edit="edit_true";

                      }
                    }
                ?>
                <option value="<?=$this->session->userdata('database_id')?>/<?=$row->id?>"><?=character_limiter(htmlspecialchars($row->name), 18, '...')?></option>
				<?php }?>
				<option value="trash">Trash</option>
			</select>
        <?php if($buttons_on == true): ?>
			<a href="<?=site_url()?>locations/add/" class="sprite new_button" alt="New" rel="facebox nm"></a>
			<a href="<?=site_url()?>locations/edit/" class="sprite edit edit_button_disabled" alt="Edit" rel="facebox nm"></a>
			<a href="<?=site_url()?>locations/delete/" class="sprite delete delete_button_disabled" alt="Delete" rel="facebox modal"></a>
        <?php endif; ?>
		</div>
		<div class="clear"></div>
    </div>

	<?php if($table_data->num_rows() > 0): ?>
	<table cellspacing="0" id="location_table" class="grid">
		<thead>
		<tr class="th_row">
			<th class="header_locationName">Name</th>
			<th class="header_locationDescription">Description</th>
			<th class="header_locationCapacity">Capacity</th>
			<th class="header_locationItemQuantity">Items</th>
			<th class="header_locationReviewDate">Review Frequency</th>
		</tr>
		</thead>
		<tbody>
<?php $count = 0;?>
<?php foreach ($table_data->result() as $row) { ?>
<?php
$loc_permissions=$this->Permissions_model->get_by_location($row->id,$this->user_data['id']);
$can_edit="edit_false";
if($loc_permissions) {
if($loc_permissions->pto_umask == 1 || $loc_permissions->pto_umask == 3 || ($organization_admin == $this->user_data['id']) ) {
    $can_edit="edit_true";
  }
}
if($is_network->num_rows() == 0):
	$database_permissions=3;
	$can_edit="edit_true";
	$buttons_on = true;
endif;
if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
	$database_permissions=3;
	$can_edit="edit_true";
	$buttons_on = true;
endif;	

?>
<?php $count++;?>
		<tr class="full_row<?= (($modified_rows!==FALSE) && in_array($row->id, $modified_rows))?' modified_row':''?>" id="location_<?=$row->id?>">
            <td><input type="checkbox" class="grid_checkbox" <?php if ($can_edit == "edit_false"):?>disabled="disabled"<?php endif;?> name="item_<?=$row->id?>" value="<?=$row->id?>"><a href="<?=site_url()?>locations/view/<?=$this->session->userdata('database_id')?>/<?=$row->id?>"><?=htmlspecialchars($row->name) ?></a> <span id="loc-<?=$row->id?>" class="used-<?=$can_edit;?>"></span>
<?php if ($can_edit == "edit_false"):?><span class="gray">(read only)</span><?php endif;?> 
			</td>
			<td><?=htmlspecialchars($row->description) ?></td>
			<td class="centered"><?=$row->capacity ?></td>
			<td class="centered"><?=$row->num_items ?></td>
			<?php
			$review_interval='';
			if($row->review_interval_num == 0 && $row->next_review != 0){
				$review_interval=date("m/d/Y",$row->next_review);
			} else if ($row->review_interval_num != 0){
				$review_interval=$row->review_interval_num." ".$row->review_interval_string;
			}
			?>
			<td class="location_reviewDate centered"><?=$review_interval==0?'&nbsp;':$review_interval?></td>
		</tr>
<?php }?>
	</tbody>
    </table>
<?php if ($buttons_on == true):?>
    <div id="table_toolbar">
		<a href="<?=site_url()?>locations/add/" alt="Add a new Location" class="sprite new_loc_button" rel="facebox nm"></a>
    </div>
<?php endif;?>
	
	
	<?php endif; ?>

<div id="nub">

</div>		
	
<script type="text/javascript">
	var nub = $('#nub');
	nub.hide();
	var last_move = 0;
	var allow_hide = 0;
	var open_time;
	
	$('#location_table tbody tr').hover(
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

            var link2 = document.createElement('a');
            link2.setAttribute('href', '/locations/delete/'+id+'/');
            link2.setAttribute('rel', 'facebox nm');

            var trash = new Image();
            trash.src = '<?=site_url()?>img/trashcan.png';
            
            link2.appendChild( trash );

            var link = document.createElement('a');
            link.setAttribute('href', '/locations/edit/'+id+'/');
            link.setAttribute('rel', 'facebox nm');
            link.setAttribute('class', 'edit');
            link.appendChild( document.createTextNode('Edit') );

            nub.html(link2);
            nub.append(' ');
            nub.append(link);
            
            nub.css( {"top": row_offset.top} );
            if (edit != "edit_false"){
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
    
    <?php if (($table_data->num_rows() < 1) && ($autoload == '1')):?>
        <script>jQuery.facebox({ ajax: '<?=site_url()?>locations/add/' });</script> 
    <?php endif;?>

