<html>
	<head>
		<title>Change email address request for Paper Tiger</title>
		
		
	</head>
	<body style="background: #ececec;font-family: Arial;">
	
	<table class="header" style="background: white;width: 650px;display: block;padding: 15px;margin: 20px auto 2px auto;">

		<tr>
	
			<td width="524px;">
			
				<h1 style="margin-top: -8px;margin-bottom: -5px; font-family: Trebuchet MS"><span style="color:gray">Change your Paper Tiger email address</h1>
			
			</td>	
			
			<td align="right" valign="middle">
			
				<img src="<?=site_url()?>img/tiger_head_small.png" alt="tiger_head_small" height="64px">
			
			</td>

		</tr>

	</table>
	
	<table class="email_body" style="background: white;width: 650px;display: block;padding: 10px 15px;margin: 0 auto;">
		<tr>
		
			<td>
				<p>Hey there!</p>
				
				<p>Someone has requested an email address change for your Paper Tiger account.</p>
			
				<p>Please use the following link to confirm this change:</p>

				<h2 style="font-family:Trebuchet MS"><?php echo anchor('account/reset_email/reset/'.$activation_code); ?></h2>		
				
				<p>If this email has been delivered to you by mistake, ignore it and <a href="http://support.thepapertiger.com">Report it</a>!</p>
				
				<h4 style="font-family: Trebuchet MS">Delivered by <a href="http://www.thepapertiger.com">Paper Tiger</a></h4>
	
			</td>

		</tr>
	
	</table>
	
	</body>
</html>
