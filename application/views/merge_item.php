<script>
$(document).ready(function(){
	$('#file_to_merge_by_slot').numeric();
});

$(function(){

	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();
	
	var loc_id;
	var item_to_merge_id_from_select;
	$('#new_location').change(function(){
		loc_id = $('#new_location').val();
    /* alert($("#new_location option:selected").attr('id')); */
		$('#merge_by_slot_result').hide();
		$('#file_to_merge_by_slot').val('');

    loc_num_items = $("#new_location option:selected").attr('id');
    if (loc_num_items > 400){
      $('#file_to_merge_select').hide();
			$('#merge_field').html('Item # to Merge Into');
      $('#file_to_merge_by_slot').show();
    }
    if (loc_num_items <= 400){
			$('#merge_field').html('File to Merge Into');
			resetFileList(loc_id);
      $('#file_to_merge_by_slot').hide();
      $('#file_to_merge_select').show();
    }
	});

	$('#file_to_merge_select').change(function(){
		item_to_merge_id_from_select = $('#file_to_merge_select').val();
		$('#file_to_merge').val(item_to_merge_id_from_select);
	});

  var ids = [];
  <?php foreach($item_ids as $id){?>
  ids.push(<?=$id?>);
  <?php }?>

	$('#file_to_merge_by_slot').keyup(function() {
		/* alert('Handler for .keyup() called.'); */
		if ($('#file_to_merge_by_slot').val() != ''){
			$('#spinner').show();
			delay(function(){
				$.getJSON('/items/get_item_by_slot/'+loc_id +'/'+urlencode($('#file_to_merge_by_slot').val()), {'prevent_caching':new Date().getTime()}, function(data){
					if(data != '[]') {
            if((jQuery.inArray(parseInt(data.id),ids)>=0)){
							$('#merge_by_slot_result').hide();
							$('#merge_by_slot_same_item').show();
							$('#spinner').hide();
              $('#submit').attr('disabled', 'disabled');
            }
            else{
              if(data.transfer_status_id == 0 || data.transfer_status_id === null){
                //If there is data, do something with it
                /* alert('item name is ' +  data.name + ' and id is ' + data.id); */
                $('#merge_by_slot_pending_transfer').hide();
                $('#merge_by_slot_same_item').hide();
                $('#file_to_merge').val(data.id);
                $('#merge_item_number').html(data.slot_num);
                $('#merge_item_name').html(data.name);
                $('#merge_by_slot_result').slideDown();
                $('#spinner').hide();
                $('#submit').attr('disabled', '');
              }
              else{
                $('#merge_by_slot_result').hide();
                $('#merge_by_slot_same_item').hide();
                $('#merge_by_slot_pending_transfer').show();
                $('#spinner').hide();
                $('#submit').attr('disabled', 'disabled');
              }
            }
					} else {
						//If there is no data, alert them
            $('#merge_by_slot_result').hide();
            $('#merge_by_slot_same_item').hide();
						$('#merge_by_slot_result').hide();
						alert('No item found! Please enter another item number!');
						$('#spinner').hide();
            $('#submit').attr('disabled', 'disabled');
          }
				});
			}, 1000 );
		}
		if($('#file_to_merge_by_slot').val() == '') {
			$('#spinner').hide();
			$('#merge_by_slot_pending_transfer').hide();
			$('#merge_by_slot_result').hide();
      $('#submit').attr('disabled', 'disabled');
		}
	});
})

function resetFileList(loc_id){

  var ids = [];
  <?php foreach($item_ids as $id){?>
  ids.push(<?=$id?>);
  <?php }?>
	
	$.getJSON('/items/file_list/'+urlencode(loc_id), {'prevent_caching':new Date().getTime()}, function(data){
		$('#file_to_merge_select').empty().append('<option value="-1">Select File...</option>');
		$.each(data.items, function(i, item) { 
			if((jQuery.inArray(item.id,ids)>=0) || (item.tsid != 0)){
				$('#file_to_merge_select').append('<option value="'+item.id+'" disabled>'+item.slot+') '+item.name+'</option>')	
			} 
			else {
				$('#file_to_merge_select').append('<option value="'+item.id+'">'+item.slot+') '+item.name+'</option>')	
			}
		});
	})
}

//Initialize variable to check if there are any huge locations
var large_location = false;
</script>
<div id="merge_item_form" class="facebox_form">
	<div id="facebox_header"><div>Merge These Files</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">Use the Merge function to merge a file with an existing file in your database.</p>
		<form action="<?=site_url()?>items/transfer/merge/<?=implode("/",$item_ids)?>" method="POST" class="form" name="merge_item">
			<div id="form_padding">
				<div class="form_divider">
					<label for="new_location">New Location</label>
					<select name="new_location" id="new_location" class="select first_input">
						<option value="-1">Select Destination...</option>
						<?php foreach($locations->result() as $row){?>
              <option id="<?=$row->num_items?>" value="<?=$row->id?>"><?=htmlspecialchars($row->name)?></option>
            <?php }?>
					</select>
              <script>


              </script>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
				<label for="file_to_merge" id="merge_field">File to Merge Into </label>
					<select name="file_to_merge_select" id="file_to_merge_select" class="select">
						<option value="-1">Select File...</option>
					</select>
					<input type="hidden" name="file_to_merge" id="file_to_merge" value="" />
					<input style="display:none;" type="text" name="file_to_merge_by_slot" class="input" id="file_to_merge_by_slot">
<img id="spinner" style="margin-top:5px; display:none;" src="<?=site_url()?>img/spinner.gif" />
					<div class="clear"></div>
<div id="merge_by_slot_result" style="display:none; color:darkgreen; text-align:right; margin-right:5px; margin-top:10px; ">Item Number <span id="merge_item_number"></span> is "<span id="merge_item_name"></span>"</div>
<div id="merge_by_slot_pending_transfer" style="display:none; color:red; text-align:right; margin-right:5px; margin-top:10px; ">This item is pending action. Please select another item!</div>
<div id="merge_by_slot_same_item" style="display:none; color:red; text-align:right; margin-right:5px; margin-top:10px; ">You are trying to merge an item into itself. Please select another item to merge into!</div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">Use Action dates to set a reminder to perform a certain action on an item on a given day. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194221&catId=230>Learn More</a></p>					
					<label>Action Date<img width="16" height="16" title="This is the action date of the item" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" name="action_date" id="action_date" class="input"/>
					<div class="clear"></div>
				</div>
				<div class="form_divider radio_list">
				<?php
				$reminder_status=$this->Settings_model->get_all();
				foreach($reminder_status->result() as $row)
				{
					if($reminder_status->num_rows() == 0 || $row->confirmations_value == 1)
					{
						?>
						<p>Should we remind you to merge these files by requiring a confirmation?</p>
						<div><input type="radio" checked name="confirmation" id="confirmYes" value="yes" /><label for="confirmYes" class="radioLabel">Yes, please! (recommended)</label></div>
						<div><input type="radio" name="confirmation" id="confirmNo" value="no" /><label for="confirmNo" class="radioLabel">No thanks, I'll remember!</label></div>
						<?php
					}
					if($row->confirmations_value == 0)
					{
						?>
						<div><input type="radio" name="confirmation" id="confirmYes" value="yes" /><label for="confirmYes" class="radioLabel">Yes, please! (recommended)</label></div>
						<div><input type="radio" checked name="confirmation" id="confirmNo" value="no" /><label for="confirmNo" class="radioLabel">No thanks, I'll remember!</label></div>
						<?php
					}
				}
				if($reminder_status->num_rows() == 0) 
				{
					?>
						<p>Should we remind you to remove this file by requiring a confirmation?</p>
						<div><input type="radio" checked name="confirmation" id="confirmYes" value="yes" /><label for="confirmYes" class="radioLabel">Yes, please! (recommended)</label></div>
						<div><input type="radio" name="confirmation" id="confirmNo" value="no" /><label for="confirmNo" class="radioLabel">No thanks, I'll remember!</label></div>
					<?php

				}
				?>
					<div class="clear"/>
				</div>
				<div class="clear"></div>
			</div>
			<input type="hidden" value="true" name="merge_item_select" />
			<input type="hidden" value="true" name="merge_flag" />
			<input type="hidden" name="merge_time" id="merge_time" value="<?=time()?>"/>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Merge These Files"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
		</form>
	</div>
</div>
