<script>
	$('<?php foreach($items as $item){?>#item_<?=$item['id']?>,<?php }?>').addClass('toDelete').animate({backgroundColor: 'pink'}, 1000);
</script>
<?php get_instance()->load->helper('text_helper'); ?>
<div id="delete_item_form" class="facebox_form">
	<?php if(count($items)>1){?>
		<div id="facebox_header"><div>Tossing Multiple Items</div></div>
	<?php } else{?>
		<div id="facebox_header"><div>Tossing Item: <?=character_limiter($items[0]['name'],37,'...')?></div></div>
	<?php }?>
	<div id="facebox_content">		
		<p id="facebox_body">Are you sure you want to toss the <?=count($items)>1?'items':'item'?>:</p>
		<ul id="facebox_list">
		<?php foreach($items as $item){
			$item_name=$item['name'];
			echo "<li>".word_wrap($item_name,40)."</li>";
		 }?>
		</ul>
		<form action="<?=site_url()?>items/delete/" method="POST" class="form" name="delete_item" style="max-width:450px">
			<div id="form_padding">
				<?php
				$reminder_status=$this->Settings_model->get_all();
				foreach($reminder_status->result() as $row)
				{
					if($reminder_status->num_rows() == 0 || $row->confirmations_value == 1)
					{
					?>
					<div class="form_divider radio_list">
						<p>Should we remind you to remove this item by requiring a confirmation?</p>
						<div><input type="radio" checked name="confirmation" id="confirmYes" value="yes" /><label for="confirmYes" class="radioLabel">Yes, please! (recommended)</label></div>
						<div><input type="radio" name="confirmation" id="confirmNo" value="no" /><label for="confirmNo" class="radioLabel">No thanks, I'll remember!</label></div>
					<div class="clear"/>
					</div>
				<?php
					}
					if($row->confirmations_value == 0)
					{
						?>
						<div class="form_divider radio_list">
							<p>Should we remind you to remove this item by requiring a confirmation?</p>
							<div><input type="radio" name="confirmation" id="confirmYes" value="yes" /><label for="confirmYes" class="radioLabel">Yes, please! (recommended)</label></div>
							<div><input type="radio" checked name="confirmation" id="confirmNo" value="no" /><label for="confirmNo" class="radioLabel">No thanks, I'll remember!</label></div>
						<div class="clear"/>
						<?php
					}
				}
				if($reminder_status->num_rows() == 0) 
				{
					?>
					<div class="form_divider radio_list">
						<p>Should we remind you to remove this item by requiring a confirmation?</p>
						<div><input type="radio" checked name="confirmation" id="confirmYes" value="yes" /><label for="confirmYes" class="radioLabel">Yes, please! (recommended)</label></div>
						<div><input type="radio" name="confirmation" id="confirmNo" value="no" /><label for="confirmNo" class="radioLabel">No thanks, I'll remember!</label></div>
					<div class="clear"/>
					<?php

				}
				?>
			
			</div>
			<?php foreach($items as $item){ ?>
				<input type="hidden" value="<?=$item['id']?>" name="items[]"/>
			<?php }?>
			<input type="hidden" value="true" name="delete" />
			<div id="result"></div>
			<div id="facebox_footer">			
				<div class="right" >
						<div class="small" style="vertical-align:middle;float:left;"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<input type="submit" class="font_normal" value="Toss" style="float:left"/>
				</div>
				<div class="clear"></div>
			</div>
		</form>
	</div>
</div>
