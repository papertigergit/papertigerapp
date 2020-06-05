<html>
	<head>
		<title>Your Paper Tiger Account has been created!</title>
	<?php get_instance()->load->helper('url_helper'); ?>	
		
	</head>
	<body style="background: #ececec;font-family: Arial;">
	
	<table class="header" style="background: white;width: 650px;display: block;padding: 15px;margin: 20px auto 2px auto;">

		<tr>
	
			<td width="524px;">
			
				<h1 style="margin-top: -8px;margin-bottom: -5px; font-family: Trebuchet MS"><span style="color:gray">Your Paper Tiger account has been created!</h1>
			
			</td>	
			
			<td align="right" valign="middle">
			
				<img src="<?=site_url()?>img/tiger_head_small.png" alt="tiger_head_small" height="64px">
			
			</td>

		</tr>

	</table>
	
	<table class="email_body" style="background: white;width: 650px;display: block;padding: 10px 15px;margin: 0 auto;">
		<tr>
		
			<td>
				<p>Dear <?php echo $first_name.' '.$last_name; ?>,</p>
				
				<p>Thank you for creating a Paper Tiger account! You are now on your way to a better filing system.</p>
			
				<h2 style="font-family:Trebuchet MS, margin-bottom:0;"><span style="color:black">Your Account email:</span> <?php echo $email; ?></h2>
				<h2 style="font-family:Trebuchet MS"><span style="color:black">Log in at:</span> <?php echo base_url(); ?></h2>				
				
				<p>If you find any problems, please <a href="http://support.thepapertiger.com">get in touch with us</a>!</p>				
				
				<h4 style="font-family: Trebuchet MS">Delivered by <a href="http://www.thepapertiger.com">Paper Tiger</a></h4>
	
			</td>

		</tr>
	
	</table>
	
	</body>
</html>