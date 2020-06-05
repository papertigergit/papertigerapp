<div id="add_item_form" class="facebox_form">
	<div id="facebox_header"><div>Move <?= count($item_ids)>1?"Files":"a File"?></div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">Use the Move function to move an item to another location in your database.</p>
							<?php 
								$this_location = $this->Location_model->get_by_id($this->session->userdata('location_id'), $this->user_data['organization_id']);
							?>
		<form action="<?=site_url()?>items/transfer/move/<?=implode("/",$item_ids)?>" method="POST" class="form" name="move_item">
			<div id="form_padding">
				<div class="form_divider">
					<label for="new_location">New Location</label>
					<select name="new_location" id="new_location" class="select first_input">
						<option value="-1">Select Destination...</option>
						<?php foreach($locations->result() as $row){?>
							<?php if($this->Location_model->get_by_id($row->id)->empty_slots == 0){$is_full=true;}else{$is_full=false;}?>
							<?php if($this->Location_model->get_by_id($row->id)->empty_slots < count($item_ids) && !$is_full){$not_enough_slots=true;}else{$not_enough_slots=false;}?>
							<option value="<?=$row->id?>"<?php if($is_full || $not_enough_slots){echo " disabled";} if(($this_location->name ?? "")==$row->name){echo " disabled";}?>><?=htmlspecialchars($row->name)?><?php if($is_full){echo " (full)";}?><?php if($not_enough_slots){echo " (not enough capacity)";}?></option>
						<?php }?>
					</select>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
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
				if($reminder_status->num_rows() == 0)
				{
						?>
						<p>Should we remind you to move this item by requiring a confirmation?</p>
						<div><input type="radio" checked name="confirmation" id="confirmYes" value="yes" /><label for="confirmYes" class="radioLabel">Yes, please! (recommended)</label></div>
						<div><input type="radio" name="confirmation" id="confirmNo" value="no" /><label for="confirmNo" class="radioLabel">No thanks, I'll remember!</label></div>
						<?php

				}
				foreach($reminder_status->result() as $row)
				{
					if($reminder_status->num_rows == 0 || $row->confirmations_value == 1)
					{
						?>
						<p>Should we remind you to move this item by requiring a confirmation?</p>
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
				?>
					<div class="clear"/>
				</div>
				<div class="clear"></div>
			</div>
			<input type="hidden" value="true" name="move_item" />
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="<?= count($item_ids)>1?"Move These Items":"Move This Item"?>"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
		</form>
	</div>
</div>
