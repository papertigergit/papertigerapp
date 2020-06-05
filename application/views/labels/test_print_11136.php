<?php error_reporting(0); ?>
<table width="100%" cellpadding="0" border="0">
<?php 
	$count=0;
	$item_count=0;
	for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%4 == 0): ?>
<tr>
<?php endif; ?>
<td height="0.5in" width="1.5in" align="left" ><?php echo $location_name; ?></td>	
<td height="0.5in" width="0.5in" align="center"><?php echo $i; ?></td>


<?php $i++;?>
<td height="0.5in" width="1.5in" align="left" ><?php echo $location_name; ?></td>	
<td height="0.5in" width="0.5in" align="center"><?php echo $i; ?></td>



<?php $count+=2; ?>
<?php if($count%4 == 0): ?>
</tr>
<?php
$item_count++;
?>
<?php
if($item_count%10 == 0){
	for($j=0;$j<9;$j++) {
?>
<tr>
<td height="0.5in"></td>
</tr>
<?php }
}
?>
<?php endif; ?>
<?php endfor; ?>
<?php if($count%2 == 0 && $count%4 != 0): ?>
</tr>
<?php endif; ?>
</table>
