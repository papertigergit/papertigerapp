<table width="100%" cellpadding="30" border="1">
<?php 
	$count=0;
	for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%4 == 0): ?>
<tr>
<?php endif; ?>
<td align="center">
	<?php if(strlen($location_name) > 12): ?> 
	<span style="font-size:50%"><?php echo $location_name; ?> <?php echo $i; ?></span>
	<?php else: ?>
	<?php echo $location_name;?> <?php echo $i; ?>
	<?php endif; ?>
</td>
<td align="center"><font size="8px">&lt;== Fold Here</font></td>
<?php $count+=2; ?>
<?php if($count%4 == 0): ?>
</tr>
<?php endif; ?>
<?php endfor; ?>
<?php if($count%2 == 0 && $count%4 != 0): ?>
</tr>
<?php endif; ?>
</table>