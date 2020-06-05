<div id="update_action_form" class="facebox_form">
	<div id="facebox_header"><div>Clear Reminders</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<?php if(count($segs) < 3) {
			?>
		<div id="flash_error">You did not select an item to clear!</div>
		<?php }
		else if(count($segs) > 3) {
			?>
		<p id="facebox_intro">Are you sure you want to clear <?=count($segs) > 3 ? "these reminders":"this reminder" ?></p>
		<?php } ?>

		<form action="<?=site_url()?>reminders/do_clear" method="POST" class="form" name="clear_reminders">
			
			<div class="form_divider">

				<ul id="facebox_list">
				
					<?php
					$num_seg=count($segs);
					$items=array();
		                        if($num_seg > 2){ 
		                           for($i=3; $i<=$num_seg; $i++){
		                            $items[$i-3] = $segs[$i];
		                        	}   
					}
		
						if($items != NULL){
						$item_data=$this->Item_model->get_by_ids($items);
						foreach($item_data->result() as $row)
						{
							echo "<li>".$row->name;
						}
						
		
		                        foreach($item_data->result() as $item)
					{ ?>
					  <input type="hidden" value="<?=$item->id?>" name="items[]"/>
				        <?php  
					}}
					?>
				
				</ul>
			
			</div>
			
			<div id="result"></div>
			
			<div id="submit_container">
			<?php
				if(count($segs) >= 3)
				{
					?>
				<span><input type="submit" name="submit" id="submit" value="Clear Reminders"/></span>
				<span class="small gray"><span>or</span><a href="#" class="red close">Cancel</a></span>
				<?php } else if(count($segs) < 3) {?>
					<div style="text-align:center;"><a href="#" class="close red">Cancel</a></div>
				<?php } ?>
			</div>
		</form>
	</div>
</div>
