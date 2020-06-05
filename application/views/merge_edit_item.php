<?php
		$show_notes=$this->Settings_model->get_all();
		$notes_settings=array($show_notes->result_array());
?>

<div id="edit_item_form" class="facebox_form">
<?php
         $location_id=$this->Item_model->get_by_id($this_item->id)->location_id;
         $location_name=$this->Location_model->get_by_ids($location_id)->row()->name;
	 $slot_num=$this->Item_model->get_by_id($this_item->id)->slot_num;
?>
	<div id="facebox_header"><div>Merging into  location '<?=$location_name?>' slot <?=$slot_num?></div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">You can add or remove information, make changes, and set a new action date here.</p>
		<form action="<?=site_url()?>items/edit/" method="POST" class="form" name="edit_item">
			<div id="form_padding">
				<div class="form_divider">
					<p class="tooltip_content">This is the name of the item. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a></p>										
					<label for="name">Item Name<img width="16"  height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="input first_input" name="name" id="name" value="<?=$this_item->name?>" />
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">Keywords are used to describe the item. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a></p>										
					<label for="keywords">Keywords<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<?php $ff_keywords = isset($ff_keywords)?' '.$ff_keywords:''?>
					<textarea type="text" name="keywords" id="keywords" class="input"><?=$this_item->keywords.' '.$merge_keywords?></textarea>
					<div class="clear"></div>
				</div>
				<?php
				if($show_notes->num_rows() > 0) {
					if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
					{
						?>
				<div class="form_divider">
					<p class="tooltip_content">Notes can be added to an item to remember something specific in a contextual manner. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194210&catId=230>Learn More</a></p>										
					<label for="notes">Notes<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<?php $ff_notes = isset($ff_notes)?' '.$ff_notes:''?>
					<textarea type="text" name="notes" id="notes" class="input"><?=$this_item->description.' '.$merge_notes?></textarea>
					<div class="clear"></div>
				</div>
						<?php
					}
				}
				?>
				<div class="form_divider">
					<p class="tooltip_content">Use categories to group items without having them in the same physical location. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194212&catId=230>Learn More</a></p>										
					<label for="category">Category<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<select name="category" id="category" class="select">
						<?php foreach ($categories->result() as $row) { ?>
						<option value="<?=$row->id?>"<?=$row->id==$this_item->category_id?" SELECTED":""?>><?=$row->name?></option>
						<?php } ?>
						<option value="0"<?=(0==$this_item->category_id)?" SELECTED":""?>>No Category</option>
					</select>
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">Use Action dates to set a reminder to perform a certain action on an item on a given day. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194221&catId=230>Learn More</a></p>										
					<label>Action Date<img width="16" height="16"  class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<?php 
						$actionDate = $this_item->action_date==0?'&nbsp;':date("m/d/Y",$this_item->action_date);
						if(isset($new_action_date)){
							$actionDate = $new_action_date;
						}
					?>
					<input type="text" name="action_date" id="action_date" class="input" value="<?=$actionDate?>"/>
					<div class="clear"></div>
				</div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Merge Item"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
						<?php foreach($items as $item){ ?>
				<input type="hidden" value="<?=$item?>" name="items[]"/>
			<?php }?>
			<?php foreach($merged_items as $merged){?>
				<input type="hidden" value="<?=$merged?>" name="merged[]"/>
			<?php }?>
			<input type="hidden" name="edit_item" value="true"/>
			<input type="hidden" name="merge_edit" value="true"/>
			<input type="hidden" name="location_id" value="<?=$location_id?>"/>
			<input type="hidden" name="confirmation" value="<?=$confirmation?>"/>
			<input type="hidden" name="edit_time" id="edit_time" value="<?=time()?>"/>
		</form>
	</div>
</div>
