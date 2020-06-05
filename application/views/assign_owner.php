<?php
if( ($user_data['id'] == 3) || ($user_data['id'] == 249489) || ($user_data['id'] == 1) || ($user_data['id'] == 105) || ($user_data['id'] == 6)  || ($user_data['id'] == 1862) || ($user_data['id'] ==9132) || ($user_data['id'] == 5894) || ($user_data['id'] == 248436)) 
{
get_instance()->load->helper('date_helper');
?>
<form method="POST" action="<?=site_url()?>imports/do_assign">
Organization ID : <input type="text" id="org_id" name="org_id" value=<?=$org_id?>>
<select name="select">
<?php
foreach($databases->result() as $dbs_to_assign) {
	?>
	<option value="<?=$dbs_to_assign->id?>"><?=$dbs_to_assign->name?> -- <?=$dbs_to_assign->id?></option>
	<?php
}
?>
</select>
<input type="submit" name="submit" value="Assign Database">
</form>
or
<form method="POST" action="<?=site_url()?>imports/search">
Admin email address
<?php
$org_name='';
?>
<input type="text" name="org_name" value="<?=$org_name?>">
<input type="submit" name="submit" value="Search for Organization by Email">
</form>
<?php

	if($assigned_databases->num_rows() == 0) {
		echo "No databases assigned yet";
	} else {
		?>
		<table>
		<tr><td colspan='2'>Assigned By</td><td>Assigned to ( Organization )</td><td>Database Name ( ID )</td><td>Time stamp</td></tr>
		<?php
		foreach($assigned_databases->result() as $assigned_database) {
			 $first_name=$this->User_model->get_user($assigned_database->assigned_by)->row()->first_name ?? "";
			 $last_name=$this->User_model->get_user($assigned_database->assigned_by)->row()->last_name ?? "";
			 $assigned_to=$this->Organization_model->get_org_by_id($assigned_database->assigned_to)->row()->name ?? "";
			 $database_name=$this->Database_model->get_by_id($assigned_database->object_id)->name ?? "";
			?>
			<tr><td><?= $first_name?></td><td><?=$last_name?></td><td><?=$assigned_to?> ( <?=$assigned_database->assigned_to?> )</td><td><?=$database_name?> (<?=$assigned_database->object_id?>) </td><td><?=unix_to_human($assigned_database->ts)?></td></tr>
			<?php
		}
		?></table><?php
	}
}
else{
	redirect('/');
}
