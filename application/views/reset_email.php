<html lang="en-US">
	<head>
		<title>Paper Tiger Online - Reset email address</title>
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
	
		<div id="main_wrapper">
			
			<span class="alert"><?php echo validation_errors(); ?></span>
		
				<?php echo form_open("account/reset_email/reset/".$authorization_code); ?>
				<h3>Change your email address</h3>
				<p>You can confirm your email address change here by entering your password:</p>
				<div class="wrapper">
			        <label for="password" style="margin-bottom:0;">Password</label>
			        <input id="password" name="password" size="30" type="password"/><br />
			        Enter your password to confirm this action.
				</div>
				
				 <input type="submit" value="Change my email address"/>
				<h4>If you are still having trouble, <a href="http://support.thepapertiger.com">contact support</a>!</h4>
			</form>
			
			<p id="login_help"><a href="<?=site_url()?>login">Take me back to the login page.</a></p>
			
		</div>
	</body>
</html>
