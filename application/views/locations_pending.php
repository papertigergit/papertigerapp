<div id="delete_restore_form" class="facebox_form">	
	<div id="facebox_header"><div>Clear your pending transfers first!</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<form action="<?=site_url()?>trash/restore/" method="POST" class="form" name="restore_trash">
			<p id="facebox_body">The following items have transfers pending in this location. Please confirm or cancel the transfers first!</p>
			<ul id="facebox_list">
								<?php
				foreach($pending_locations->result() as $row)
				{
					$items=$this->Item_model->get_by_id($row->item_id);
					echo "<li>Item ".$items->slot_num." - ".$items->name."</li>";
				}
				?>
			</ul>
			<div id="result"></div>
			<div id="facebox_footer">
				<div class="right" >
						<div class="small" style="vertical-align:middle"><a href="#" class="close red">Go Back</a></div>
				</div>
				<div class="clear"></div>
		</div>
		</form>
	</div>
</div>
