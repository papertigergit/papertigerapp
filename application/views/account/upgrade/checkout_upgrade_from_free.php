<div id="add_location_form" class="facebox_form">
	<div id="facebox_header"><div>Upgrade Account</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">We don't have your billing information, but we will need it to proceed with the upgrade.</p>
		<form action="<?=site_url()?>account/upgrade/process/" method="POST" class="form" name="upgrade_from_free">
			<div id="form_padding">
				<div class="form_divider">
					<label for="card_number">Credit Card #</label>
					<input id="card_number" maxlength="16" class="inline_input input" name="ccnumber" type="text" />
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<label>Expiration<img width="16" height="16"></label>
                    <?=expire_form_inputs(); ?>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				
				
				
				
					<hr />
					
					<h3>Billing Information</h3>
					
					<label for="billingFirstName">First Name</label>
					<input type="text" size="30" name="billingFirstName" maxlength="40" id="billingFirstName">
					<br />
					
					<label for="billingLastName">Last Name</label>
					<input type="text" size="30" name="billingLastName" maxlength="40" id="billingLastName">
					<br />

					<label for="billingStreet">Address 1</label>
					<input id="billingStreet" maxlength="40" name="billingStreet" size="30" type="text" />
					<br />

					<label for="billingStreet2">Address 2 <span class="gray">(optional)</span></label>
					<input id="billingStreet2" maxlength="40" name="billingStreet2" size="30" type="text" />
					<br />


					<label for="billingCity">City</label>
					<input id="billingCity" maxlength="40" name="billingCity" size="30" type="text" />
					<br />

					<label for="billingState">State</label>
					<input id="billingState" maxlength="40" name="billingState" size="30" type="text" />
					<br />
										
					<label for="billingZip">Billing Zip</label>
					<input class="c2" id="billingZip" maxlength="10" name="billingZip" size="10" type="text" /> <span class="gray">(or Postal Code if not in the USA)</span>
				
				
				
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Process Upgrade"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
		</form>
	</div>
</div>