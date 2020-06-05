<html>
	<head>
		<title>Your Paper Tiger Account has been created!</title>
		
		
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
				<p>Dear <?php echo $new_user_fname.' '.$new_user_lname; ?>,</p>
				
                <p>You have been invited to create a Paper Tiger account by <?=$admin_fname?> <?=$admin_lname?> of <?=$organization_name?>.</p>

                <p>You can create your account by clicking on the link below. This will create an account under the organization that invited you (<?=$organization_name?>).
			
                <h2 style="font-family:Trebuchet MS, margin-bottom:0;">
<?=anchor($register_url, 'Click here to create your account!'); ?>
</h2>

<p>After you create your Paper Tiger account, you can access this account in the future by using the link below:</p>

				<h2 style="font-family:Trebuchet MS"><span style="color:black">Log in at:</span> <?php echo base_url(); ?></h2>				
				
                <p>If you have any questions, you can get in touch with your account administrator (<?=$admin_fname?> <?=$admin_lname?>) at  <a href="mailto:<?=$admin_email?>"><?=$admin_email?></a>. You can also get in touch with <a href="mailto:support@thepapertiger.com">Paper Tiger Support</a>.</p>				
				
				<h4 style="font-family: Trebuchet MS">Delivered by <a href="http://www.thepapertiger.com">Paper Tiger</a></h4>
	
			</td>

		</tr>
	
	</table>
	
	</body>
</html>
