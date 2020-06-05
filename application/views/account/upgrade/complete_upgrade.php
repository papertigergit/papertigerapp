Thank you for upgrading your account to the <?=$package->name?> Package!

<br/><br/>
You now have the following features availble to you!
<br/><br/>

<?php if($extra_users == 1): ?>
You can now create an additional user account by going to <br>
<?php elseif($extra_users > 1): ?>
You can now create <?=$extra_users?> additional user accounts by going to <br>
<?php endif; ?>