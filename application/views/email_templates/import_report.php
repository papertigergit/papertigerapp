<?php
$this->load->helper('date');
?>
<html>
	<head>
		<title>Paper Tiger Database Import Report</title>
		 
		
	</head>
	<body style="background: #ececec;font-family: Arial;">
	
	<table class="header" style="background: white;width: 650px;display: block;padding: 15px;margin: 20px auto 2px auto;">

		<tr>
	
			<td width="524px;">
						
				<h4 style="font-family: Trebuchet MS"><?=$org_name?></h4>
			
				<h1 style="margin-top: -8px;margin-bottom: -5px; font-family: Trebuchet MS"><span style="color:gray">Import Report:</span> <?=$database_name?></h1>
			
			</td>	
			
			<td align="right" valign="middle">
			
				<img src="<?=site_url()?>img/tiger_head_small.png" alt="tiger_head_small" height="64px">
			
			</td>

		</tr>

	</table>
	
	<table class="email_body" style="background: white;width: 650px;display: block;padding: 10px 15px;margin: 0 auto;">
		<tr>
		
			<td>
				
				<p>Your database has finished importing! Please review the following report and check this database against your existing Paper Tiger database to make sure the locations have imported properly.</p>
				
				<p>If you find any problems, please <a href="http://suport.thepapertiger.com">get in touch with us</a>!</p>
				
				<h2 style="font-family:Trebuchet MS"><span style="color:#478430">Import Date:</span> <?=unix_to_human($date_added)?></h2>
				
				<table id="locations" style="background: white;width: auto;display: block;padding: 0;margin: 0;margin-bottom: 20px;">
					
					<thead style="background: #ececec;">
					
						<tr>
							<th style="padding: 5px;text-align: left;border: 0;">Location Name</th>
							<th style="padding: 5px;text-align: left;border: 0;">Number of Items</th>
							<th style="padding: 5px;text-align: left;border: 0;">Total Capacity</th>
						</tr>
					
					</thead>
					
					<tbody>
					
					<?php foreach($locations as $row){?>
							
						<tr>
							<td style="width: 200px;text-align: left;padding: 5px;height: 30px;border-bottom: solid 1px #ececec;"><?=$row->name?></td>
							<td style="width: 200px;text-align: left;padding: 5px;height: 30px;border-bottom: solid 1px #ececec;"><?=$row->num_items?></td>
							<td style="width: 200px;text-align: left;padding: 5px;height: 30px;border-bottom: solid 1px #ececec;"><?=$row->capacity?></td>
						</tr>
					
					<?php }?>	
					
					</tbody>
								
				</table>
				
				<h4 style="font-family: Trebuchet MS">Delivered by <a href="http://www.thepapertiger.com">Paper Tiger</a></h4>
	
			</td>

		</tr>
	
	</table>
	
	</body>
</html>
