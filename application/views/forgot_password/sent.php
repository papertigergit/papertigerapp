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
		
				<h1>Your password reset has been sent, please check your email.</h1>
				
				<h4 style="text-align:center; width:432px;">The email is sent immediately. If you don't see it, look in your spam folder.</h4>
			
		</div>
	</body>
</html>