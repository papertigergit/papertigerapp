<h3>Add people, remove and manage people</h3>

<p><?=$this->session->flashdata('delete');?></p>

<br/><br/>

<?php if($users->num_rows() <= $package->max_users): ?>
<?=anchor('people/add', 'Add User', array('rel' => 'facebox nm'));?>
<?php endif; ?>

<br/><br/>
<?php foreach($users->result() as $user): ?>
	<div>
		<strong><?=$user->first_name?> <?=$user->last_name?></strong> <br/>
		<?=anchor('people/edit/'.$user->id, 'Manage')?><?php if($user->id != $this->user_data['id']): ?> | <?=anchor('people/delete/'.$user->id, 'Delete')?><?php endif;?>
	</div>
<?php endforeach; ?>