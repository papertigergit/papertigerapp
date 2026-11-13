<head>
<script type="text/javascript" src="https://ajax.microsoft.com/ajax/jQuery/jquery-2.2.1.min.js"></script>
		
<script type="text/javascript">
		$(document).ready(function(){
		$('#sbt').click(function(){
			$("#sbt").submit(function(){
 			//[0] - numer wiersza w tablicy
    if($(this).value === 1)	
    				{
  				alert('Success / Formularz został wysłany');
          // form submit return true
 					return true;
 				}
 
 				else
				{
        // form submit return false
 					return false;
 				}
			});
    	$captcha = $( '#recaptcha' );
      response = grecaptcha.getResponse();

  if (response.length == "") {
    $( '.msg-error').text( "Please Check The Box To Proceed" );
      
    if( !$captcha.hasClass( "error" ) ){
      $captcha.addClass( "error" );
     
      return false;
    }
  } else {
   
   	 return true;
  }
});
 });		</script>
 </head>

<body>
    <div id="top_span">
        <div id="logo">
            Paper Tiger Online - The easiest way to manage your documents!
        </div>
    </div>

    <div id="title_span">
        <h1>Sign up for Paper Tiger in 60 seconds!</h1>
    </div>

    <div id="content">
        <div id="right_column">
            <img id="tiger_head" src="<?=site_url()?>img/signup/tiger_head.png" alt="The Paper Tiger Logo" width="232" height="214" />

            <h2>Thanks for choosing The Paper Tiger!</h2><img id="paper" src="<?=site_url()?>img/signup/paper.png" alt="This is all the clutter that you will eliminate with simple Document Management" width="234" height="117" />
        </div>

        <div id="left_column">
            <h3>1. Create your Paper Tiger Account</h3>
            
            <?php echo validation_errors(); ?>
			
			<?php echo form_open("signup/create/".strtolower($package->name), array('id' => 'signup','autocomplete'=>'off')); ?>

            <table id="name">
                <tr>
                    <td class="name_label"><label for="signup_first_name">First name</label></td>

                    <td class="name_input"><input id="signup_first_name" name="signup[first_name]" size="26" type="text" value="<?php echo set_value('signup[first_name]'); ?>" /></td>
                </tr>

                <tr>
                    <td class="name_label"><label for="signup_last_name">Last name</label></td>

                    <td class="name_input"><input id="signup_last_name" name="signup[last_name]" size="26" type="text" value="<?php echo set_value('signup[last_name]'); ?>" /></td>
                </tr>

                <tr>
                    <td class="name_label c1"><label for="signup_company_name">Company</label></td>

                    <td class="name_input"><input class="c2" id="signup_company_name" name="signup[company_name]" size="26" type="text" value="<?php echo set_value('signup[company_name]'); ?>" /><br />
                    (Or non-profit, organization, group, school, etc.)</td>
                </tr>
                <tr>
                    <td class="name_label c1"><label class="top_label" for="signup_phone_number">Phone Number</label></td>

                    <td class="name_input"><input class="c2" id="signup_phone_number" name="signup[phone_number]" size="30" type="text" value="<?php echo set_value('signup[phone_number]'); ?>" /><br /></td>
                </tr>
                <tr id="select_industry">
                    <td class="name_label c1"><label class="top_label" for="signup_industry">Industry</label></td>
 
                    <td class="name_select" >
                    
                        <select class="c2 industry_dropdown" id="signup_industry" name="signup[industry]" type="text" >
                            <option value="Associations">Associations</option>
                            <option value="Attorneys">Attorneys</option>
                            <option value="Churches/Religious">Churches/Religious</option>
                            <option value="CPA/Bookkeeping">CPA/Bookkeeping</option>
                            <option value="Education">Education</option>
                            <option value="Government">Government</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Medical">Medical</option>
                            <option value="Real Estate">Real Estate</option>
                            <option value="Retail and Wholesale">Retail and Wholesale</option>
                        </select>
                        <span class="industry_toggle">Choose Other Industry</span>
                    </td><br />
                     
                     
                </tr>
                 <tr class="hide_industry" id="other_industry">
                    <td class="name_label c1"><label class="top_label" for="signup_industry">Other Industry</label></td>
 
                    <td class="name_input">
                        <input disabled="disabled" class="c2 industry_other" id="signup_industry" name="signup[industry]" size="30" type="text" value="<?php echo set_value('signup[industry]'); ?>" /> 
                    </td>
                </tr>
                    	


            </table><br />

             <fieldset class="credentials">
                <h3>2. Now choose your login information</h3>

                <div id="user_entry">
                    <p class="field"><label class="top_label" for="signup_email">Email</label><br />
                    <input id="signup_email" name="signup[email]" size="30" type="text" value="<?php echo set_value('signup[email]'); ?>" /><br />
                    You'll use this email address to sign in.</p>

                    <p class="field"><label class="top_label" for="signup_password">Password</label><br />
                    <input id="signup_password" name="signup[password]" size="30" type="password" /><br />
                    6 characters or longer with at least one number is safest.</p>

                    <p class="field"><label class="top_label" for="signup_password_confirmation">Enter your password again for verification</label><br />
                    <input id="signup_password_confirmation" name="password_confirmation" size="30" type="password" /></p>
                    
                           
              

                </div>
            </fieldset>
            <hr />
            

            <h3>3. Enter your Credit Card information &mdash; SECURE</h3>
            
			<?php if( isset($billing_error) ): ?>
            
            <div class="error"><?=$billing_error?></div>
            
            <?php endif; ?>

            <div id="cc_wrapper">
                <div id="credit_card_info">
                    <img src="<?=site_url()?>img/signup/cards.png" alt="We accept Visa, MasterCard, American Express, and Discover." />

                    <p>We accept POs for annual payments. Contact us at <a href="mailto:sales@thepapertiger.com">sales@thepapertiger.com</a> for details.</p>
                </div>
            </div>

            <div id="credit_card_form">
                <table id="cc">
                    <tr>
                        <td class="name_label"><label for="card_number">Card number</label></td>

                        <td class="name_input"><input class="transaction_field" id="card_number" maxlength="16" name="ccnumber" size="22" type="text" /></td>

                        <td><img id="secure" src="<?=site_url()?>img/signup/secure.png" alt="secure" width="27" height="28" /></td>
                    </tr>

                    <tr>
                        <td class="name_label"><label for="card_expires_on_month">Expires on</label></td>

                        <td class="name_input" colspan="2">
                        
            				<?=expire_form_inputs(); ?>
                        
                        </td>
                    </tr>
					
					<tr>
                        <td class="name_label"><label for="card_number">CVV</label></td>
						
                        <td class="name_input"><input class="transaction_field" id="card_cvv" maxlength="4" name="card_cvv" size="4" type="text" /></td>
                    </tr>
                </table>
                <br /><br />
            </div>
            
					<hr />
					
					<div id="billing_information">
					
						<h3>4. Enter your Billing Information</h3>
					
						<label for="billingFirstName">First Name</label>
						<input type="text" value="<?php echo set_value('billingFirstName', $this->user_data['first_name']); ?>" size="30" name="billingFirstName" maxlength="40" id="billingFirstName">
						<br />
					
						<label for="billingLastName">Last Name</label>
						<input type="text" value="<?php echo set_value('billingFirstName', $this->user_data['last_name']); ?>" size="30" name="billingLastName" maxlength="40" id="billingLastName">
						<br />

						<label for="billingStreet">Address 1</label>
						<input id="billingStreet" maxlength="40" name="billingStreet" size="30" type="text" value="<?php echo set_value('billingStreet'); ?>" />
						<br />

						<label for="billingStreet2">Address 2</label>
						<input id="billingStreet2" maxlength="40" name="billingStreet2" size="30" type="text" value="<?php echo set_value('billingStreet2'); ?>" /> <span class="gray">(optional)</span>
						<br />


						<label for="billingCity">City</label>
						<input id="billingCity" maxlength="40" name="billingCity" size="30" type="text" value="<?php echo set_value('billingCity'); ?>" />
						<br />

						<label for="billingState">State/Province</label>
						<input id="billingState" maxlength="40" name="billingState" size="30" type="text" value="<?php echo set_value('billingState'); ?>" />
						<br />
						
						<label for="billingNumber">Phone Number</label>
						<input id="billingNumber" maxlength="40" name="billingNumber" size="30" type="text" value="<?php echo set_value('billingNumber'); ?>" />
						<br />
										
						<label for="billingZip">Billing Zip</label>
						<input class="c2" id="billingZip" maxlength="10" name="billingZip" size="10" type="text" value="<?php echo set_value('billingZip'); ?>" /> <span class="gray">(or Postal Code if not in the USA)</span>
						  
						<br />
						<script>
 var vcc = function(g_recaptcha_response) {
 var $captcha = $( '#recaptcha' );
 
 $( '.msg-error' ).text('');
    $captcha.removeClass( "error" );
 };
</script>
<script src="https://www.google.com/recaptcha/api.js"></script>

 <div id="recaptcha" class="g-recaptcha" data-sitekey="<?= html_escape(config_item('recaptcha_site_key') ?: '6LeS_CAUAAAAALp9-8JIGXK8p8LdoFgJqIyaWAJg') ?>" data-callback="vcc"></div>
<span class="msg-error"></span>

            
					</div>

            <div class="c3"></div>
            <hr />

            

            <p id="instructions">After creating your account, you will be immediately signed into Paper Tiger. You can <strong>cancel</strong> your Paper Tiger account at any time, there are no contracts or cancellation fees. You will be billed <strong>$472</strong> immediately and will be billed again on this date next year.</p>

			<p id="agreement">By clicking <strong>Create my account</strong> you agree to the <a target="_blank" href="http://www.thepapertiger.com/terms_of_service">Terms of Service</a>, and <a target="_blank" href="http://www.thepapertiger.com/privacy_policy">Privacy policies</a>.</p><br />
            <input type="submit" value="Create my account" />
        </div><!-- #left_column -->
    </div><!-- #content -->
</body>
<script type="text/javascript">

function enabler(){


	var industry_select = document.getElementsByClassName("industry_dropdown")[0];
	var industry_other = document.getElementsByClassName("industry_other")[0];
	var industryToggle = document.getElementsByClassName("industry_toggle")[0];
	var newText = "";
	var otherIndustry = document.getElementById("other_industry");
	var selectIndustry = document.getElementById("select_industry");
		
	industryToggle.addEventListener('click', function(e){
		var originalText = this.innerHTML;
		
		if (originalText == "Choose Other Industry"){
			this.innerHTML = "";
			newText = document.createTextNode("Hide Other Industry");
			this.appendChild(newText);
			makeDisable("industry_dropdown");

		} else {
			this.innerHTML = "";
			newText = document.createTextNode("Choose Other Industry");
			this.appendChild(newText);
			makeEnable("industry_dropdown");

		}
		
	}, 'false');

	function makeDisable(toHide){
    	document.getElementsByClassName(toHide)[0].setAttribute('disabled', 'disabled');
    	document.getElementsByClassName("industry_other")[0].removeAttribute('disabled');
    	otherIndustry.classList.remove("hide_industry");
    	otherIndustry.classList.add("show_industry");
	};
	function makeEnable(toHide){
    	document.getElementsByClassName(toHide)[0].removeAttribute('disabled');
    	document.getElementsByClassName("industry_other")[0].setAttribute('disabled', 'disabled');
    	otherIndustry.classList.remove("show_industry");
    	otherIndustry.classList.add("hide_industry");
	};
	    
};

enabler();

</script>
