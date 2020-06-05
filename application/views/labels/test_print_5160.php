<?php 
	error_reporting(0);
?>
<table width="100%" cellpadding="15" border="">
<?php 
	$count=0;
	$j=0;
	$printable_item_names=$settings->result_object[0]->print_item_name;

	$printable_item_names = 1;

if($printable_item_names==1){		
	$label_text='';
	$items_result=$items->result_array();
	
	if($print_location_name=="on" && $print_item_name=="on"):
		$label_text=$location_name;
	endif;	
	if($print_location_name==null && $print_item_name=="on"):
		$label_text=$location_name;
	endif;	
	if($print_location_name=="on" && $print_item_name==null):
		$label_text=$location_name;
	endif;	
} else {
	$label_text=$location_name;
}
  $i=$range_start;
  $item_slot = $items_result[$i-1]['slot_num'];
  $k = $range_start - 1;
  $r = $range_start - 1;
  $item_range = $range_end - $range_start;
  $item_range = $item_range + 1;
	for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%3 == 0): ?>
<tr>
<?php endif; ?>
<?php
 $counter++;
$k++;
    if ($counter == $item_range && $counter%3 == 1) { $count = $count + 2;$range_end = $range_end + 2;}
    if ($counter == $item_range && $counter%3 == 2) { $count = $count + 1;$range_end = $range_end + 2;}
if ($item_slot == $k){ 
  $r++;
$item_slot = $items_result[$r-1]['slot_num'];
$trim = trim($items_result[$r-1]['name']);
$notrim = $items_result[$r-1]['name'];
?>

<td height="1in" width="1.75in" align="center" >
<br/>
		<strong style=""><?php 
			if($print_type == 'all' || $print_type == 'location'): 
				echo character_limiter($label_text,14,'').' '; 
			 endif; 
			 echo $item_slot; 
		?></strong>
		<?php if($print_type == 'all' || $print_type == 'title'):  ?>
				<br/><font size="9%"><?php echo character_limiter($trim, 20, '…') ? character_limiter($notrim, 22, '…') : '(open)'; ?></font>
		<?php  endif; ?>

</td>
<?php 
       
$item_slot = $items_result[($r-1) + 1]['slot_num'];

}else{ 
?>

<td height="1in" width="1.75in" align="center" >
<br/>
		<strong style=""><?php 
			if($print_type == 'all' || $print_type == 'location'): 
				echo character_limiter($label_text,14,'').' '; 
			 endif; 
			 echo $i; 
		?></strong>
		<?php if($print_type == 'all' || $print_type == 'title'):  ?>
				<br/><font size="9%"><?php echo '(open)'; ?></font>
		<?php  endif; ?>
       

</td>
<?php } ?>

<td border="0">
</td>

<?php $count+=1; ?>
<?php if($count%3 == 0): ?>
</tr>
<?php endif; ?>
<?php   endfor; ?>
</table>
