<div id="cancel_account_form" class="facebox_form">
		<div id="facebox_header"><div>Cancel Account</div></div>
	<div id="facebox_content">
		<p id="facebox_body">Are you sure you want to permanently cancel your account? <br/><br/>Please ensure you have a full backup of your account as all data will be deleted upon confirmation.</p>
				<div id="facebox_footer">
				<form action="<?=site_url()?>account/cancel/confirm" method="POST" class="form" name="cancel_account">
			<div class="facebox_password_confirm">
				<p>Enter your password to confirm this action</p>
				
				<div class="input_container">
				
					<input type="password" name="cancel_account_password" id="cancel_account_password"/>
				
				</div>
				
			</div>					
			<div id="result"></div>
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<input type="hidden" value="true" name="cancel" />
						</span><input type="submit" class="font_normal" value="Proceed to cancel account"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
