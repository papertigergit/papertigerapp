<?php
if( ($user_data['id'] == 3) || ($user_data['id'] == 1) || ($user_data['id'] == 105) || ($user_data['id'] == 6)  || ($user_data['id'] == 249489) || ($user_data['id'] == 1862) || ($user_data['id'] == 9132) || ($user_data['id'] == 5894) || ($user_data['id'] == 248436)) 
{
?>
<form method="POST" action="<?=site_url()?>change_plan/do_change">
Organization ID : <input type="text" id="org_id" name="org_id" value=<?=$org_id?>>
<select name="select">
<?php
foreach($package_plans->result() as $package_plan) {
	?>
	<option value="<?=$package_plan->id?>"><?=$package_plan->plan_description?> -- $ <?=$package_plan->price?></option>
	<?php
}
?>
</select>
<input type="submit" name="submit" value="Change Plan!">
</form>
or
<form method="POST" action="<?=site_url()?>change_plan/search">
Admin email address
<?php
$org_name='';
get_instance()->load->helper('date_helper');
?>
<input type="text" name="org_name" value="<?=$org_name?>">
<input type="submit" name="submit" value="Search for Organization by Email">
</form>
<?php

	if($changed_plans->num_rows() == 0) {
		echo "<br>No plans changed yet!<br>";
	} else {
		?>
		<table>
		<tr><td colspan='2'>Plan Changed By</td><td>Organization Name</td><td>Old Plan</td><td>New Plan</td><td>Time Stamp</td></tr>
		<?php
		foreach($changed_plans->result() as $changed_plan) {
			 $first_name=$this->User_model->get_user($changed_plan->changed_by)->row()->first_name ?? "";
			 $last_name=$this->User_model->get_user($changed_plan->changed_by)->row()->last_name ?? "";
			 $organization_name=$this->Organization_model->get_org_by_id($changed_plan->org_id)->row()->name ?? "";
			 $old_plan_description=$this->Package_model->get_package($changed_plan->from_plan)->plan_description;
			 $old_plan_price=$this->Package_model->get_package($changed_plan->from_plan)->price;
			 $new_plan_description=$this->Package_model->get_package($changed_plan->to_plan)->plan_description;
			 $new_plan_price=$this->Package_model->get_package($changed_plan->to_plan)->price;
			?>
			<tr><td><?=$first_name?></td><td><?=$last_name?></td><td><?=$organization_name?></td><td><?=$old_plan_description?> (<?=$old_plan_price?>) </td><td><?=$new_plan_description?> (<?=$new_plan_price?>) </td><td><?=unix_to_human($changed_plan->ts)?></td></tr>
			<?php
		}
		?></table><?php
	}
}
else{
	redirect('/');
}
