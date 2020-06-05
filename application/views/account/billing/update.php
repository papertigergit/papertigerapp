<style>

br {
	display:block;
	clear:both;
}

#edit_billing_form h2{
	font-size: 1em;
	margin-bottom:15px;
	margin-left: 10px;
}

#edit_billing_form h3{
	font-size: .8em;
	margin-bottom:5px;
}

#edit_billing input[type=text]{
	height:20px;
	margin-bottom: 10px;
	font-size:1.2em;
}

#edit_billing select{
	height:20px;
	margin-bottom: 10px;
}

#edit_billing label{
	width:110px;
	margin-right:20px;
	margin-left: 15px;
	font-size:.9em;
}

#form_padding {
	font-family: Arial, "MS Trebuchet", sans-serif;
}

span.secure{
	color:green;
}

img#cards{
	display: block;
	margin-left: 145px;
}

hr{
	margin:15px 5px;
}

#edit_billing_form p{
	width:500px;
	display: block;
}

#edit_billing_form #submit_container{
	border-top: 2px solid black;
	margin-top:15px;
}

</style>
<?php get_instance()->load->helper('url_helper'); ?>

<div id="edit_billing_form" class="facebox_form">
	<div id="facebox_header"><div>Change or update your Billing Information</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<form action="<?=site_url().'account/billing/update'?>" method="POST" class="form" name="edit_billing" id="edit_billing">
			<div id="form_padding">

					<h2>Credit Card Information <span class="secure">(SECURE)</span></h2>

					<label for="ccnumber">Credit Card</label>
					<input type="text" name="ccnumber" id="ccnumber"/>
					<br>

					<label for="description">Expiration</label>
					<?=expire_form_inputs(); ?>
					<br>
				
				    <img id="cards" src="<?=site_url()?>img/signup/cards.png" alt="We accept Visa, MasterCard, American Express, and Discover." />
				
					<hr />
					
					<h2>Billing Information</h2>
					
					<label for="billingFirstName">First Name</label>
					<input type="text" size="30" name="billingFirstName" maxlength="40" id="billingFirstName">
					<br />
					
					<label for="billingLastName">Last Name</label>
					<input type="text" size="30" name="billingLastName" maxlength="40" id="billingLastName">
					<br />

					<label for="billingStreet">Address 1</label>
					<input id="billingStreet" maxlength="40" name="billingStreet" size="30" type="text" />
					<br />

					<label for="billingStreet2">Address 2</label>
					<input id="billingStreet2" maxlength="40" name="billingStreet2" size="30" type="text" />
					<br />


					<label for="billingCity">City</label>
					<input id="billingCity" maxlength="40" name="billingCity" size="30" type="text" />
					<br />

					<label for="billingState">State/Province</label>
					<input id="billingState" maxlength="40" name="billingState" size="30" type="text" />
					<br />
										
					<label for="billingZip">Billing Zip</label>
					<input class="c2" id="billingZip" maxlength="10" name="billingZip" size="10" type="text" /> <span class="gray">(or Postal Code if not in the USA)</span>
									
			</div>
			
			<p>Changes to your credit card will be effective immediately. All future charges will be charged to this card.</p>
			
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Update Billing Info"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
		</form>
	</div>
</div>