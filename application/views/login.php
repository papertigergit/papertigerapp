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
		<link href="<?=site_url()?>css/login.css" media="screen" rel="stylesheet" type="text/css" />
		<link rel="apple-touch-icon" href="<?=site_url()?>img/apple-touch-icon.png"/>
		<script type="text/javascript" src="<?=site_url()?>js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="<?=site_url()?>js/jquery.form.js"></script>
		<script type="text/javascript" src="<?=site_url()?>js/jquery.blockUI.js"></script>
		<script type="text/javascript" src="<?=site_url()?>js/login.js"></script>
		<script type="text/javascript" src="<?=site_url()?>facebox/facebox.js"></script>
		<link rel="icon" type="image/png" href="<?=site_url()?>img/favicon.png" />		
	</head>
	<body>
	<?php if(isset($using_ie6) && $using_ie6 === true){
	?>
	  <script>
		  $(document).ready(function(){
				jQuery.facebox('<div>This application does not support Internet Explorer 6.</div><div>Please upgrade your browser to start using application.</div>');
		  });
		
	  </script> 
	<?php } ?>
	    <div id="top_span">
	        <div id="logo">
	            Paper Tiger Online - The easiest way to manage your documents!
	        </div>
	    </div>
		<div id="result"></div>	
		<div id="main_wrapper">
			<form name="login-form" id="login-form" method="post" action="<?=site_url()?>login">
		
				<h2>Sign In to</h2>
				<h1>Paper Tiger</h1>


				<div class="wrapper">
					<label for="email">Email</label>
					<input type="text" id="email" name="email" /> 
				</div>
				<div class="wrapper">
					<label for="password">Password</label>
			    	<input type="password" id="password" name="password" /> 
		    	</div>
		    	<div class="wrapper">
		    		<input class="remember" type="checkbox" name="remember_me">
		    		<label class="remember" for="remember_me">Stay signed in on this computer (7 days)</label>
		    	</div>
	    	<div class="wrapper">
	    	</div>
		    	<input type="image" id="submit" src="/img/login/signin_button.png"/>
			</form>
			
			<p id="login_help" style="text-align:center; margin-top: 40px; font-size: 16pt;">Help! <a href="<?=site_url()?>forgot_password">I forgot my password/I can't log in!</a></p>
			
			<p id="login_help" style="text-align:center; margin-top: 10px;font-size: 16pt;">Don't have an account? <a href="http://www.thepapertiger.com/pricing.php" >Sign Up FREE!</a></p>

			<p id="login_help" style="text-align:center; margin-top: 10px;font-size: 12pt;">Privacy Policy <a href="http://www.thepapertiger.com/privacy_policy.php" target="_blank">Privacy Policy</a></p>
			
			<p id="login_help" style="text-align:center; margin-top: 10px;font-size: 12pt;">Terms Of Service <a href="http://www.thepapertiger.com/terms_of_service.php" target="_blank">Terms Of Service</a></p>
		</div>
	</body>
</html>
