<div id="modify_location_capacity_form" class="facebox_form">
	<div id="facebox_header"><div>Add Empty Slots</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<form action="<?=site_url()?>locations/modify_capacity/<?=$this_location->id?>" method="POST" class="form" name="modify_location_capcity">
			<div id="form_padding">
				<div class="form_divider">
					<label for="review">Add Capacity<img width="16" height="16" class="tooltip" title="" src="<?=site_url()?>img/tooltip.png"></label>
					<div class="input">Add <input type="text" size="3" class="first_input inline_input" name="new_capacity" id="new_capacity" /> empty slots</div>
					<div class="clear"></div>
				</div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Save Changes"/></span>
				<span class="small gray"> or <a href="#" class="close red"> Cancel</a></span>
			</div>
			<input type="hidden" name="modify_location_capacity" value="true"/>
		</form>
	</div>
</div>