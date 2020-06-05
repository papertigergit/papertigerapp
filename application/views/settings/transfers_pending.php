<div id="delete_category_form" class="facebox_form">
		<div id="facebox_header"><div>The import for database "<?=$database->name?>" failed!</div></div>
	<div id="facebox_content">
		<p id="facebox_body">Your import for the database "<?=$database->name?>" failed because it has pending transfers. To fix it, read <a href="http://thepapertiger.zendesk.com/entries/158139-how-to-prepare-my-database-for-the-paper-tiger-online/" title="How to prepare my database for The Paper Tiger Online">this Knowledge Base article</a> and try the import again.</p>
		
		<p>If you are still having issues, you can <a href="#" onClick="return SnapABug.startLink();">let us know</a> and we'll help you out!</p><br />
		<div id="facebox_footer">
			<form action="<?=site_url()?>databases/delete_failed/<?=$database->id?>" method="POST" class="form" name="delete_user">
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Go back</a><span class="gray spacing"> or </span></div>
							
						<input type="hidden" value="true" name="delete" />
						<input type="submit" class="font_normal" value="Delete failed import"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
