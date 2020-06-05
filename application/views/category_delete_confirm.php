<script>
	$('<?php foreach($categories as $cat){?>#category_<?=$cat['id']?>,<?php }?>').addClass('toDelete').animate({backgroundColor: 'pink'}, 1000);
</script>
<div id="delete_category_form" class="facebox_form">
	<?php if(count($categories)>1){?>
		<div id="facebox_header"><div>Deleting Multiple Categories</div></div>
	<?php } else{?>
		<div id="facebox_header"><div>Deleting Category: <?=character_limiter($categories[0]['name'],37,'...')?></div></div>
	<?php }?>
	<div id="facebox_content">
		<p id="facebox_body">Are you sure you want to delete the following <?=count($categories)>1?'categories':'category'?>:</p>
		<ul id="facebox_list">
		<?php foreach($categories as $cat){?>
			<li><?=$cat['name']?></li>
		<?php }?>
		</ul>
		<div id="facebox_footer">
			<form action="<?=site_url()?>categories/delete/" method="POST" class="form" name="delete_category">
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<?php foreach($categories as $cat){ ?>
							<input type="hidden" value="<?=$cat['id']?>" name="cat[]"/>
						<?php }?>
						<input type="hidden" value="true" name="delete" />
						<input type="submit" class="font_normal" value="Delete"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
