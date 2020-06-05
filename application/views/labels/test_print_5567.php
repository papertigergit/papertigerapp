<?php error_reporting(0); ?>
<?php  
 $abc="Ankit Parekh";
 $this->load->library('pdf');
 $this->pdf->StartTransform();
 $this->pdf->Rotate(-90);
 $this->pdf->write(2,$abc);
 $this->pdf->StopTransform();
?> 
<table width="100%" cellpadding="15" border="0">
<?php 
	$count=0;
	for($i=$range_start; $i<=$range_end; $i++): 
?>
<?php if($count%3 == 0): ?>
<tr>
<?php endif; ?>

<td height="1in" width="1.8in" align="left" ><?php echo $location_name; ?></td>	
<td height="1in" width="0.525in" align="center" ><?php echo $i; ?></td>

<td width="0.2in" border="0">
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