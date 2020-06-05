<link href="<?=site_url()?>css/account.css" media="screen" rel="stylesheet" type="text/css"/>

<style>

#section {padding-top: 20px;}

#left_col {
	width: 580px;
	float: left;
	height: 1000px;
/* 	border: solid 1px gray; */
}

#left_col h2 {
	font-size: 1.8em;
	margin: 0;
	padding: 0;
	margin-bottom: 20px;
	border-bottom: solid 1px gray;
	padding-bottom: 5px;
}

#left_col h2.bottom {
	margin-top: 20px;
	border-top: solid 3px black;
	padding-top: 40px;
}

#left_col h3 {
	margin-bottom: 20px;
}

#left_col label {
	width: 175px;
	display: inline-block;
	margin-bottom: 20px;
	font-family: Arial;
	color: #686868;		
	font-size: .9em;	
}

#left_col input[type=text] {
	height: 20px;
	font-size: 1em;
}

#left_col input[type=radio] {
/* 	height: 20px; */
	font-size: 1em;
/* 	margin-top: 10px; */
	margin-right: 5px;
}

#left_col select{
	margin-right: 10px;
	font-size: 1em;
}

#right_col {
	border: solid 1px gray;
	width: 230px;
	float: right;
	padding: 20px;	
}

#right_col hr {
}

#right_col h2 {
	font-size: 1.5em;
	margin: 0;
	padding: 0;
	margin-bottom: 20px;
}

#right_col h3 {
	font-size: 1.2em;
	margin-bottom: 10px;
}

#left_col h3.first {
	margin-top: 20px;
}

#right_col p, #left_col p{
	font-family: Arial;
	margin-bottom: 10px;	
	color: #686868;		
	font-size: .9em;
	line-height: 1em;
}

#right_col span.price {
	color: green;
	font-family: "Trebuchet MS";
	font-size: 1.4em;
}

#right_col ul {
	list-style: disc;
	margin-left: 25px;
	list-style-image: url(<?=site_url()?>img/clear.png);
}

#right_col li {
	font-family: Arial;
	margin-bottom: 10px;	
	color: #686868;		
	font-size: .9em;

}

#right_col img {
	margin-bottom: 10px;
}

#left_col #errors p{
	color:red;
	font-weight: bold;
	font-size: 1.2em;
}

#chat {
	display: block;
	margin: 0 auto;
}

</style>

<div id="content_wrapper">
<?php
$plan=$packages_by_name->result_array();
$plan_arr=$plan[0];
echo "<pre>";
echo "</pre>";
?>

	<div id="table_header_bar">
		<div id="header_bar_left">Paper Tiger <?=$plan_arr['plan_description']?></div>
		<div class="clear"></div>
	</div>
	
	<div id="section">
	
		<div id="left_col">
	
			<form action="<?=site_url().'account/billing/create/'.$selected_plan?>" method="POST" name="create_billing">
				        
				<div id="errors"><?php echo validation_errors(); ?></div>
				    
				<?php if( isset($billing_error) ): ?>
				    
				<div class="error"><?=$billing_error?></div>
				    
				<?php endif; ?> 
					                
					<h2>Add your credit card information to continue!</h2>
					
					<p>The best filing system in the world...so simple & easy! Enter your billing information to continue using Paper Tiger with full functionality.</p>
					
					<h3 class="first">Credit Card Information (SECURE)</h3>
										
					<label for="card_number">Card number</label>	
					<input class="transaction_field" id="card_number" maxlength="16" name="ccnumber" size="30" type="text" />
<!-- 					<img id="secure" src="<?=site_url()?>img/signup/secure.png" alt="secure" width="27" height="28" />	 -->
					<br />

					<label for="card_cvv">Security code</label>	
					<input class="transaction_field" id="card_cvv" maxlength="4" name="card_cvv" size="10" type="text" />
					<input type="hidden" name="selected_plan" value="<?=$selected_plan?>" />
					<br />
					
					<label for="card_expires_on_month">Expires on</label></td><?=expire_form_inputs(); ?>
					<br />

					<hr />
					
					<h3>Billing Information</h3>
					
					<label for="billingFirstName">First Name</label>
					<input type="text" value="<?php echo set_value('billingFirstName', $this->user_data['first_name']); ?>" size="30" name="billingFirstName" maxlength="40" id="billingFirstName">
					<br />
					
					<label for="billingLastName">Last Name</label>
					<input type="text" value="<?php echo set_value('billingFirstName', $this->user_data['last_name']); ?>" size="30" name="billingLastName" maxlength="40" id="billingLastName">
					<br />

					<label for="billingStreet">Address 1</label>
					<input id="billingStreet" maxlength="40" name="billingStreet" size="30" type="text" value="<?php echo set_value('billingStreet'); ?>" />
					<br />

					<label for="billingStreet2">Address 2 <span class="gray">(optional)</span></label>
					<input id="billingStreet2" maxlength="40" name="billingStreet2" size="30" type="text" value="<?php echo set_value('billingStreet2'); ?>" />
					<br />


					<label for="billingCity">City</label>
					<input id="billingCity" maxlength="40" name="billingCity" size="30" type="text" value="<?php echo set_value('billingCity'); ?>" />
					<br />

					<label for="billingState">State/Province</label>
					<input id="billingState" maxlength="40" name="billingState" size="30" type="text" value="<?php echo set_value('billingState'); ?>" />
					<br />
										
					<label for="billingZip">Billing Zip</label>
					<input class="c2" id="billingZip" maxlength="10" name="billingZip" size="10" type="text" value="<?php echo set_value('billingZip'); ?>" /> <span class="gray">(or Postal Code if not in the USA)</span>
					<br />
					
				<tr>
                    <td class="name_label c1"><label class="top_label" for="signup_phone_number">Phone Number</label></td>
 
                    <td class="name_input"><input class="c2" id="signup_phone_number" name="signup[phone_number]" size="30" type="text" value="<?php echo set_value('signup[phone_number]'); ?>" /><br /></td>
                </tr>
                <tr id="select_industry">
                    <td class="name_label c1"><label class="top_label" for="signup_industry">Industry</label></td>
 
                    <td class="name_select" >
                   
                    	<select class="c2 industry_dropdown" id="signup_industry" name="signup[industry]" type="text" >
							<option value="Associations" <?php echo set_select('signup[industry]', 'associations'); ?> >Associations</option>
							<option value="Attorneys" <?php echo set_select('signup[industry]', 'attorneys'); ?> >Attorneys</option>
							<option value="Churches/Religious" <?php echo set_select('signup[industry]', 'churches'); ?> >Churches/Religious</option>
							<option value="CPA/Bookkeeping" <?php echo set_select('signup[industry]', 'bookkeeping'); ?> >CPA/Bookkeeping</option>
							<option value="Education" <?php echo set_select('signup[industry]', 'education'); ?> >Education</option>
							<option value="Government" <?php echo set_select('signup[industry]', 'government'); ?> >Government</option>
							<option value="Insurance" <?php echo set_select('signup[industry]', 'insurance'); ?> >Insurance</option>
							<option value="Medical" <?php echo set_select('signup[industry]', 'medical'); ?> >Medical</option>
							<option value="Real Estate" <?php echo set_select('signup[industry]', 'realestate'); ?> >Real Estate</option>
							<option value="Retail and Wholesale" <?php echo set_select('signup[industry]', 'retail'); ?> >Retail and Wholesale</option>
							<option value="Other" <?php echo set_select('signup[industry]', 'other'); ?> >Other</option>
						</select>
						<span class="industry_toggle" type="button">Choose Other Industry</span>
                    </td><br />
                    
                    
                </tr>
                 <tr class="hide_industry" id="other_industry">
                    <td class="name_label c1"><label class="top_label" for="signup_phone_number">Other Industry</label></td>
 
                    <td class="name_input">
                    	<input class="c2 industry_other" id="signup_industry" name="signup[industry]" size="30" type="text" value="<?php echo set_value('signup[industry]'); ?>" /> 
                    </td>
                </tr>

            </table><br />
                  
                  <br />

					<hr />
					
					<h3>Choose your plan</h3>
					
					<label for="subscriptionType">Billing Plan</label>

						<?php
						$billing_group=$plan_arr['groups'];
						$group=$this->Package_model->get_by_groups($billing_group);
						$this_plan=$this->Package_model->get_by_period($selected_plan);
						foreach($group->result() as $bill_group) {
							$selected='';
							if (strtolower($bill_group->name) == strtolower($selected_plan)) {
								$selected="checked";
							}

									$trailer='';
							if($bill_group->id == 1 || $bill_group->id == 2){
								if($bill_group->billing_cycle=="month"){
                                    $cycle=$bill_group->name;
								}
								if($bill_group->billing_cycle=="year"){
                                    $cycle=$bill_group->name;
									$trailer=" <span style=\"color:green;\">(2 months free!)</span>";
								}
							} else {
								$cycle=$bill_group->name;
								if($bill_group->billing_cycle=="year"){
									$trailer=" <span style=\"color:green;\">(2 months free!)</span>";
								}
							}

						?>
							<input type="radio" name="subscriptionType" <?=$selected?> value="<?=$cycle?>">$<?=$bill_group->sales_price?>/<?=$bill_group->billing_cycle?><?=$trailer?>		
						<?php } ?> 
					<br />					
					
					<input type="submit" value="Create my subscription" />				
			
			</form>			
			
		</div> <!-- #left_col -->
		
		<div id="right_col">

			<h2>Why use Paper Tiger?</h2>
			
			<h3>The best filing system ever</h3>
	
				<p>Paper Tiger is the easiest filing system you will ever use! You and your business will work more efficiently than ever now that you can find your information.</p>
		
			<hr />
			
			<h3>How much?</h3>
			
			<p>Paper Tiger is affordable:</p>
			<span class="price">
			<?php
			$billing_group=$plan_arr['groups'];
			$group=$this->Package_model->get_by_groups($billing_group);
			foreach($group->result() as $bill_group) {
			?>
				<?php
				echo "$".$bill_group->sales_price."/".$bill_group->billing_cycle;
				?>
			<?php } ?> 
			</span>

			<hr />
			
			<h3>Satisfaction Guaranteee</h3>
			
			<p>If you're not satisfied with Paper Tiger, you can cancel at any time. There are no cancellation fees or hidden charges. You are responsible for any charges incurred up until the time you cancel.</p>
			
			<hr />

			<h3>We accept</h3>
		
			<img src="<?=site_url()?>img/signup/cards.png" alt="We accept Visa, MasterCard, American Express, and Discover." />
				
			<p>We don't accept POs, checks, or invoices to be paid at a later date. We will email you a receipt each time your card is charged.</p>			

			<hr />

			<h3>Need a Purchase Order?</h3>
				
			<p>We accept purchase orders but only if you choose the yearly plan. <a href="mailto:sales@thepapertiger.com">Contact us</a> for details.</p>			

			<hr />			

              <h3>Any questions?</h3>
				
			<p>Email us now and allow us to help you with your decision!</p>
			
			<a href="mailto:sales@thepapertiger.com">Email us now!</img></a>
		
		</div> <!-- #right_col -->
		
		<div class="clear"></div>

	</div> <!-- #section -->
			
</div> <!-- #content_wrapper -->

<!-- begin SnapEngage code -->
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
