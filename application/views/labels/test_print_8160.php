<?php error_reporting(0); ?>
<table width="100%" cellpadding="15" border="0">
<?php 
	$count=0;
	for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%3 == 0): ?>
<tr>
<?php endif; ?>

<?php if(strlen($location_name) > 20): ?> 
<span style="font-size:50%">
<td height="1in" width="2.125in" align="left" ><?php echo $location_name; ?></td>	
<td height="1in" width="0.525in" align="center"><?php echo $i; ?></td>
</span>
<?php else: ?>

<td height="1in" width="2.125in" align="left" ><?php echo $location_name; ?></td>
<td height="1in" width="0.525in" align="center"><?php echo $i; ?></td>
	
<?php endif; ?>
<td width="0.1in" border="0">
</td>

<?php $count+=2; ?>
<?php if($count%3 == 0): ?>
</tr>
<?php endif; ?>
<?php endfor; ?>
<?php if($count%3 == 0): ?>
</tr>
<?php endif; ?>
</table>