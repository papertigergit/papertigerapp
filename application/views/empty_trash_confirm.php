<div id="empty_trash_form" class="facebox_form">
	<div id="facebox_header"><div>Empty Trash</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_body">Are you sure you want to empty the trash?</p>
		<div id="facebox_footer">
			<form action="<?=site_url()?>trash/empty_trash/" method="POST" class="form" name="empty_trash">
			<div class="facebox_password_confirm" style="">
				<p>Enter your password to confirm this action</p>
				
				<div class="input_container">
				
					<input type="password" name="trash_empty_password" id="db_name_field"/>
				
				</div>
				
			</div>					


			<div id="result"></div>
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<?php foreach($trash as $t){ ?>
							<input type="hidden" value="<?=$t['id']?>" name="trash[]"/>
						<?php }?>
						<input type="hidden" value="true" name="empty" />
						<input type="submit" class="font_normal" value="Empty trash"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
