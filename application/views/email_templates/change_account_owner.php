<html>
	<head>
		<title>Change of account administrators</title>
		
		
	</head>
	<body style="background: #ececec;font-family: Arial;">
	
	<table class="header" style="background: white;width: 650px;display: block;padding: 15px;margin: 20px auto 2px auto;">

		<tr>
	
			<td width="524px;">
			
				<h1 style="margin-top: -8px;margin-bottom: -5px; font-family: Trebuchet MS"><span style="color:gray">Paper Tiger account ownership change</h1>
			
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
				
				<p>You have changed the account administrator for your Paper Tiger account.</p>
			
				<p>Here are the details.</p>

				<p>New Account Owner : <?=$get_new_admin_info->first_name?> <?=$get_new_admin_info->last_name?></p>
				
				<p>New Admin Email : <?=$get_new_admin_info->email_address?></p>

				<p>If you did not initiate this action, <a href="http://support.thepapertiger.com">Report it</a>! to Paper Tiger</p>
				
				<h4 style="font-family: Trebuchet MS">Delivered by <a href="http://www.thepapertiger.com">Paper Tiger</a></h4>
	
			</td>

		</tr>
	
	</table>
	
	</body>
</html>
