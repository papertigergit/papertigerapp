<link href="<?=site_url()?>css/account.css" media="screen" rel="stylesheet" type="text/css"/>

<style>

#section {padding-top: 20px;}

#left_col {
    width: 580px;
    float: left;
    padding-bottom:20px;
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

	<div id="table_header_bar">
		<div id="header_bar_left">Paper Tiger Affiliate Program Signup</div>
		<div class="clear"></div>
	</div>
	
	<div id="section">
	
		<div id="left_col">

            <h2>Become a Paper Tiger Affiliate in 60 seconds!</h2>

<p>The Paper Tiger Affiliate program is a great way to make money while helping your friends and co-workers be more organized and productive. Enter the information below and we'll create an affiliate account that is linked to your Paper Tiger account.</p>

<p>From here, you'll be given an affiliate code, links, and banners that you can post on your site/blog/facebook/twitter that will track signups.</p><br />

            <?=form_open('affiliates')?>

                <p>
                    <?php echo validation_errors(); ?>
                </p>

                <div id="affiliate_information">
                        
                    <h3>Please indicate where your affiliate checks should be sent:</h3>
                
                    <label for="affiliateFirstName">First Name</label>
                    <input type="text" id="affiliateFirstName" maxlength="40" name="affiliateFirstName" size="30" value="<?php echo set_value('affiliateFirstName', $this->user_data['first_name']); ?>">
                    <br>
                
                    <label for="affiliateLastName">Last Name</label>
                    <input type="text" id="affiliateLastName" maxlength="40" name="affiliateLastName" size="30" value="<?php echo set_value('affiliateLastName', $this->user_data['last_name']); ?>">
                    <br>

                    <label for="affiliateStreet">Address 1</label>
                    <input type="text" value="" size="30" name="affiliateStreet" maxlength="40" id="affiliateStreet">
                    <br>

                    <label for="affiliateStreet2">Address 2</label>
                    <input type="text" value="" size="30" name="affiliateStreet2" maxlength="40" id="affiliateStreet2"> <span class="gray">(optional)</span>
                    <br>


                    <label for="affiliateCity">City</label>
                    <input type="text" value="" size="30" name="affiliateCity" maxlength="40" id="affiliateCity">
                    <br>

                    <label for="affiliateState">State/Province</label>
                    <input type="text" value="" size="30" name="affiliateState" maxlength="40" id="affiliateState">
                    <br>
                                    
                    <label for="affiliateZip">Zip Code</label>
                    <input type="text" value="" size="10" name="affiliateZip" maxlength="10" id="affiliateZip" class="c2"> <span class="gray">(or Postal Code if not in the USA)</span>
                    <br>
        
                </div>

                <label for="affiliateTax">Tax ID, SSN or VAT</label><input type="text" name="affiliateTax" id="affiliateTax"/> <br/>
                
                <label for="affiliatePaypal">Paypal Email</label><input type="text" name="affiliatePaypal" id="affiliatePaypal"/><span class="gray">(optional)</span>

                <div style="margin-top:10px; padding-top:15px; border-top:solid 2px black;">
                    <input type="submit" value="Become a Paper Tiger Affiliate!"/>
                </div>

            </form>

		</div> <!-- #left_col -->
		
		<div id="right_col">

			<h2>Why be an Affiliate?</h2>
			
			<h3>Make Money!</h3>
	
				<p>Paper Tiger is the easiest filing system you will ever use! You and your business will work more efficiently than ever now that you can find your information.</p>
		
			<hr />
			
			<h3>How much can you earn?</h3>
			
            <p>You get a 15% payout of a plan's value over 12 months if the customer remains on a paid account for 3 months after signing up.</p>

			<span class="price">
                Make 15% of a plan's value over a year!                
			</span>

			<hr />
			
			<h3>How do I get paid?</h3>
			
			<p>We will disburse payments through PayPal. You are responsible for any fees incurred from the transactions.</p>
			
			<hr />			

			<h3>More Questions?</h3>
			
			<p>Please go to our <a href="http://www.thepapertiger.com/affiliates" target="_blank">Affiliates Information Page</a> for details on how the Affiliate Program works.</p>
			
			<hr />			

			<h3>Any questions?</h3>
				
			<p>Chat with us live and let us help you with your decision!</p>
			
			<a id="chat" href="#" onclick="return SnapABug.startLink();">Chat with us now!</img></a>
		
		</div> <!-- #right_col -->
		
		<div class="clear"></div>

	</div> <!-- #section -->
			
</div> <!-- #content_wrapper -->

<script type="text/javascript">
document.write(unescape("%3Cscript src='" + ((document.location.protocol=="https:")?"https:":"http:") + "//snapabug.appspot.com/snapabug.js' type='text/javascript'%3E%3C/script%3E"));</script><script type="text/javascript">
SnapABug.init('40ecda62-72ed-4ea2-8dbf-00ed322a1e70',"0","55%");
SnapABug.allowScreenshot(false);			
</script>
