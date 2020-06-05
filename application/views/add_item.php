<?php
		$show_notes=$this->Settings_model->get_all();
		$notes_settings=array($show_notes->result_array());
		get_instance()->load->helper('text_helper');
?>
<div id="add_item_form" class="facebox_form">
    <div id="facebox_header"><div>Add A New Item to <?=character_limiter(htmlspecialchars($this_location->name),37,'...')?> <span id="next_avail_slot"><?=$position?></span></div><a href="#" onClick="checkSave(); return false"><img src="<?=site_url()?>img/close_button.gif" alt="close"/></a></div>
	<div id="facebox_content">
			<p id="facebox_intro">An Item is what goes in a Location. (example: if you are using Paper Tiger for paper filing, an Item is a hanging file folder.) <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a>.
		</p>
		<form action="<?=site_url()?>items/add/<?=$this_location->id?>/<?=$position?>/<?=$order_by?>/<?=$order?>" method="POST" class="form" name="add_item" id="add_item_form">

			<div id="form_padding" class="find_first">
			<?php
			global $user_data;
			$find_first_text='';
			$find_first_flag='';
			$ff_merge='';
			      $packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']));
			      $package=(array($packages->result()));
			      if(($packages->num_rows() == 0)  || ($package[0][0]->find_first == 0)) {
					$find_first_text='';
					$find_first_flag='';
					$ff_merge='';
			      }
			      if($package[0][0]->find_first == 1) {
					$find_first_text="find_first_text";
					$find_first_flag="find_first_flag";
					$ff_merge="ff_merge";
			      }
			?>
				<div id="find_first"><span id="<?=$find_first_text?>"></span><input type="hidden" id="<?=$find_first_flag?>" name="find_first_flag" value="false"/><a id="<?=$ff_merge?>" href="#"></a></div>
				<div class="clear"></div>

				<div class="form_divider">
					<p class="tooltip_content">
					Enter Item Name.  The Item name should be short & description (similar to what you might have written on a file folder tab in an alphabetic system in the past). <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a></p>
					<label for="name">Item Name<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="first_input input new_item_name" name="name" id="name"/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">Enter Keywords.  You can enter as many keywords to describe this Item as you want.  Be as descriptive as possible so that you can easily find the Item later.  You, or anyone else, can always come back and enter additional keywords. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a></p>
					<label for="keywords">Keywords<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<textarea type="text" name="keywords" id="keywords" class="input"/>
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
							<textarea type="text" name="notes" id="notes" class="input"/>
							<div class="clear"></div>
						</div>
						<?php
					}
				}
				?>
				<div class="form_divider" id="existing_category">
					<?=$this->session->set_userdata('new_category_id','')?>
					<p class="tooltip_content">Select or Add a Category.  Categories allow you to easily see all "Like" things together no matter how they are stored physically. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194212&catId=230>Learn More</a></p>
					<label for="category">Category<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<select name="category" id="category" class="select" onchange="verifyCategory()">
						<option value="0">Select A Category...</option>
						<?php foreach ($categories->result() as $row) { ?>
						<option value="<?=$row->id?>"><?=character_limiter(htmlspecialchars($row->name),18,'...')?></option>
						<?php } ?>
						<option value="#">Add New Category...</option>
					</select>
					<div class="clear"></div>
					<script type="text/javascript">
					function verifyCategory()
					{
						var cat_id = document.add_item.category.selectedIndex;
						var selected_text = document.add_item.category.options[cat_id].text;
						if(selected_text == 'Add New Category...')
						{
							//alert(selected_text);
							$('#add_category').show();
							$('#existing_category').hide();
							$('#new_category').focus();							
						}
					}
					
					function checkSave()
					{ 
						
						var new_changes=document.add_item.elements["name"].value;
						if(new_changes=='') {
							new_changes='';
							$(document).trigger('close.facebox');
						} else {
							new_changes='';
							alert("You have unsaved changes. Either save or discard them before proceeding.");
						}
					}

					$(document).ready(function() {					
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
    				});
    
					
					</script>
				</div>
				<div class="form_divider" id="add_category" style="display:none;">
					<p class="tooltip_content">Select or Add a Category.  Categories allow you to easily see all "Like" things together no matter how they are stored physically. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194212&catId=230>Learn More</a></p>
					<label for="new_category">Category<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="input" name="new_category" id="new_category"/>
					<div class="clear"></div>
					<a href="#" class="small red" id="close_add_category" style="float:right; margin-top:5px;">Cancel adding category</a></span>
					<div class="clear"></div>
				</div>
				<?php
                               $review_action_date='';
                                if($this_location->next_review == 0 && $this_location->review_interval_string == 0)
                                {   
                                        $default_review_date='';
                                }   
                                else if($this_location->next_review != 0 && $this_location->review_interval_string == 0)
                                {   
					$default_review_date=date("m/d/Y",$this_location->next_review);
                                }   
                                else if($this_location->next_review == 0 && $this_location->review_interval_string != 0)
				{
					$default_review_date=date("m/d/Y",strtotime('+'.$this_location->review_interval_number." ".$this_location->review_interval_string));
				}

				if($this_location->next_review > 0)
				{
					$default_review_date=date("m/d/Y",$this_location->next_review);
					if($this_location->next_review < time())
					{
						$review_action_date="<a id=\"expired_review_freq\" href=".site_url()."locations/edit/".$this_location->id." class=\"red\" rel=\"facebox_fb modal_fb\">Click to update.</a>";
					}
				}
				?>
				<div class="form_divider">
					<p class="tooltip_content">Set an Action Date.  Paper Tiger will notify you that this Item needs attention on the Action Date you set. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194221&catId=230>Learn More</a></p>
					<label>Action Date<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" name="action_date" value="<?=$default_review_date?>" id="action_date" class="input"/>
					<div class="clear"></div>
				
				<?php if($review_action_date) {?>	
					<div id="review_freq_expired" style="padding-top:10px; color:red;">
						<div style="margin-left:160px; font-size:.8em; display:block;">Review Frequency is expired! <?=$review_action_date?></div>
					</div>
				<?php }?>
				</div>				
			</div>
			<div id="result"></div>
			<div id="submit_container">
            <span><input type="submit" name="submit" id="submit" value="Add to <?=character_limiter(htmlspecialchars($this_location->name),37,'...')?> <?=$position?>"/></span>

<script type="text/javascript" src="<?=site_url()?>js/jquery.timers-1.2.0.js"></script> 
<script type="text/javascript">
// Collision Detection //
$(document).ready(function(){
  $("#result").everyTime(300,function(i){
    var self = $(this);
    var position_val = $("input#position").val();
    url = $("form#add_item_form.form").attr("action");
    url_array = url.split("/"), url_len = url_array.length;
    url_len_slot = url_len - 3, url_array[url_len_slot] = position_val;
 
    $.each(url_array, function(idx2) {                    
      var str = idx2 + "";
      url_array.push(str);
      url_array.pop();
    });
 
    $('div#submit_container span input#submit').removeAttr("disabled");
    $("div#db_list").html($("input#position").val());
    collision_url = url_array.join("/"); 
    $("form#add_item_form.form").attr("action", collision_url);
  });
});
// End Collision Detection //
</script>
				<span class="small gray"><span>or</span><a href="#" class="close red" id="cancel_no_save">Cancel</a></span>
			</div>
			
			<?php if( $position !== FALSE ): ?>
				<input type="hidden" name="position" id="position" value="<?=$position;?>"/>
			<?php endif; ?>
		</form>
	</div>
</div>
