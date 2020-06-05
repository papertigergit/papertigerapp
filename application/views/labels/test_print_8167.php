<?php error_reporting(0); ?>
<table width="100%" cellpadding="20" border="">
<?php 
  $count=0;
  for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%4 == 0): ?>
<tr>
<?php endif; ?>


<td height="0.50in" width="1.75in" align="left" ><font size="13px"><?php echo $location_name . "   " . $i; ?></font> </td>
<td height="0.50in" width="0.02in" align="left" ><font size="10px"><?php ?></font></td> 

  
<td width="0.25in" border="0">
</td>
<?php $i++;?>

<td height="0.50in" width="1.75in" align="left" ><font size="13px"><?php echo $location_name . "   " .  $i; ?></font> </td>
<td height="0.50in" width="0.02in" align="left" ><font size="10px"><?php  ?></font></td> 

<td width="0.25in" border="0"></td>
<?php $count+=2; ?>
<?php if($count%4 == 0): ?>
</tr>
<?php endif; ?>
<?php endfor; ?>
<?php if($count%2 == 0 && $count%4 != 0): ?>
</tr>
<?php endif; ?>
</table>
