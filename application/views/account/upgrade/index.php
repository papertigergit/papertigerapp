<h2>Upgrade/Downgrade Account</h2>

<br/><br/>
You are currently on the <?=$organization_package->name?> Package.
<br/><br/>
<?php if($upgrade_packages->num_rows > 0): ?>

<h4>Upgrade Paths</h4>

	<?=form_open("account/upgrade/checkout/")?>
	
		<select name="package_id">
		<?php foreach($upgrade_packages->result() as $package): ?>
			<option value="<?=$package->id?>"><?=$package->name?></option>
		<?php endforeach; ?>
		</select>
		
		<input type="submit" value="Proceed with upgrade" />
	</form>
	
<?php else: ?>

Looking to add more users? You made do so by going to: 

<?php endif; ?>


<?php if($downgrade_packages->num_rows > 0): ?>
<br/><br/>
<h4>Downgrade Paths</h4>

	<?=form_open("account/upgrade/checkout/")?>
	
		<select name="package_id">
		<?php foreach($downgrade_packages->result() as $package): ?>
			<option value="<?=$package->id?>"><?=$package->name?></option>
		<?php endforeach; ?>
		</select>
		
		<input type="submit" value="Proceed with downgrade" />
	</form>

<?php endif; ?>