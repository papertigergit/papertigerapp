<?php
$this->load->helper('text');
?>
<script>
	$('<?php foreach($trash as $t){?>#trash_<?=$t['id']?>,<?php }?>').addClass('toDelete').animate({backgroundColor: 'pink'}, 1000);
</script>
<div id="delete_trash_form" class="facebox_form">
	<?php if(count($trash)>1){?>
		<div id="facebox_header"><div>Deleting Multiple Items</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<?php } else{?>
		<div id="facebox_header"><div>Deleting Item: <?=character_limiter($trash[0]['name'],22,'...')?></div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>		
	<?php }?>
	<div id="facebox_content">
		<p id="facebox_body">Are you sure you want to delete the following <?=count($trash)>1?'items':'item'?>:</p>
		<ul id="facebox_list">
		<?php foreach($trash as $t){?>
			<li><?=$t['name']?></li>
		<?php }?>
		</ul>
		<div id="facebox_footer">
			<form action="<?=site_url()?>trash/delete/" method="POST" class="form" name="delete_trash">
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<?php foreach($trash as $t){ ?>
							<input type="hidden" value="<?=$t['id']?>" name="trash[]"/>
						<?php }?>
						<input type="hidden" value="true" name="delete" />
						<input type="submit" class="font_normal" value="Delete"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
