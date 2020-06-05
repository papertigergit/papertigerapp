<script type="text/javascript">
function checkSave(currentName)
{	
	/* this code is required to fix the facebox dialog to close 11/1/2011	*/
	/*
	edit_item = document.getElementById('edit_form');

	var new_changes= edit_item.elements["name"].value;
	if(new_changes==currentName) {
		new_changes='';
		$(document).trigger('close.facebox');
	} else {
		new_changes='';
		alert("You have unsaved changes. Either save or discard them before proceeding.");
	}
	*/
}
function verifyCategory(){
	var cat_id = document.edit_item.category.selectedIndex;
	var selected_text = document.edit_item.category.options[cat_id].text;
	if(selected_text == 'Add New Category...')
	{	$('#add_category').show();
		$('#existing_category').hide();
		$('#new_category').focus();							
	}
}
$(function(){
	
	var itemNameVal = $('#name').val();
	var keyWordVal = $('#keywords').val();
	var notesVal = $('#notes').val();
	var actionDateVal = $('#action_date').val();
	var category = $("#category").val();
	var notesVal = $('#notes').val();
	$('#closeForm').click(function (){
		if( itemNameVal != $('#name').val() || keyWordVal != $('#keywords').val() || actionDateVal !=  $('#action_date').val() || category != $("#category").val() || $('#notes').val() != notesVal  ){
			alert("You have unsaved changes. Either save or discard them before proceeding.");
		}else{
			$(document).trigger('close.facebox');
		}
	});
	// add multiple items wihile diting 
	// close add item link click
	$("#close_add_category").click(
		function () {
			$('#existing_category').show();								
			$('#add_category').hide();
			$("#category").val('0');
			var saved_valude = $("#new_category").val();
			$("#new_category").val('');
			$('#category').focus();
		}
	); 

	// add multiple items wihile diting 
	// add category link click
	$('#add_category_link').click(
		function(){
			if($('#new_category').val() != ''){
				var postVar = {'new_category':$('#new_category').val()};
				$.ajax({
				  url: "<?php echo site_url();?>items/edit_add_new_category/",
				  cache: false,
				  data: postVar,
				  type:'POST',
				  dataType:'json',
				  success: function(data){
					if( data.message != undefined && data.message_type != undefined) {
						$('#flash').remove();
						$('body').prepend('<div id="flash" class="flash_' + data.message_type + '"><p>' + data.message + '</p><a href="#" id="flash_hide" class="small" style="float:right; margin-top:-10px; margin-right:10px;">Hide this</a></div>');	
						$('#existing_category').show();	
						$('#category option').removeAttr('selected');
						$('#category').prepend('<option selected="true" value="'+data.new_category_id+'">'+data.new_category_name+'</option>');
						$('#add_category').hide();
						$("#new_category").val('');
					} else {
						$('#flash').remove();	
						alert("An error occured,please try again");
					}			
		
				/*

					if(data=="session_expired")
					{
					//	$("#database_list").html("Out");
					//	$('a[rel*=facebox]').unbind('click');				
					//	$('a[rel*=facebox]').facebox();
					}
					else
					{
						$("#database_list").html(html);
						$('a[rel*=facebox]').unbind('click');				
						$('a[rel*=facebox]').facebox();
					}	*/
				  }
				})
			}else{
				alert('Enter category name');
				$("#new_category").focus();
			}
		}
	);
    			

});

</script>
<style>

span.added {
    background: lightgreen;
}

del {
    background: pink;
}

a.active {
background:none repeat scroll 0 0 gray;
color:white !important;
padding:5px;
}

#collision_selector{
    display:block;
    width:540px;
    margin: 0 auto;
    margin-bottom:5px;
    display:none;
    border-bottom:3px solid black;
    padding-bottom:15px;
}

#collision_selector li{
    float:left;
    width: 135px;
    text-align:center;
}

#original_data{
    display:none;
}

p.collision_tooltip{
    display:none;
}

a.active:hover{
    color: red;
}
</style>

<?php
		$show_notes=$this->Settings_model->get_all();
		$notes_settings=array($show_notes->result_array());
		get_instance()->load->helper(['text_helper','date_helper']);
?>
<div id="edit_item_form" class="facebox_form">
	<div id="facebox_header"><div><?=$multiple_items?"Editing Multiple Items":"Editing Item On ".character_limiter(htmlspecialchars($location_name),37,'...')." ".$this_item->slot_num?></div><a href="#" id="closeForm"  onClick="checkSave('<?php echo $this_item->name??"" ?>'); return false"><img src="<?=site_url()?>img/close_button.gif" alt="close"/></a></div>
	<div id="facebox_content">
		<p id="facebox_intro"><?=$multiple_items?"You can add, remove, or modify the category or action date for multiple items here.":"You can add or remove information, make changes, and set a new action date here."?></p>
		<?php
		if(!isset($this_item)) {
			$last_item=(end($items));
		} else {
			$last_item=($this_item->id);
		}
		?>
		<form action="<?=site_url()?>items/edit/<?=$order_by?>/<?=$order?>/<?=$last_item?>" method="POST" class="form" name="edit_item" id="edit_form">
            
            <?php //Now start the actual item ?> 
			<div id="form_padding">
				<div class="form_divider">
					<p class="tooltip_content">This is the name of the item. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a></p>					
					<label for="name">Item Name<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text"<?=$multiple_items?" disabled":""?> class="input<?=$multiple_items?" disabled_input":" first_input"?>" name="name" id="name" <?= $multiple_items?"":'value="'.htmlspecialchars($this_item->name).'"'?> <?=$multiple_items?"DISABLED":""?>/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">Keywords are used to describe the item. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a></p>					
					<label for="keywords">Keywords<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<?php $ff_keywords = isset($ff_keywords)?' '.$ff_keywords:''?>
					<textarea type="text" name="keywords" id="keywords"<?=$multiple_items?" disabled":""?> class="input<?=$multiple_items?" disabled_input":""?>"><?=$multiple_items?"":$this_item->keywords.$ff_keywords?></textarea>
					<div class="clear"></div>
				</div>
				<?php
				if($show_notes->num_rows() > 0) {
					if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
					{
						?>
						<div class="form_divider">
							<p class="tooltip_content">Notes can be added to an item to remember something specific in a contextual manner. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a></p>					
							<label for="notes">Notes<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
							<?php $ff_notes = isset($ff_notes)?' '.$ff_notes:''?>
							<textarea type="text" name="notes" id="notes"<?=$multiple_items?" disabled":""?> class="input<?=$multiple_items?" disabled_input":""?>"><?=$multiple_items?"":$this_item->description.$ff_notes?></textarea>
							<div class="clear"></div>
						</div>
						<?php
					}
				}
				?>
				<div class="form_divider" id='existing_category'>
					<p class="tooltip_content">Use categories to group items without having them in the same physical location. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194212&catId=230>Learn More</a></p>					
					<label for="category">Category<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<select onchange="verifyCategory()" name="category" id="category" class="select<?=$multiple_items?" first_input":""?>">
						<?=$multiple_items?'<option value="-1" SELECTED>Choose A New Category</option>':''?>
						<?php foreach ($categories->result() as $row) { ?>
						<option value="<?=$row->id?>"<?=(!$multiple_items&&$row->id==$this_item->category_id)?" SELECTED":""?>><?=htmlspecialchars($row->name)?></option>
						<?php } ?>
						<option value="0"<?=(!$multiple_items&&0==$this_item->category_id)?" SELECTED":""?>>No Category</option>
						<option value="#">Add New Category...</option>
					</select>
					<div class="clear"></div>
				</div>
				<div class="form_divider" id="add_category" style="display:none;">
					<p class="tooltip_content">Select or Add a Category.  Categories allow you to easily see all "Like" things together no matter how they are stored physically. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194212&catId=230>Learn More</a></p>
					<label for="new_category">Category<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="input" name="new_category" id="new_category"/>
					<div class="clear"></div>
					<a href="##" class="small red" id="close_add_category" style="float:right; margin-top:5px;">Cancel adding category</a>
					<a href="##" class="small red" id="add_category_link" style="float:right; margin-top:5px;margin-right:15px;">Add category</a>
					<div class="clear"></div>
				</div>
				
				<div class="form_divider">
					<p class="tooltip_content">Use Action dates to set a reminder to perform a certain action on an item on a given day. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194221&catId=230>Learn More</a></p>					
					<label>Action Date<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" name="action_date" id="action_date" class="input" value="<?=($multiple_items||$this_item->action_date==null)?'':date("m/d/Y",$this_item->action_date)?>"/>
					<div class="clear"></div>
				</div>
            </div>

			<div id="result"></div>
            <ul id="collision_selector">
                <h3 style="margin-bottom:15px;">Select one:</h3>
                <li><a class="collision_link" id="collision_original" href="#">Original Item</a><p class="collision_tooltip">This is the original item information that you started to edit. Use this information if you want to revert the changes both <em>you and <span class="collision_edited_by"></span></em> made.</p></li>
                <li><a class="collision_link" id="collision_new" href="#">Their Changes</a><p class="collision_tooltip">This is the item information that was just saved by <span class="collision_edited_by"></span> right before you finished editing this item. Use this information if you want to use <span class="collision_edited_by"></span>'s changes.</p></li>
                <li><a class="collision_link" id="collision_yours" href="#">Your Changes</a><p class="collision_tooltip">These are the changes to the item information that you entered. Use this if you want to <em>overwrite</em> <span class="collision_edited_by"></span>'s changes.</p></li>
                <li><a class="collision_link" id="collision_merged" href="#">Merged Changes</a><p class="collision_tooltip">This is a merge of your changes along with <span class="collision_edited_by"></span>'s changes. Use this information if you want to combine both yours and <span class="collision_edited_by"></span>'s changes. NOTE: The category and action date <strong>you</strong> input will be used!</p></li>
                <div class="clear"></div>
                <h4 style="margin-top:15px; color:gray; font-size:.8em;">Don't worry, you can browse through each of them before you make a decision on which information you want to use. You can also use the information that is closest to your needs and edit it.</h4>
                <div class="clear"></div>
            </ul>
            <div id="original_data">
                <p id="original_item_name"><?=urlencode($this_item->name)?></p>
                <p id="original_keywords"><?=urlencode($this_item->keywords)?></p>
                <p id="original_notes"><?=urlencode($this_item->description)?></p>
                <p id="original_category"><?php if($this_item->category_id == NULL):?>0<?php else:?><?=urlencode($this_item->category_id)?><?php endif;?></p>
                <p id="original_action_date"><?php if($this_item->action_date == NULL):?><?php else:?><?=urlencode(date("m/d/Y",$this_item->action_date))?><?php endif;?></p>
            </div>
            <div class="clear"></div>
			<div id="submit_container">
				<input type="submit" name="submit" id="submit" value="Save Changes">
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
			<?php
			if(!$multiple_items):
			$this_item->last_edited_time;
			$this_item->last_edited_by;
			if($is_network):
				if($is_network->num_rows() > 0):
					if($this_item->last_edited_time):
						if($this_item->last_edited_by):
			?>
			<div class="right">
				<span class="small gray">Last edited by <?=$this->User_model->get_user($this_item->last_edited_by)->row()->first_name?> <?=$this->User_model->get_user($this_item->last_edited_by)->row()->last_name?> on <?=unix_to_human($this_item->last_edited_time)?></span>
			</div>
			<?php 
						endif;
					endif;
				endif;
			endif;	
			endif;
			?>
			<?php foreach($items as $item){ ?>
				<input type="hidden" value="<?=$item?>" name="items[]"/>
			<?php }?>
			<input type="hidden" name="edit_item" value="true"/>
			<input type="hidden" name="edit_time" id="edit_time" value="<?=$last_edit_time?>"/>
		</form>
	</div>
</div>
