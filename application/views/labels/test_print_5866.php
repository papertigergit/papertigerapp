<?php 
	error_reporting(0);
?>
<table style="padding:100px;" width="100%" cellspacing="0" cellpadding="" border="">
<?php 
	$count=0;
	$j=0;
	$printable_item_names=$settings->result_object[0]->print_item_name;
	$printable_item_names = 1;

if($printable_item_names==1){		
	$label_text='';
	$items_result=$items->result_array();
	if($print_location_name=="on" && $print_item_name=="on"):
		$label_text=$location_name;//."-".$items_result[$j]['name'];
	endif;	
	if($print_location_name==null && $print_item_name=="on"):
		$label_text=$location_name;//$items_result[$j]['name'];
	endif;	
	if($print_location_name=="on" && $print_item_name==null):
		$label_text=$location_name;
	endif;	
} else {
	$label_text=$location_name;
}
  $i=$range_start;
  $item_range = $range_end - $range_start;
  $item_range = $item_range + 1;
  $item_slot = $items_result[$i-1]['slot_num'];
  $k = $range_start - 1;
  $r = $range_start - 1;
  $range_end = $range_end;
  //if ($range_end = 25){
    //$range_end = $range_end + 1;
  //} 
  
	for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%4 == 0 || $count%4 == 3 || $count%4 == 1): ?>
<tr>
<?php endif; ?>
<?php
    $counter++;
$k++;

    if ($counter == $item_range && $counter%2 == 1) { $count = $count + 2;}
if ($item_slot == $k){ 
  $r++;
$item_slot = $items_result[$r-1]['slot_num'];
$trim = trim($items_result[$r-1]['name']);
$notrim = $items_result[$r-1]['name'];
?>
<td style="margin-right:500px;" height=".67in" width="3.0in" align="center" >
		<div style="line-height:5px;">
		<strong><?php 
			if($print_type == 'all' || $print_type == 'location'): 
				echo "<font size=\"12%\">" . ' ' . character_limiter($label_text,14,'') . "</font> " ; 
			 endif; 
			 echo "<font size=\"12%\">" . $item_slot . "</font> "; 
		?></strong>
		
		<?php
		 if($print_type == 'all' || $print_type == 'title'):  ?>

				<td></td><font size="8%;"><?php echo character_limiter($trim, 32, '…') ? character_limiter($notrim, 32, '...') : '(open)'; ?></font></div>
		<?php  endif; ?>					
<?php if($print_type == 'location'):  ?>
				<td></td></div>
		<?php  endif; ?>
</td>
<?php  
$item_slot = $items_result[($r-1) + 1]['slot_num'];
}else{ 
?>
<td style="margin-right:500px;" height=".67in" width="3.0in" align="center" >
		<div style="line-height:5px;">
		<strong><?php 
			if($print_type == 'all' || $print_type == 'location'): 
				echo "<font size=\"12%\">" . ' ' . character_limiter($label_text,14,'') . "</font> " ; 
			 endif; 
			 echo "<font size=\"12%\">" . $i . "</font> "; 
		?></strong>
		<?php
		 if($print_type == 'all' || $print_type == 'title'):  ?>
				<td></td><font size="8%;"><?php echo '(open)'; ?></font></div>
		<?php  endif; ?>					
<?php if($print_type == 'location'):  ?>
				<td></td></div>
		<?php  endif; ?>
</td>
<?php  } ?>
<td width="1.0in" border="0">
</td>

<?php $count+=2; ?>
<?php if($count%4 == 0 || $count%4 == 3 || $count%4 == 1): ?>
</tr>
<?php endif; ?>
<?php endfor; ?>
</table>
