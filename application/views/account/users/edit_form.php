<?=$title?>

<p><?=$this->session->flashdata('edit');?></p>

<?php echo validation_errors(); ?>

<?=form_open($action)?>

	<table>
	  <tr>
	    <td><label for="user_first_name">First name</label></td>
	
	    <td><input id="user_first_name" name="user[first_name]" size="24" type="text" value="<?php echo set_value('user[first_name]', $user->first_name); ?>"/></td>
	  </tr>
	  <tr>
	    <td><label for="user_last_name">Last name</label></td>
	    <td><input id="user_last_name" name="user[last_name]" size="24" type="text" value="<?php echo set_value('user[last_name]', $user->last_name); ?>" /></td>
	  </tr>
	  <tr>
	    <td><label for="user_email">Email</label></td>
	
	    <td><input id="user_email" name="user[email]" size="24" type="text" value="<?php echo set_value('user[email]', $user->email_address); ?>" /></td>
	  </tr>
	</table>
	
	<fieldset class="credentials">
	
	  <legend>Optionally, change the password</legend>
	
	  <div id="username_entry" >
	  	
	    <p class="field">
	      <label for="signup_password">Password</label><br />
	      <input id="signup_password" name="user[password]" size="30" type="password"/><br />
	      6 characters or longer with at least one number is safest.
	    </p>
	  
	    <p class="field">
	      <label for="signup_password_confirmation">Enter your password again for verification</label><br />
	      <input id="signup_password_confirmation" name="password_confirmation" size="30" type="password" />
	  </div>
	  
	  
	</fieldset>

<input type="submit" value="<?=$title?>"/>

</form>