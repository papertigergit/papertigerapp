<div id="edit_location_form" class="facebox_form">
	<?php get_instance()->load->helper('date_helper'); ?>
	<div id="facebox_header"><div>Editing: <?=character_limiter(htmlspecialchars($this_location->name),50,'...')?></div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">A location is the "container" where you will store your paper. This could be a drawer, a file cabinet, etc.</p>
		<form action="<?=site_url()?>locations/edit/<?=$this_location->id?>" method="POST" class="form" name="edit_location">
			<div id="form_padding">
				<div class="form_divider">
					<p class="tooltip_content">The name of this location. Make it something descriptive and easy to remember! <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=197044&catId=230>Learn More</a></p>					
					<label for="name">Location Name<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="first_input input" name="name" id="name" value="<?=htmlspecialchars($this_location->name)?>"/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
					<div id="validation_name"></div>
				<div class="form_divider">
					<p class="tooltip_content">A description of the location so you can remember what/where it is. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=197044&catId=230>Learn More</a></p>										
					<label for="description">Description<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<textarea type="text" name="description" id="description" class="input"><?=htmlspecialchars($this_location->description)?></textarea>
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">How many items this location will hold. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=197044&catId=230>Learn More</a></p>										
					<label for="capacity">Capacity<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<div class="input"><input type="text" size="6" class="inline_input" name="capacity" id="capacity" value="<?=$this_location->capacity?>"/> empty slots</div>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
                                <div class="form_divider">
										<p class="tooltip_content">All items filed into this location will have an action date based on the review frequency that you select. i.e. a review frequency of 2 weeks will set the action date of an item being filed today to 2 weeks from today. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=192750&catId=230>Learn More</a></p>										
                                        <label for="review">Review Frequency<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
                                        <div class="input">Every <select name="review_interval" id="review_interval" class="inline_select">
					<?php
					$review_interval="Specific Date...";
					$review_range='';
					$new_range='';
					if($this_location->review_interval_num == 0 && $this_location->next_review != 0){
					$review_interval=date("m/d/Y",$this_location->next_review);
					} else if ($this_location->review_interval_num != 0){
						$review_range=$this_location->review_interval_num." ".$this_location->review_interval_string;
					$this_range=explode(' ',$review_range);
					$new_range=$this_range[0]." ".$this_range[1];
					}
					?>
                                                <option value="-1" <?php if($new_range=="") { echo "selected"; }  ?>></option>
                                                <option value="0" <?php if($new_range=="1 weeks") { echo "selected"; }  ?>>1 Week</option>
                                                <option value="1" <?php if($new_range=="2 weeks") { echo "selected"; }  ?>>2 Weeks</option>
                                                <option value="2" <?php if($new_range=="1 months") { echo "selected"; }  ?>>1 Month</option>
                                                <option value="3" <?php if($new_range=="3 months") { echo "selected"; }  ?>>3 Months</option>
                                                <option value="4" <?php if($new_range=="6 months") { echo "selected"; }  ?>>6 Months</option>
                                        </select>
                       					<div style="margin: 0 6px; display:inline;">or</div>  
                                        <input type="text" size="12" name="interval_frequency" value="<?=$review_interval?>" id="action_date" />
                                        </div>
                                        <div class="clear"></div>
                                </div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Save Changes"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
			<?php
			$this_location->last_edited_time;
			$this_location->last_edited_by;
			if($is_network):
				if($is_network->num_rows() > 0):
					if($this_location->last_edited_time):
						if($this_location->last_edited_by):
			?>
			<div class="right">
				<span class="small gray">Last edited by <?=$this->User_model->get_user($this_location->last_edited_by)->row()->first_name?> <?=$this->User_model->get_user($this_location->last_edited_by)->row()->last_name?> on <?=unix_to_human($this_location->last_edited_time)?></span>
			</div>
			<?php 
						endif;
					endif;
				endif;
			endif;	

			?>
			<input type="hidden" name="edit_location" value="true"/>
			<input type="hidden" name="edit_time" value="<?=time()?>"/>
		</form>
	</div>
</div>
