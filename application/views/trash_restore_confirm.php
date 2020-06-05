<?php
$this->load->helper('text');
?>
<div id="delete_restore_form" class="facebox_form">	
	<div id="facebox_header"><div>Restore Item: <?=character_limiter($item['name'],22,'...')?></div></div>
	<div id="facebox_content">
		<form action="<?=site_url()?>trash/restore/" method="POST" class="form" name="restore_trash">
			<p id="facebox_body">Are you sure you want to restore the following item:</p>
			<ul id="facebox_list">
				<li><?=$item['name']?></li>
			</ul>
			<?php 
			$prev_location=$this->Location_model->get_by_id($item['previous_location_id']);
			if($prev_location)
			{
				$location_deleted=false;
				if($prev_location->empty_slots == 0)
				{
					$location_empty=true;
				}
				elseif($prev_location->empty_slots > 0)
				{
					$location_empty=false;
				}
			}
			elseif(!$prev_location)
			{
				$location_deleted=true;
				$location_empty=true;
			}
			if(($item['previous_location_id']==-1) || ($location_empty==true) || ($location_deleted==true))
			{?>
				<select name="new_location" id="new_location">
					<option value="-1">Select Destination...</option>
					<?php foreach($locations->result() as $row){?>
						<?php if($this->Location_model->get_by_id($row->id)->empty_slots == 0){$is_full=true;}else{$is_full=false;}?>
						<option value="<?=$row->id?>"<?=$is_full?" disabled":''?>><?=$row->name?><?=$is_full?" (full)":''?></option>
					<?php }?>
				</select>
			<?php } else {?>
				<p id="facebox_body">to location "<?=$this->Location_model->get_by_id($item['previous_location_id'])->name?>"</p>
				<input type="hidden" value="<?=$item['previous_location_id']?>" name="new_location"/>
			<?php }?>
			<div id="result"></div>
			<div id="facebox_footer">
				<div class="right" >
						<div class="small left" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<input type="hidden" value="<?=$item['id']?>" name="item"/>
						<input type="hidden" value="true" name="restore" />
						<input type="submit" class="font_normal right" value="Restore"/>
				</div>
				<div class="clear"></div>
		</div>
		</form>
	</div>
</div>
