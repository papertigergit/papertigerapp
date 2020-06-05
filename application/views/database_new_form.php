<div id="update_action_form" class="facebox_form">
	<div id="facebox_header"><div>Create a new database</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">A database is a physical filing cabinet that you use to store your files.</p>
		<form action="<?=site_url()?>databases/add" method="POST" class="form" name="edit_location">
			<div id="form_padding">
				<div class="form_divider">
					<label>Database Name:</label>
					<input type="text" name="name" id="name" class="input" value=""/>
					<div class="clear"></div>
				</div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Create a new database"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
			<input type="hidden" name="edit_location" value="true"/>
		</form>
	</div>
</div>
