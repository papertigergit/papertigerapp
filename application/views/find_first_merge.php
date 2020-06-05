<div id="edit_item_form" class="facebox_form">
	<div id="facebox_header"><div>Merging into '<?=character_limiter(htmlspecialchars($this_item->name),37,'...')?>'</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<?php
		$location_id=$this->Item_model->get_by_id($this_item->id)->location_id;
		$location_name=$this->Location_model->get_by_ids($location_id)->row()->name;
		?>
		<p id="facebox_intro">You are adding this item into "<?=$location_name?>" slot <?=$this_item->slot_num?></p>
		<form action="<?=site_url()?>items/edit/" method="POST" class="form" name="edit_item">
			<div id="form_padding">
				<div class="form_divider">
					<label for="name">Item Name<img width="16" title="This is the name of the item" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text"<?=$multiple_items?" disabled":""?> class="input<?=$multiple_items?" disabled_input":" first_input"?>" name="name" id="name" <?= $multiple_items?"":'value="'.htmlspecialchars($this_item->name).'"'?> <?=$multiple_items?"DISABLED":""?>/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<label for="keywords">Keywords<img width="16" height="16" class="tooltip" title="These are keywords that describe the item" src="<?=site_url()?>img/tooltip.png"></label>
					<?php $ff_keywords = isset($ff_keywords)?' '.$ff_keywords:''?>
					<textarea type="text" name="keywords" id="keywords"<?=$multiple_items?" disabled":""?> class="input<?=$multiple_items?" disabled_input":""?>"><?=$multiple_items?"":$this_item->keywords.$ff_keywords?></textarea>
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<label for="notes">Notes<img width="16" height="16" class="tooltip" title="This a textual description" src="<?=site_url()?>img/tooltip.png"></label>
					<?php $ff_notes = isset($ff_notes)?' '.$ff_notes:''?>
					<textarea type="text" name="notes" id="notes"<?=$multiple_items?" disabled":""?> class="input<?=$multiple_items?" disabled_input":""?>"><?=$multiple_items?"":$this_item->description.$ff_notes?></textarea>
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<label for="category">Category<img width="16" height="16" title="This is the category of the item" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<select name="category" id="category" class="select<?=$multiple_items?" first_input":""?>">
						<?=$multiple_items?'<option value="-1" SELECTED>Choose A New Category</option>':''?>
						<?php foreach ($categories->result() as $row) { ?>
						<option value="<?=$row->id?>"<?=(!$multiple_items&&$row->id==$this_item->category_id)?" SELECTED":""?>><?=htmlspecialchars($row->name)?></option>
						<?php } ?>
						<option value="0"<?=(!$multiple_items&&0==$this_item->category_id)?" SELECTED":""?>>No Category</option>
					</select>
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<label>Action Date<img width="16" height="16" title="This is the action date of the item" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" name="action_date" id="action_date" class="input" value="<?=($multiple_items||$this_item->action_date==null)?'':date("m/d/Y",$this_item->action_date)?>"/>
					<div class="clear"></div>
				</div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<input type="hidden" name="merge" value="yes">
				<span><input type="submit" name="submit" id="submit" value="Add to Existing File"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
			<?php foreach($items as $item){ ?>
				<input type="hidden" value="<?=$item?>" name="items[]"/>
			<?php }?>
			<input type="hidden" name="edit_item" value="true"/>
			<input type="hidden" name="edit_time" value="<?php echo $last_edit_time; ?>" />
		</form>
	</div>
</div>
