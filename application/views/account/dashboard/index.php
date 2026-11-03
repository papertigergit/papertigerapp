<style>
 .hover {
 	background: yellow;
 }
 
 th.option {
 cursor: help;
 }
</style>

		<div id="content_wrapper">

			<div id="table_header_bar">
				<div id="header_bar_left"><?php echo $user_data['organization_name']; ?> - Account</div>
				<div class="clear"></div>
			</div>
			
			<div id="section">
			
				<div id="section_title">
					<div id="section_title_left">
						<h2>Your account details</h2>
					</div>
					<div id="section_title_right">
					   <?php if($this->organization_data->subscription_id != "0"): ?>
                            Next charge is $<?=$this->subscription_data->nextBillAmount?> on <?php /* no subscription: PHP 7's date_format(null) printed nothing, PHP 8 throws */ echo ($this->subscription_data->nextBillingDate ?? null) instanceof DateTimeInterface ? date_format($this->subscription_data->nextBillingDate, "d M Y") : ''; ?> on <?=$cardType?? "" ?> <?=$cardLastFour?? "" ?> (<a href="<?=site_url();?>account/billing" rel="facebox nm">Update Billing Info</a>)
				        <?php endif; ?>
					</div>
					<div class="clear"></div>
				</div> <!-- #section_title -->
				
				<div id="account_owner">
					<h3>Account Owner</h3>
					<div id="account_owner_item">
						<img src="<?=site_url()?>img/people/user_icon.png" style="float:left;margin-right: 10px;"/>
						<h4 style="margin-bottom: 3px;"><?=$user_data['first_name']?> <?=$user_data['last_name']?></h4>
						<a href="mailto:<?=$user_data['email_address']?>" style="font-size: 13px;"><?=$user_data['email_address']?></a>
						
						<?php if($this->organization_data->admin_user == $this->user_data['id']): ?>
						<br/>
							<a href="<?=site_url()?>people/me"  style="color:red; font-size: 13px;">Edit user</a>
						
						<?php endif; ?>
						
					</div> <!-- end account_owner_item -->
				</div> <!-- end account_owner -->

				<div class="account_info">
					<div id="data_usage">
						<h3>Data Usage</h3>
						<div id="data_usage_item_contain">
							<div class="data_usage_item"><?=$num_databases;?><br/><span>Databases</span></div>
							<div class="data_usage_item"><?=$num_locations;?> <br/><span>Locations</span></div>
							<div class="data_usage_item"><?=$num_users->num_rows()?> <br/><span>Users</span></div>
							<div class="last data_usage_item"><?=$num_filed_items;?> <br/><span>Filed Items</span></div>
							<div class="clear"></div>		
						</div>	
					</div>				
					
					<div class="clear"></div>
				</div>
			
                <!-- <hr/> -->

                <br /><br />

				<h2>Upgrade your Paper Tiger Account:</h2>
				
				<script>
				
				$(document).ready(function() {
		         
		         // Destroy currrent tooltip if present
		         if($(this).data("qtip")) $(this).qtip("destroy");
		         
		          $("th.option").qtip({
		               style: {
		                  border: {
		                     width: 3,
		                     radius: 10
		                  },
		                  padding: 10, 
		                  textAlign: 'left',
                          tip: true,  
                          name: 'dark' // Style it according to the preset 'cream' style
		               }
		            });


				});						   
				</script>
				
				<table id="package_table">
				
					<thead>
						<th class="plan">Plans</th>
						<th class="tipped describe" title="A database is where your locations and items are stored.">Databases</th>
						<th class="tipped describe" title="A location is the physical place where you store your items. Examples: A file cabinet, a desk drawer, or a storage bin.">Locations</th>
						<th class="tipped describe" title="An item contains documents or files. For example, a hanging file folder.">Items</th>
						<th class="tipped describe" title="Reports can help you keep track of transfers, action items, and the contents of your database">Reports</th>
						<th class="tipped describe" style="align:center" title="Paper Tiger can generate tabs for your hanging file folders using Avery templates.">Tabs and Labels</th>
						<th class="tipped describe" title="Find First alerts you if you are filing an item that could go somewhere else to prevent duplicate information.">Find First</th>
						<th class="tipped describe" title="Advanced search allows you to do a detailed search based on a specific location, category, or action date.">Adv. Search</th>
						<th class="tipped describe" title="The number of users that can access your Paper Tiger account.">Users</th>
						<th class="tipped describe"><a href="http://www.thepapertiger.com/pricing">Check out our <br>Current Prices!</a></th>
						<th>&nbsp;</th>
					</thead>
					<tbody>
					
					<?php foreach($packages->result() as $package):
	
	  					$upgrade_button = base_url().'img/upgrade.png';
	  						if($package->billing_cycle=="month"){
	  							$billing_duration="/mo";
	  						}
	  						if($package->billing_cycle=="year"){
	  							$billing_duration="/yr";
	  						}

                    ?>

                        <?php
                         //Only show monthly packages, and DON'T show special packages   
                            if (($package->billing_cycle == 'month') && ($package->groups != '6') && ($package->groups != '98')):
                        ?>
								<tr>
									<td class="plan_name"><?=$package->plan_description;?><br /><span class="plan_description">$<?php echo substr($package->price, 0, -3).$billing_duration;?> or $<?=substr($this->Package_model->get_year_price($package->id),0, -3)?>/yr</span></td>
									<td class="option"><?php echo ($package->max_databases == -1) ? '<span class="unlimited"><img src="'.site_url().'img/account/unlimited.png" alt="Unlimited" width="20" height="11"/></span>' : $package->max_databases ;?></td>
									<td class="option"><?php echo ($package->max_locations == -1) ? '<span class="unlimited"><img src="'.site_url().'img/account/unlimited.png" alt="Unlimited" width="20" height="11"/></span>' : $package->max_locations ;?></td>
									<td class="option"><?php echo ($package->max_items == -1) ? '<span class="unlimited"><img src="'.site_url().'img/account/unlimited.png" alt="Unlimited" width="20" height="11"/></span>' : $package->max_items ;?></td>				
									<td class="option"><?php echo ($package->allow_reports == 1) ? '<span class="yes"><img src="'.site_url().'img/account/yes.png" alt="Yes" width="23" height="22" /></span>' : '<span class="no" style="color:red; font-style:bold;">Limited</span>' ;?></td>		
									<td class="option"><?php echo ($package->labels == 1) ? '<span class="yes"><img src="'.site_url().'img/account/yes.png" alt="Yes" width="23" height="22" /></span>' : '<span class="no" style="color:red; font-style:bold;">Limited</span>' ;?></td>
									<td class="option"><?php echo ($package->find_first == 1) ? '<span class="yes"><img src="'.site_url().'img/account/yes.png" alt="Yes" width="23" height="22" /></span>' : '<span class="no"><img src="'.site_url().'img/account/no.png" alt="No" width="23" height="22" /></span>' ;?></td>
									<td class="option"><?php echo ($package->advanced_search == 1) ? '<span class="yes"><img src="'.site_url().'img/account/yes.png" alt="Yes" width="23" height="22" /></span>' : '<span class="no"><img src="'.site_url().'img/account/no.png" alt="No" width="23" height="22" /></span>' ;?></td>
									<td class="option"><?php echo ($package->max_users == -1) ? '<span class="unlimited"><img src="'.site_url().'img/account/unlimited.png" alt="Unlimited" width="20" height="11"/></span>' : $package->max_users ;?></td>
									<td class="option">
							
									<?php
							
									if($package->active == 0) {
										echo '<img src="'.site_url().'img/account/coming_soon.png" alt="Coming Soon!" width="118" height="37"/>';
									}
							 
                                    elseif($package->groups == $organization_package->groups) {
                                        echo '<span style="font-style:bold;">Your Package</span>';
                                    //making sure beta can't upgrade to free
                                    }
                                    
                                    elseif(($package->id == '2') && ($organization_package->id == '13')) {
                                        echo '<span style="font-style:bold;">Your Package (special)</span>';
                                    //making sure beta can't upgrade to free
                                    }
                                    elseif(($package->id == '5') && ($organization_package->id == '15')) {
                                        echo '<span style="font-style:bold;">Your Package (special)</span>';
                                    //making sure beta can't upgrade to free
                                    }
                                    elseif(($package->id == '7') && ($organization_package->id == '17')) {
                                        echo '<span style="font-style:bold;">Your Package (special)</span>';
                                    //making sure beta can't upgrade to free
                                    }
                                    elseif(($package->id == '11') && ($organization_package->id == '16')) {
                                        echo '<span style="font-style:bold;">Your Package (special)</span>';
                                    //making sure beta can't upgrade to free
                                    }
                                    
                                    elseif (($organization_package->id == 4) && ($package->id == 1) || $package->price == 0) {
									    echo '<span class="gray">Not available for downgrade</span>';
				                    }
                                    elseif($package->level < $organization_package->level) {										          
                						echo '<a href="http://thepapertiger.com/contact">Contact to downgrade</a>';
                                    }
                                    
                                    else {

                                        if($this->organization_data->subscription_id != "0") {
						       				echo '<a href="'.site_url().'account/upgrade/checkout/'.$package->id.'" rel="facebox nm"><img src="'.site_url().'img/account/upgrade.png" alt="Upgrade!" width="118" height="37"/></a>';
                                        }
                                        
                                        else {
							       				echo '<a href="'.site_url().'account/billing/create/'.$package->name.'"><img src="'.site_url().'img/account/upgrade.png" alt="Upgrade!" width="118" height="37"/></a>';
										    }
										}
									
							
?>

                                <?php endif;?> 
					
									</td>
								</tr>
						
						<?php endforeach;?>
				
						</tbody>
				</table>
				
				<?php if($organization_package->id == '13'):?>
                <h3 style="margin-top:-10px; text-align:center; margin-bottom:2em; border-bottom: 2px solid black; padding-bottom:10px;">Upgrade to <a href="<?=site_url()?>account/upgrade/checkout/15" rel="facebox nm">Business Special for $159.60/yr</a> or Upgrade to <a href="<?=site_url()?>account/upgrade/checkout/16" rel="facebox nm">Elite Special for $327.60</a> or Upgrade to <a href="<?=site_url()?>account/upgrade/checkout/17" rel="facebox nm">Unlimited Special for $495.60</a></h3>
                <?php endif;?>	


				<?php if($organization_package->id == '4'):?>
                <h3 style="margin-top:-10px; text-align:center; margin-bottom:2em; border-bottom: 2px solid black; padding-bottom:10px;">Upgrade to <a href="<?=site_url()?>account/billing/create/proyearlyspecial">Pro Special for $75.60/yr</a> or Upgrade to <a href="<?=site_url()?>account/billing/create/businessyearlyspecial">Business Special for $159.60/yr</a> or Upgrade to <a href="<?=site_url()?>account/billing/create/eliteyearlyspecial">Elite Special for $327.60</a> or Upgrade to <a href="<?=site_url()?>account/billing/create/unlimitedyearlyspecial">Unlimited Special for $495.60</a></h3>
                <?php endif;?>	

                <div id="account_footer">
					
					<div id="account_footer_right">
					
						<h5>What happens if we upgrade, downgrade, or cancel in the middle of a billing cycle?</h5>
					
							<p>If you upgrade or downgrade you'll be charged the new rate for your new plan starting on your next billing cycle. If you cancel you'll be cancelled immediately and you won't be charged again.</p>
					
						
						
						<div id="credit_card_info">
						
						<h5>We accept</h5>
						
                    <img src="<?=site_url()?>img/signup/cards.png" alt="We accept Visa, MasterCard, American Express, and Discover." />

                    <p>We accept Visa, Mastercard, Discover, and American Express</p>
                </div>
						
							<div>
							<p id="login_help" style="text-align:left; margin-top: 10px;font-size: 12pt;"><a href="http://www.thepapertiger.com/privacy_policy.php" target="_blank">Privacy Policy</a> </p>	
	                        <p id="login_help" style="text-align:left; margin-top: 10px;font-size: 12pt;"> <a href="http://www.thepapertiger.com/terms_of_service.php" target="_blank">Terms Of Service</a> </p>	
							</div>
					</div> <!-- end account_footer_right -->

				<div id="account_footer_left">

						<h5>Rename your Organization</h5>
						
										
							<div class="footer_section">
								<p>
								Change your Organization name here
								</p>
								
								<?php echo form_open('account/organization/update'); ?>
	<input type="text" id="organization_name" name="organization_name" style=" width:275px; margin-right:20px;" value="<?=$this->organization_model->get_organization($user_data['organization_id'])->row()->name?>"><label for="organization_name" style="font-size: smaller;"></label> <input type="submit" name="rename_organization" value="Rename Organization"/>

								</form>
								
							</div>
							
					
						<h5>The Paper Tiger Newsletter</h5>
						<?php
						$newsletter=false;
						if($this->organization_model->get_organization($user_data['organization_id'])->row()->newsletter == 1) {
							$newsletter="checked=\"checked\"";
						}
						?>
							<?php echo form_open('account/organization/newsletter'); ?>
								<div class="footer_section" style="font-size: smaller;">
								<input type="checkbox" id="newsletter" name="newsletter" <?=$newsletter?>> <label for="newsletter">Yes, I would like to receive the periodic newsletter about The Paper Tiger.
</label>								<input type="submit" name="update_newsletter" value="Update"/>
								</div>
							</form>
						
					
					<?php if(($digital_allowed->id !=1) && ($digital_allowed->max_users ==1)): ?>

	<h5>Activate Digital Tiger with &nbsp; &nbsp;<img id="google_logo" src="<?=site_url()?>img/google-drive-logo-300x120.png" alt="Google Docs"/></h5>
		<div class="footer_section auth_warn">
		<?php
		if($google_users_data->num_rows() > 0) {
			echo anchor('account/organization/revoke_google_docs', '<font color=#FF0000>Disconnect your Paper Tiger Account from Google Drive.</font>');
		} else {
			echo anchor('account/organization/google_docs', 'Connect your Paper Tiger Account to Google Drive');
		}
		?>
	</div>
		<?php endif; ?><?php if($is_network->num_rows() > 0): ?>



							<h5>Want to make someone else the account owner?</h5>
							
							<div class="footer_section">
							<p>The account owner is the only person that can access this account page, upgrade, downgrade, change billing information, and cancel the account. The account owner also has permanent access to all databases. Once you make this change you'll no longer be the account owner.</p>
								<?php echo form_open('account/organization/change_admin'); ?>
							<select id="account_owner_change" name="account_owner">
							<?php
							foreach($org_users->result() as $user){
								?>
								<option value="<?=$user->id?>"><?=$user->first_name?> <?=$user->last_name?></option>
								<?php
							}
							?>
							</select>
							
							<input type="submit" id="account_owner_sumbit" name="account_owner_sumbit" value="Make this person the account owner">
							<form>
							
							<div style="clear:both;"></div>
							
							</div>
					<?php endif; ?>		

                    <?php if(!($this->user_data['affiliate_code'])):?>


						<h5>Would you like to become a Paper Tiger Online Affiliate?</h5>
						
							<div class="footer_section">
							
							<p>Refer your family, colleagues, or peers to start using Paper Tiger Online, and you can make a 15% commision (prorated for a year) on every person you refer! Signing up is free and only takes 60 seconds.</p>
							
							<?=anchor('affiliates', 'Sign up for the Paper Tiger Affiliate Program')?>
							
							</div>

                    <?php endif;?>

						<h5>Need to cancel your account?</h5>
						
							<div class="footer_section">
							
							<p>We'll be sorry to see you go. <strong>Once your account is cancelled, all your databases and information will be immediately and permanently deleted.</strong> If you have a paying account you won't be charged again after your official cancellation date. Please familiarize yourself with our refund policy.</p>
							
							<?=anchor('account/cancel', 'Please cancel my account and delete all my data', array('rel' => 'facebox nm', 'class' => 'red'))?>
							
							</div>
					</div> <!-- end account_footer_left -->
			
					<div style="clear:both;"></div>

				</div> <!-- end account_footer -->

			</div>
		
		</div> <!-- #content_wrapper -->
