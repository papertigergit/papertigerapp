<?php
$segs = $this->uri->segment_array();
$num_seg = $this->uri->total_segments();
if($num_seg > 2){ 
for($i=3; $i<=$num_seg; $i++){
        $items[$i-3] = $segs[$i];
	}   
foreach($items as $confirmations)
	{
		$num_of_items=sizeof($items);
	}
}
else
{
	//
}
?>
<div id="delete_item_form" class="facebox_form">
		<?php
		if($num_of_items == 1)
		{
		?>
		<div id="facebox_header"><div>Clearing One Reminder</div></div>
		<?php
		}
		if($num_of_items > 1)
		{
		?>
		<div id="facebox_header"><div>Clearing Multiple Reminders</div></div>
		<?php
		}
		?>
	<div id="facebox_content">		
		<p id="facebox_body">Are you sure you want to clear the following reminder<?=($num_of_items>1)? "s" : ''?></p>
		<ul id="facebox_list">
		<?php
		foreach($items as $confirmations)
		{
			echo "<li>";	
			print_r($this->Item_model->get_by_id($confirmations)->name);
		}
		?>
		</ul>
		<form action="<?=site_url()?>reminders/do_clear/" method="POST" class="form" name="delete_item" style="max-width:450px">
			<div id="result"></div>
			<input type="hidden" name="items" value="<?=$items?>">
			<div id="facebox_footer">			
				<div class="right" >
						<div class="small" style="vertical-align:middle;float:left;"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<input type="submit" name="submit" class="font_normal" value="Clear Reminders" style="float:left"/>
				</div>
				<div class="clear"></div>
			</div>
		</form>
	</div>
</div>
