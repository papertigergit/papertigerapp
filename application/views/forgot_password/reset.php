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
	
		<div id="main_wrapper">
			
			<span class="alert"><?php echo validation_errors(); ?></span>
		
				<?php echo form_open("forgot_password/reset/".$authorization_code); ?>
				<h3>Reset your password</h3>
				<p>If you forgot your password or it doesn't work, you can reset it here:</p>
				<div class="wrapper">
			        <label for="password" style="margin-bottom:0;">New password</label>
			        <input id="password" name="password" size="30" type="password"/><br />
			        6 characters or longer with at least one number is safest.
				</div>
				
				<div class="wrapper">
			        <label for="password" style="margin-bottom:0;">Verify password</label>
			        <input id="password" name="password_confirmation" size="30" type="password"/><br />
			        6 characters or longer with at least one number is safest.
				</div>
				
				 <input type="submit" value="Reset my password"/>
				<h4>If you are still having trouble, <a href="http://support.thepapertiger.com">contact support</a>!</h4>
			</form>
			
			<p id="login_help"><a href="<?=site_url()?>login">Take me back to the login page.</a></p>
			
		</div>
	</body>
</html>
