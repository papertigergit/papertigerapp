Search Results for "<?=$org_name?>"
<table>
<tr><td>Org ID</td><td>Organization Name</td><td>Admin User email</td><td>Current Plan</td></tr>
<?php
foreach($organizations->result() as $org_data) {
	$admin_user=$this->User_model->get_user($org_data->admin_user)->row()->email_address;
	?>
	<tr><td><a href="<?=site_url()?>change_plan/plans/<?=$org_data->id?>"><?=$org_data->id?></a></td><td><?=$org_data->name?></td><td><?=$admin_user?></td><td><?=$this->Package_model->get_by_id($org_data->package_id)->row()->plan_description?> -- $ <?=$this->Package_model->get_by_id($org_data->package_id)->row()->price?></td></tr>
	<?php
}
?>
</table>
