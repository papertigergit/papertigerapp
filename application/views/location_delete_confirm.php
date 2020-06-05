<script>
	$('<?php foreach($locations as $loc){?>#location_<?=$loc['id']?>,<?php }?>').addClass('toDelete').animate({backgroundColor: 'pink'}, 1000);
</script>
<div id="delete_location_form" class="facebox_form">

	<?php if(count($locations)>1){?>
		<div id="facebox_header"><div>Deleting Multiple Locations</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<?php } else{?>
		<div id="facebox_header"><div>Deleting Location: <?=character_limiter($locations[0]['name'],37,'...')?></div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<?php }?>

	<div id="facebox_content">
		
		<p id="facebox_body">Are you sure you want to delete the following <?=count($locations)>1?'locations':'location'?><br/><span class="red">AND</span> all items within:</p>
		<ul id="facebox_list">
		<?php foreach($locations as $loc){?>
			<li><?=$loc['name']?></li>
		<?php }?>
		</ul>
		
		<div id="facebox_footer">
			<form action="<?=site_url()?>locations/delete/" method="POST" class="form" name="delete_location">
			
			<div class="facebox_password_confirm" style="">
				<p>Enter your password to confirm this action</p>
				
				<div class="input_container">
				
					<input type="password" name="location_delete_password" id="db_name_field" />
				
				</div>
				
			</div>					


			<div id="result"></div>
				<div class="small red left">NOTE: You can't undo this!</div>
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Cancel</a><span class="gray spacing"> or </span></div>
						<?php foreach($locations as $loc){ ?>
							<input type="hidden" value="<?=$loc['id']?>" name="loc[]"/>
						<?php }?>	
						<input type="hidden" value="true" name="delete" />
						<input type="submit" class="font_normal" value="Delete"/>
				</div>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
