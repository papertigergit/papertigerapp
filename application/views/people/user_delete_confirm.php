<script>
	$('#user_<?=$user->id?>').addClass('toDelete').animate({backgroundColor: 'pink'}, 1000);
</script>
<div id="delete_category_form" class="facebox_form">
		<div id="facebox_header"><div>Deleting User: <?=$user->first_name?> <?=$user->last_name?></div></div>
	<div id="facebox_content">
		<p id="facebox_body">Are you sure you want to delete this user?</p>
		<div id="facebox_footer">
			<form action="<?=site_url()?>people/delete" method="POST"  name="delete_user">
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
							<input type="hidden" value="<?=$user->id?>" name="user_id"/>
						<input type="hidden" value="true" name="delete" />
						<input type="submit" class="font_normal" value="Delete"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
