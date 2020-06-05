<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Paper Tiger Online - Login</title>
		<noscript>
			<meta HTTP-EQUIV="REFRESH" content="0; url=<?=site_url()?>noscript.php"> 
		</noscript>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link href="<?=site_url()?>css/reset.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="<?=site_url()?>facebox/facebox.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="<?=site_url()?>css/forgot_password.css" media="screen" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?=site_url()?>js/jquery-1.4.js"></script>
		<script type="text/javascript" src="<?=site_url()?>js/jquery-ui-1.7.2.custom.min.js"></script>
		<script type="text/javascript" src="<?=site_url()?>js/jquery.form.js"></script>
		<script type="text/javascript" src="<?=site_url()?>js/jquery.blockUI.js"></script>
		<script type="text/javascript" src="<?=site_url()?>js/login.js"></script>
	</head>
	<body>
	
	    <div id="top_span">
	        <div id="logo">
	            Paper Tiger Online - The easiest way to manage your documents!
	        </div>
	    </div>	
	
		<div id="main_wrapper_forgot">
			
			<span class="alert"><?php echo validation_errors(); ?></span>
		
			<?php echo form_open('forgot_password'); ?>
				<h3>Can't sign in? Forgot your password?</h3>
				<p>Enter your email address below and we'll send you password reset instructions.</p>
				<div class="wrapper">
					<label for="email_address">Enter your email address:</label>					 
					<input type="text" name="email_address" id="email_address" />
				</div>
				<div class="wrapper">
					<label for="protector">What is two plus seven?:</label>					 
					<input type="text" name="protector" id="protector" />
				</div>
				<input type="submit" name="forgot_password" value="Send me a password reset link" />
			<h4>A note about spam filters</h4>
			<p>If you don't get an email from us within a few minutes please be sure to check your spam filter. The email will be coming from app@thepapertiger.com.</p>
			
			</form>
			
			<p id="login_help"><a href="<?=site_url()?>login">Take me back to the login page.</a></p>
			
		</div>
	</body>
</html>