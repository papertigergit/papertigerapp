<div id="update_action_form" class="facebox_form">
	<div id="facebox_header"><div>Accept Confirmations</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">Are you sure you want to accept these confirmations? </p>
		<form action="<?=site_url()?>confirmations/do_accept" method="POST" class="form" name="accept_confirmation">
			<div class="form_divider">
				<ul id="facebox_list">
				<?php
				foreach($segs as $transfers_id){
					$item_id=$this->Transfers_model->get_by_id($transfers_id)->item_id;
					echo "<li>".$this->Item_model->get_by_id($item_id)->name;
					?>
                                        <input type="hidden" value="<?=$transfers_id?>" name="arr[]"/>
					<?php
				}
				?>
				</ul>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Confirm"/></span>
				<span class="small gray"><span>or</span><a href="#" class="red close">Cancel</a></span>
			</div>
		</form>
	</div>
</div>
