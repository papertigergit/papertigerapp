Search Results for "<?=$org_name?>"
<table>
<tr><td>Org ID</td><td>Organization Name</td><td>Admin User email</td></tr>
<?php
foreach($organizations->result() as $org_data) {
	$admin_user=$this->User_model->get_user($org_data->admin_user)->row()->email_address;
	?>
	<tr><td><a href="<?=site_url()?>imports/assign_owner/<?=$org_data->id?>"><?=$org_data->id?></a></td><td><?=$org_data->name?></td><td><?=$admin_user?></td></tr>
	<?php
}
?>
</table>
