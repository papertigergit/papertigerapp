<div id="delete_category_form" class="facebox_form">
		<div id="facebox_header"><div>Deleting Database: <?=$database->name?></div></div>
	<div id="facebox_content">
		<p>Are you sure you want to delete this database? <strong>All information</strong> from this database will be <span class="red">IMMEDIATELY</span> deleted, including:</p>
		
		<ul id="facebox_list">
		
			<li>Locations</li>
			<li>Items</li>
			<li>Categories</li>
			<li>Transfers</li>
			<li>Trash</li>
			<li>Database Preferences</li>
		
		</ul>
		
		
		
<!--
		<div id="facebox_footer">
			<form action="<?=site_url()?>databases/do_delete/" method="POST" class="form" name="delete_user">
				<div class="form_divider">
					<h3 style="margin-bottom:10px;">Enter your password to confirm this action</h3>
					<label for="name">Password<img width="16" title="This is your login password." height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="password" class="inline_input input" name="db_delete_password" id="db_name_field"/>
					<div class="clear"></div>
					<div id="result"></div>
				</div>				
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
							
						<input type="hidden" value="true" name="delete" />
						<input type="submit" class="font_normal" value="Delete"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
-->
		
		
		
		<div id="facebox_footer">
			<form action="<?=site_url()?>databases/do_delete/" method="POST" class="form" name="delete_user">
			<div class="facebox_password_confirm">
				<p>Enter your password to confirm this action</p>
				
				<div class="input_container">
				
					<input type="password" name="db_delete_password" id="db_name_field"/>
				
				</div>
				
			</div>					


			<div id="result" style="margin-bottom:20px;"></div>
			
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<input type="hidden" value="true" name="delete" />
						<input type="submit" class="font_normal" value="Delete"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
		
		
		
		
	</div>
</div>
