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
		
                <?php echo form_open("signup/user/".$activation_code); ?>
				<h3>Welcome to Paper Tiger!</h3>
                <p>You are moments away from creating your Paper Tiger account. Just choose a password for your account and you'll be automatically signed into the Paper Tiger account of the person who invited you.</p>

                <h4 class="bottom">Account you're invited to: <?=$organization->name?></h4>

                <h4 style="border-bottom:solid 2px #ddc952; padding-bottom:5px; margin-top:5px;">Your account email: <a href="mailto:<?=$user->email_address?>"><?=$user->email_address?></a></h4>

				<div class="wrapper">
					<label for="password">Choose your password:</label>					 
					<input type="password" name="password" id="signup_password" />
				</div>
				<div class="wrapper">
					<label for="password_confirmation">Verify your new password:</label>					 
					<input type="password" name="password_confirmation" id="signup_password_confirmation" />
				</div>
                <input style="display:block; margin-left:110px; margin-top:10px; width:150px;" type="submit" value="Create my Account!" />
            
                </form>
			
			
		</div>
	</body>
</html>
