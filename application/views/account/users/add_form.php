<!--
<?=$title?>

<?php echo validation_errors(); ?>

<?=form_open('people/'.$action)?>

	<table>
	  <tr>
	    <td><label for="user_first_name">First name</label></td>
	
	    <td><input id="user_first_name" name="user[first_name]" size="24" type="text" value="<?php echo set_value('user[first_name]'); ?>"/></td>
	  </tr>
	  <tr>
	    <td><label for="user_last_name">Last name</label></td>
	    <td><input id="user_last_name" name="user[last_name]" size="24" type="text" value="<?php echo set_value('user[last_name]'); ?>" /></td>
	  </tr>
	  <tr>
	    <td><label for="user_email">Email</label></td>
	
	    <td><input id="user_email" name="user[email]" size="24" type="text" value="<?php echo set_value('user[email]'); ?>" /></td>
	  </tr>
	</table>

	      <p class="instruction">
	        <strong>The user will receive an email invitation.</strong>
	        Please have the user check their email and follow the provided link to activate their account.
	      </p>

<input type="submit" value="<?=$title?>"/>

</form>
-->

<div id="add_location_form" class="facebox_form">
	<div id="facebox_header"><div>Add A New User</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">This user will have access to all of your organization's files.</p>
		<form action="<?=site_url().'people/do_add'?>" method="POST" class="form" name="add_user">
			<div id="form_padding">
				<div class="form_divider">
					<label for="name">First Name<img width="16" title="This is the first name of the user." height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="inline_input input" name="first_name" id="first_name"/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<label for="description">Last Name<img width="16" height="16" class="tooltip" title="This is the last name of the user." src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="inline_input input" name="last_name" id="last_name"/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<label for="capacity">E-mail<img width="16" height="16" class="tooltip" title="E-mail address of the user. They will receive the email to set up their account at this address." src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="inline_input input" name="email" id="email"/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Create This User"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
		</form>
	</div>
</div>