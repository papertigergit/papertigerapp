<?php error_reporting(0); ?>
<table width="100%" cellpadding="15" border="0">
<?php 
	$count=0;
	for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%4 == 0): ?>
<tr>
<?php endif; ?>

<td height="1.0865in" width="2.8in" align="left" ><?php echo $location_name; ?></td>	
<td height="1.0865in" width="0.6345in" align="center"><?php echo $i; ?></td>

<td width="0.60in" border="0">
</td>


<?php $count+=2; ?>
<?php if($count%4 == 0): ?>
</tr>
<?php endif; ?>
<?php endfor; ?>
<?php if($count%2 == 0 && $count%4 != 0): ?>
</tr>

<?php endif; ?>
</table>