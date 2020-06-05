	<div class="status_data">
		<h2 style="font-size:24pt; margin:20px 20px 10px 0px;"><?=$org_count?> Total Organizations, <?=$user_count?> Total Users</h2>
	</div>
	<table>
	<tr><td>Package</td><td>Subscribers</td><tr>
	<?php
	foreach($packages->result() as $package)
	{
		?>
		<tr><td><?=$this->Package_model->get_package($package->package_id)->name?></td><td><?=$package->total?></td></tr>
		<?php
	}
	?>
	</table>
	
	<div id="table_header_bar" class="status_table">
		<div id="header_bar_left" class="header_locations">Organizations List</div>
		<div class="clear"></div>
	</div>
	<table cellspacing="0" id="status_table" class="grid">
		<thead>
		<tr class="th_row">
			<th>Org Name</th>
			<th>Date Created</th>
			<th>Owner</th>
			<th>Email</th>
			<th>Databases</th>
		</tr>
		</thead>
		<tbody>
<?php $count = 0;?>
<?php foreach ($owners->result() as $row) { ?>
<?php $count++;?>
		<tr class="full_row">
			<td><?=$row->name ?></td>
			<td><?=gmdate('M j, Y', $row->date_added) ?></td>
			<td><?=$row->first_name ?> <?=$row->last_name ?></td>
			<td><a href="mailto:<?=$row->email_address ?>"><?=$row->email_address ?></a></td>
			<td><?=$quantity[$row->id]?></td>
		</tr>
<?php }?>
	</tbody>
	</table>
