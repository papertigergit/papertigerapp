<?php error_reporting(0); ?>
<table width="100%" cellpadding="5" border="0">
<?php 
  $count=0;
  for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%4 == 0): ?>
<tr>
<?php endif; ?>

<?php if(strlen($location_name) > 20): ?> 
<span style="font-size:50%">
<td height="0.66in" width="2.8in" align="left" cellpadding="15"><?php echo $location_name; ?></td>  
<td height="0.66in" width="0.6345in" align="right"><?php echo $i; ?></td>
</span>
<?php else: ?>

<td height="0.66in" width="2.8in" align="left" cellpadding="15"><?php echo $location_name; ?></td>
<td height="0.66in" width="0.6345in" align="right"><?php echo $i; ?></td>
  
<?php endif; ?>
<td width="0.5in" border="0">
</td>
<!--<td align="center"><font size="8px">&lt;== Fold Here</font></td>-->
<?php $count+=2; ?>
<?php if($count%4 == 0): ?>
</tr>
<?php endif; ?>
<?php endfor; ?>
<?php if($count%2 == 0 && $count%4 != 0): ?>
</tr>
<?php endif; ?>
</table>
