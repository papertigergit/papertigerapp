<div id="delete_category_form" class="facebox_form">
		<div id="facebox_header"><div>Cancelling Import for database "<?=$database->name?>"</div></div>
	<div id="facebox_content">
		<p id="facebox_body">Are you sure you want to cancel this process?</p>
		<div id="facebox_footer">
			<form action="<?=site_url()?>databases/delete_unimported/<?=$database->id?>" method="POST" class="form" name="delete_user">
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
							
						<input type="hidden" value="true" name="delete" />
						<input type="submit" class="font_normal" value="Cancel import"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
