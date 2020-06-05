<div id="update_action_form" class="facebox_form">
	<div id="facebox_header"><div>Updating Action Date</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">An Action Date is the date that something needs to be completed by. Use these to make sure you never miss an important date!</p>
		<form action="<?=site_url()?>reminders/edit<?php echo $items; ?>" method="POST" class="form" name="edit_location">
			<div id="form_padding">
				<div class="form_divider">
					<label>Action Date<img width="16" height="16" title="This is the action date of the item" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" name="action_date" id="action_date" class="input" value=""/>
					<div class="clear"></div>
				</div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Save Changes"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
			<input type="hidden" name="edit_location" value="true"/>
		</form>
	</div>
</div>