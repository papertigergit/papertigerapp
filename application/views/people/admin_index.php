<link href="<?=site_url()?>/css/people.css" media="screen" rel="stylesheet" type="text/css"/>

<div id="dashboard">

    <div id="dashboard_right">

				<div class="tip">

					<div  style="margin-top:15px;" class="databases">Users in your Paper Tiger Account</div>

					<p style="margin-top:-10px;">
                        Here you can invite more users to your Paper Tiger account, as well as control who can see and change your information.
                    </p>
		    <?php $max_users=(array($packages->result_array())); ?>
			    
                    <h2 style="border-top:solid 1px gray; border-bottom:solid 1px gray; padding:5px 0; color:darkgreen; margin-top:20px;">There are <?=$users->num_rows();?> of <?php echo $max_users[0][0]['max_users']==-1?'<em>Unlimited</em>':$max_users[0][0]['max_users']; ?> seats occupied.</h2>
 
				</div>			

	</div>

<div id="dashboard_left">
	
	<div id="dashboard_header">Manage users for <em><?php print htmlspecialchars($this->Organization_model->get_organization($user_data['organization_id'])->row()->name)?></em> <a style="color:lightgray; float:right; font-size:.7em; margin-top:5px;" href="<?=site_url();?>permissions">Change permissions</a></div>

	<div id="dashboard_content">

		<?php $count = 0; foreach($users->result() as $user): ?>
		<div class="seat_container_full">
			<a href="<?=site_url().'people/edit/'.$user->id?>" ><img class="user_picture" src="<?=site_url()?>img/people/user_icon.png"/></a>
            <h3 class="person_name"><?=$user->first_name?> <?=$user->last_name?> <?php if($user_data['id'] == $user->id):?>(me)<?php endif;?></h3>
			<a href="mailto:<?=$user->email_address?>" class="person_email"><?=$user->email_address?></a><br/>
		<?php if($user->active == 1):?>
		<?php if($user_data['id'] == $user->id): ?>
			<?=anchor('people/me', 'Edit user', array('style' => 'color: red; font-size: 13px;'))?>
		<?php else: ?>	
			<?=anchor('people/edit/'.$user->id, 'Edit user', array('style' => 'color: red; font-size: 13px;'))?>
		<?php endif; ?>	
        <?php else:?>
			<?=anchor('people/edit/'.$user->id, 'Edit user (invited)', array('style' => 'color: gray; font-size: 13px;'))?>
	    <?php endif;?> 
		
		</div>
		<?php $count++?>
		<?php endforeach; ?>

		<?php for($i = 0; $i < $available_slots; $i++): ?>
		<div class="seat_container_empty">
			<a href="<?=site_url().'people/add'?>" ><img class="seat_available" alt="Click Here To Add User" src="<?=site_url()?>img/people/seat_available.png"></a>
		</div>
	
		<?php endfor;?>
		<?php if($max_users[0][0]['max_users'] == -1):  ?>
			<div class="seat_container_empty">
                <!--<a href="<?=site_url().'people/add'?>" >Add a bijillion more users...</a>-->
                <a href="<?=site_url().'people/add'?>" ><img class="seat_available" alt="Click Here To Add User" src="<?=site_url()?>img/people/seat_available.png"></a>
			</div>
        <?php endif ?>

        <div class="clear"></div>

        <?php if($max_users[0][0]['max_users'] == -1):  ?>
            <h3 style="color:gray; padding-top:20px; margin-bottom:20px;">Tip: Since you are using an unlimited account, you have an unlimited number of seats. You will always see one more open seat available than the number of users that you have.</h3>
		<?php endif ?>
		</div>
		<div class="clear"></div>

	</div> <!-- #dashboard_content -->

</div> <!-- #dashboard_left -->

<div class="clear"></div>
