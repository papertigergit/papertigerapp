<div id="add_location_form" class="facebox_form">
	<div id="facebox_header"><div>Add A New Location</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">A Location is where you store physical things such as paper. If you are using Paper Tiger for paper filing, enter the name of your filing cabinet. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=197044&catId=230>Learn More</a> </p>
		<form action="<?=site_url()?>locations/add" method="POST" class="form" name="add_location">
			<div id="form_padding">
				<div class="form_divider">
					<p class="tooltip_content">The name of this location. Make it short but descriptive (example: Mary's Gray File Cabinet). <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=197044&catId=230>Learn More</a></p>										
					<label for="name">Location Name<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="first_input input" name="name" id="name"/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">A description of the location so you can remember what/where it is. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=197044&catId=230>Learn More</a></p>										
					<label for="description">Description<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<textarea type="text" name="description" id="description" class="input"/>
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">How many items this location will hold. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=197044&catId=230>Learn More</a></p>										
					<label for="capacity">Capacity<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<div class="input"><input type="text" size="3" class="inline_input" name="capacity" id="capacity" /> items</div>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">All items filed into this location will have an action date based on the review frequency that you select. i.e. a review frequency of 2 weeks will set the action date of an item being filed today to 2 weeks from today. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=192750&catId=230>Learn More</a></p>										
					<label for="review">Review Frequency<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<div class="input">Every <select name="review_interval" id="review_interval" class="inline_select">
						<option value="-1"></option>
						<option value="0">1 Week</option>
						<option value="1">2 Weeks</option>
						<option value="2">1 Month</option>
						<option value="3">3 Months</option>
						<option value="4">6 Months</option>
					</select>
<!-- 					<br /> -->
						<div style="margin: 0 0px 0 6px; display:inline;"><span style="font-weight:bold; margin-right:4px; color:gray;">OR</span> on</div><input  type="text" size="11" name="interval_frequency" id="action_date"  style="font-size:.9em;"/>
									
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Add This Location"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
		</form>
	</div>
</div>
