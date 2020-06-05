<?php
if( ($user_data['id'] == 3) || ($user_data['id'] == 249489) || ($user_data['id'] == 1) || ($user_data['id'] == 5894) || ($user_data['id'] == 9132) || ($user_data['id'] == 6)  || ($user_data['id'] == 1862) || ($user_data['id'] == 248436)) 
{
	if($messeges->num_rows() == 0):
		echo "No Messages";
	endif;	
	if($messeges->num_rows() > 0):
		?>
		<table>
		<tr><td>id</td><td>Title</td><td>Body</td><td>Text</td><td>Link Anchor</td><td>Activate/Deactivate</td></tr>
		<?php
		foreach($messeges->result() as $msg){
			$yes="<input type=\"submit\" name=\"submit\" value=\"On\">";
			?>
                <tr><td><a href="<?=site_url()?>messeges/delete/<?=$msg->id?>">Delete</a> <?=$msg->id?></td><td><?=$msg->title?></td><td><?=$msg->body?></td><td><?=$msg->text?></td><td><?=$msg->anchor?></td>
			<td>
			<form method="post" action="<?=site_url()?>/messeges/activate">
				<select name="select">
			<?php 
			foreach($packages->result() as $package_plan) {
				?>
				<option value="<?=$package_plan->id?>"><?=$package_plan->plan_description?> -- $ <?=$package_plan->price?></option>
				<?php
			}
				echo "</select>";
				echo $yes; 
				?>
				<input type="hidden" name="msg_id" value="<?=$msg->id?>">
			</form>
			<?php

				$plan_id=$this->Messeges_model->get_from_package($msg->id);
				if($plan_id->num_rows() > 0):
						echo "<br>Active For Plans:<br>";
					foreach($plan_id->result() as $key) {
						echo "<br>";
						print($package_name=$this->Package_model->get_by_id($key->id)->row()->plan_description);
						echo "<br>";
						?>
						<form method="post" action="<?=site_url()?>/messeges/deactivate">
							<input type="hidden" name="plan_id" value="<?=$key->id?>">
							<input type="submit" name="submit" value="Deactivate">
						</form>	
						<?php
						echo "<br>";
					}
				endif;	


			?>


			</td></tr>
			<?php
		}
		?>
		</table>
		<?php
	endif;	
	?>
		<form action="<?=site_url()?>/messeges/insert" method="post">
		<table>
		<tr><td>Title</td><td><input type="text" name="title" size="100"></td></tr>
		<tr><td>Body</td><td><textarea name="body" cols="70"></textarea>
		<tr><td>Anchor</td><td><input type="text" name="link_anchor" size="100"></td></tr>
        <tr><td>Text</td><td><input type="text" name="text" size="100"></td></tr>
		<table>
		<input type="submit">
		</form>
	<?php
}
else{
	redirect('/');
}
