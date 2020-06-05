<div id="ff_grid" class="facebox_form">
	<div id="facebox_header"><div>Choose a File</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<span class="red" style="display:block; margin-top:20px; margin-left:10px;">NOTE : Pending items are not shown.</span>
	<div id="facebox_content">
		<!--<p class="small" style="margin-bottom:15px;"><a class="red" href="<?=site_url()?>items/add/<?=$items[0]->location_id?>" rel="facebox_fb nm_fb">Go Back to adding a new file</a></p>-->
		<div id="form_padding">
			<div class="form_divider">
				<table cellspacing="0" id="find_first_table">
					<thead>
						<tr>
							<th></th>
							<th>Name</th>
							<th>Location</th>
							<th></th>
						</tr>
					</thead>
					<?php foreach($items as $item){?>
						<tr>
							<td><?php if(trim($item->keywords)!=''){?><img src="<?=site_url()?>img/addbuttoncircle.png"><?php }?></td>
							<td><?=htmlspecialchars($item->name)?></td>
							<td><?=htmlspecialchars($this->Location_model->get_by_id($item->location_id)->name)?></td>
							<td><a rel="nm_fb facebox_fb" href="/items/find_first_view/<?=$item->id?>">Merge</a></td>
						</tr>
						<tr class="keys_row keys_hidden">
							<td>&nbsp;</td>
							<td colspan="2">
								<div class="ff_keys_head">Keywords</div>
								<div class="ff_keys"><?=$item->keywords?></div>
							</td>
						</tr>
					<?php }?>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	$('a[rel$=modal_fb]').bind('click',function(){$.facebox.settings.modal=true;});
	$('a[rel$=nm_fb]').bind('click',function(){$.facebox.settings.modal=false;});
	$('a[rel*=facebox_fb]').facebox();
</script>
