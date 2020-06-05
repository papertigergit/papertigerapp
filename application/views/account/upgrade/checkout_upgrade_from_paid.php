<?php get_instance()->load->helper('url_helper'); ?>
<div id="cancel_account_form" class="facebox_form">
		<div id="facebox_header"><div>Upgrade Account</div></div>
	<div id="facebox_content">
		<p id="facebox_body">Please confirm that the credit card on file for your account should be billed!</p>
		<div id="facebox_footer">
				<div class="right" >
						<div class="small" style="vertical-align:middle; float:left; padding-top:3px;"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </div></span><?=anchor('account/upgrade/process', 'Proceed with upgrade')?>
				</div>
				<div class="clear"></div>
		</div>
	</div>
</div>