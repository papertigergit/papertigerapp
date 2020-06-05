<link href="<?=site_url()?>css/user_picker.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=site_url()?>js/signup.js"></script>

<div id="edit_user_form" class="facebox_form">
	<div id="facebox_header"><div>Manage User Slots</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">Please indicate the total number of user slots necessary. This includes the number included with the package. You will be billed for extra users starting your next invoice.</p>
		<form action="<?=site_url()?>account/slots/manage" method="POST" class="form" name="add_slot">
			<div id="form_padding">
			
	            <div id="user_picker">
	                <div id="minus"></div>
	
	                <div id="plus"></div>
	
	                <div id="display">
	                    <div id="quantity">
	                        <?php echo $package->max_users+$organization->extra_user_slots; ?>
	                    </div>
	
	                    <div id="label">
	                        SEATS
	                    </div>
	                </div>
	            </div>
	
	            <div class="c3"></div>
	
				<?php
					if($organization->extra_user_slots > 0):
						$price = (int)$package->price + ($this->config->item('cost_extra_user')*($organization->extra_user_slots-$package->max_users));
					else: 
						$price = (int)$package->price;
					endif;
				?>
	
	            <p id="price">$<?=$price?>/month</p>
	            <hr />
			
			</div>
			
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Update user slots"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
			<input type="hidden" name="num_users_input" id="num_users_input" value="<?php echo $package->max_users+$organization->extra_user_slots; ?>"/>
			<input type="hidden" name="manage_user_slots" value="true"/>
		</form>
				
	</div>
</div>