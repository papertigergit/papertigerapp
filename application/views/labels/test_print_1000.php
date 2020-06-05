<?php error_reporting(0);
	$items = $items->result_array();

?>

<table width="100%" cellpadding="30" border="1">
<?php 
	$count=0;
	for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%4 == 0): ?>
<tr>
<?php endif; ?>
<?php if($print_type): ?>
	<td height="0.50in" align="center">
		<?php if(strlen($location_name) > 12): ?> 
		<span><?php echo $location_name; ?> <?php echo $i; ?></span>
		<?php else: ?>
		<?php echo $location_name;?> <?php echo $i; ?>
		<?php endif; ?>
	</td>
<?php endif; ?>
<?php if($print_type):  ?>
		<td align="center">
			
		</td>
<?php elseif($print_type): ?>
		<td height="0.50in" align="center">
			 <?php if(strlen($location_name) > 12): ?> 
		<span><?php echo $location_name; ?> <?php echo $i; ?></span>
		<?php else: ?>
		<?php echo $location_name;?> <?php echo $i; ?>
		<?php endif; ?>
		</td>
<?php  endif; ?>				
<?php $count+=2; ?>
<?php if($count%4 == 0): ?>
</tr>
<?php endif; ?>
<?php endfor; ?>
<?php if($count%2 == 0 && $count%4 != 0): ?>
</tr>
<?php endif; ?>
<div>
</div>
</table> 
