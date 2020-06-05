<html>
	<head>
		<title>Account Details</title>
		<?php get_instance()->load->helper('url_helper'); ?>
		
	</head>
	<body style="background: #ececec;font-family: Arial;">
	
	<table class="header" style="background: white;width: 650px;display: block;padding: 15px;margin: 20px auto 2px auto;">

		<tr>
	
			<td width="524px;">
			
				<h1 style="margin-top: -8px;margin-bottom: -5px; font-family: Trebuchet MS"><span style="color:gray">Account Created on <?=site_url()?></h1>
			
			</td>	
			
			<td align="right" valign="middle">
			
			
			</td>

		</tr>

	</table>
	
	<table class="email_body" style="background: white;width: 650px;display: block;padding: 10px 15px;margin: 0 auto;">
		<tr>
		
			<td>
				
				<p>Client Name : <?=$first_name?> <?=$last_name?></p>
				<p>Email	: <?=$email?></p>
				<p>Company : <?=$company_name?></p>
				<p>Package Name : <?=$package?></p>
				<p>Package Price : <?=$price?></p>
				
				<h4 style="font-family: Trebuchet MS">Delivered by <a href="http://www.thepapertiger.com">Paper Tiger</a></h4>
	
			</td>

		</tr>
	
	</table>
	
	</body>
</html>
