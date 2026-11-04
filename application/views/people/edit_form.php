<?php
ini_set('display_errors',0); 
error_reporting(0); // was error_reporting('NONE'): PHP 7 read it as 0, PHP 8 throws a TypeError
?>

<style>
p, ol, #db_import_tool li, #db_import_tool label {font-family:arial; font-size:.9em; margin-bottom:1em;}
#db_import_tool img{border:solid 1px gray;}

h4.name, h5.database, h6.location{
	float:left;
	margin-right: 20px;
	width:150px;
}

h5.database {
	margin-left:20px;
}

h6.location {
	float:left;
	margin-left:40px;
	color:gray;
}

ul h3 {
	font-weight:normal;
}

p.description {
	display:inline;
}

h3.user_name{
	display:block;
	width:220px;
	float:left;
	font-weight:normal;
}


li.database_container {
	border-bottom:solid 1px gray;
}

li.location:nth-child(even) {
	background:white;
}

li.location{
	border-top:solid 1px gray;
}

input[type=checkbox]{
	margin-left:20px;
}

ul.triState {
	width:100px;
	float:left;
	display:block;
}

ul.triState2 {
	display:block;
	width:470px;
}

div.user_permissions_container {
	padding-top:10px;
	padding-left:10px;
	font-family: arial;
	background:#ececec;
}

ul.databases_list_read, ul.databases_list_write, ul.locations_container{
	display:none;
}

ul.triState li {
	padding-bottom:8px;
}

ul.triState li label {
	float:left;
}

span.all_dbs{
	margin-left:25px;
	font-weight:bold;
}

span.db_name{
	margin-left:25px;
	font-weight:bold;
}

span.loc_name{
	margin-left:25px;
	font-weight:normal;
	color:gray;
}

a.expand_link{
	display:inline;
	font-size:.8em;
	margin-left:20px;
}

ul.triState ul{
	border-top:solid 1px gray;
	margin-top:5px;
	padding-top:10px;
}

ul.triState ul ul{
	border-top:none;
	margin-top:0;
	padding-top:8px;
}

li.locations_container{
	margin-bottom:5px;
}

ul.locations_container li {
	display:block;
	margin-bottom:8px;
}

#footnote {
	padding-top:10px;
	font-size:.9em;
}

.inputs_container {
	font-family:arial;
	display:block;
	padding: 10px 5px;
	background:#ececec;
	margin-bottom:20px;
}

.inputs_container input[type=text], .inputs_container input[type=password] {
	height:20px;
	width:400px;
	font-size:1em;
	font-family:arial;
}

.inputs_container label {
	display:inline-block;
	width:100px;
	margin-bottom:20px;
	font-weight:bold;
	font-size:.9em;
}

.inputs_container label.last {
	margin-bottom:0;
}

h3.db_tools_subheading {
	margin-bottom:10px;
	font-size:1.3em;
}


#db_tools_page #submit_container {
	padding-top:10px;
	border-top: solid 2px black;
	font-family:arial;
}

</style>

<script>
	
	$(document).ready(function(){
		
		$('ul.triState').tristate({
			heading: 'h3'
		});
		
		$('a.show_dbs').click(function(event){
			event.preventDefault();
			if ($(this).text() == '(show databases)') {
				$(this).text('(hide databases)');
			}
			else {
				$(this).text('(show databases)');	
			}
			
			//var userid = $(this).next().next('ul.databases_list_write').attr('class').split(' ').slice(0);
			$('ul.databases_list').toggle();
		});
		
		$('a.show_locs').click(function(event){
			event.preventDefault();			
			if ($(this).text() == '(show locations)') {
				$(this).text('(hide locations)');
			}
			else {
				$(this).text('(show locations)');	
			}			
			var dbid = $(this).next().next('ul.locations_container').attr('class').split(' ').slice(0);
			$('ul.' + dbid).toggle();
		});
		
	});
	
	
</script>
	
	<div id="db_tools_page_wrapper">
	
		<div id="db_tools_page">			
				
				<h2>Edit <em><?=$user->first_name;?> <?=$user->last_name;?></em></h2>
				
				<p>Here you can edit this user's information and give or take away permissions.</p>

<form action="<?=site_url()?>people/do_edit" method="POST" class="form" name="edit_user">
	
				<!-- <p>Change this user's name</p> -->
				<h3 class="db_tools_subheading">Change this user's first and last name:</h3>
				<div class="inputs_container">
		          <label for="name">First Name</label>
		          <input type="text" class="inline_input input" name="first_name" id="first_name" value="<?php echo set_value('user[first_name]', $user->first_name); ?>"/><br>
		          <label class="last" for="description">Last Name</label>
		          <input type="text" class="inline_input input" name="last_name" id="last_name" value="<?php echo set_value('user[last_name]', $user->last_name); ?>" />
                </div> <!-- .inputs_container -->
<?php if($user_data['id'] == $user->id): ?>
<h3 class="db_tools_subheading">Change the email address associated with this account</h3>
<div class="inputs_container">
					<label for="email">Current Email</label>
                    <input type="text" class="inline_input input" value="<?php echo set_value('email', $user->email_address); ?>" disabled/><br />
					<label class="last" for="email">New Email</label>
					<input type="text" class="inline_input input" name="email" id="email" />
</div>
<?php endif;?>
          		
   				<!-- <p>Optional Password Change</p> -->
				<h3 class="db_tools_subheading">Change this user's password:</h3>
				<div class="inputs_container">
			        <label for="password">Password</label>
			        <input type="password" class="inline_input input" name="password" id="password"/><br>
			        <label class="last" for="password_confirmation">Confirm</label>
			        <input type="password" name="password_confirmation" id="password_confirmation"/>
				</div>
      
      				<?php if($organization_admin->admin_user != $user->id): ?>

				<h3 class="db_tools_subheading">Set what databases and locations this user can access:</h3>

				<div class="user_permissions_container">

						<h3 class="user_name"><strong><?=$user->first_name." ".$user->last_name?></strong> can...</h3>
						<!--View only -->
						<ul class="triState">
							<li><h3 class="view_label">View</h3>
								<div class="clear"></div>
								<ul class="databases_list databases_list_read">
								<?php
								foreach($databases->result() as $dbs){
									$db_permissions=$this->Permissions_model->get_by_database($dbs->id,$user->id);
									if($db_permissions) {
									if($db_permissions->pto_umask !== NULL) {
										if($db_permissions->pto_umask == 0) {
											$db_read='';
										} elseif ($db_permissions->pto_umask == 2) {
											$db_read="checked";
										} elseif ($db_permissions->pto_umask == 3) {
											$db_read='';
										} elseif ($db_permissions->pto_umask == 1) {
											$db_read='';
										}else {
											$db_read='';
										}
									} else {
											$db_read='';
									}
								} else {
									$db_read='';
								}

								?>
									<li class="locations_container"><input type="checkbox" name="<?="db_r_new_".$dbs->id?>" id="<?="db_r_new_".$dbs->id?>" value="yes" <?=$db_read?>/><label href="#" for="<?="db_r_new_".$dbs->id?>" class="label">View</label>
										
										<div class="clear"></div>
										<ul class="<?=$dbs->id?> locations_container">
												<?php
												$locations=$this->Location_model->get_by_database($dbs->id);
												foreach($locations->result() as $locs) {
													$loc_permissions=$this->Permissions_model->get_by_location($locs->id,$user->id);
													if($loc_permissions) {
														if($loc_permissions->pto_umask !== NULL) {
															if($loc_permissions->pto_umask == 0) {
																$loc_read='';
															} elseif ($loc_permissions->pto_umask == 2) {
																$loc_read="checked";
															} elseif ($loc_permissions->pto_umask == 3) {
																$loc_read='';
															} elseif ($loc_permissions->pto_umask == 1) {
																$loc_read='';
															}else {
																$loc_read='';
															}
														} else {
																$loc_read='';
														}
													} else {
														$loc_read='';
													}
												?>
													<li><input type="checkbox" name="<?="loc_r_new_".$locs->id?>" value="yes" <?=$loc_read?>/><label href="#" for="<?="loc_r_new_".$locs->id?>" class="label">View</label><div class="clear"></div></li>
												<?php
												}
												?>	
										</ul>
									</li>
								<?php
								}
								?>
								</ul>
							</li>
						</ul>
						<!-- View and edit -->	
						<ul class="triState2 triState">
							<li><h3>View <strong><em>and Edit</em></strong> <span class="all_dbs">All Databases*</span></h3> <a href="#" class="show_dbs expand_link">(show databases)</a>
								<div class="clear"></div>
								<ul class="databases_list databases_list_write">
								<?php
								foreach($databases->result() as $dbs){
									$db_permissions=$this->Permissions_model->get_by_database($dbs->id,$user->id);
									if($db_permissions) {
									if($db_permissions->pto_umask !== NULL) {
										if($db_permissions->pto_umask == 0) {
											$db_write='';
										} elseif ($db_permissions->pto_umask == 2) {
											$db_write='';
										} elseif ($db_permissions->pto_umask == 3) {
											$db_write="checked";
										} elseif ($db_permissions->pto_umask == 1) {
											$db_write="checked";
										}else {
											$db_write='';
										}
									} else {
											$db_write='';
									}
								} else {
									$db_write='';
								}
								?>
									<li class="locations_container"><input type="checkbox" name="<?="db_w_new_".$dbs->id?>" id="<?="db_w_new_".$dbs->id?>" value="yes" <?=$db_write?>/><label href="#" for="cboxA02" class="label">View <strong><em>and Edit</em></strong> <span class="db_name"><?=character_limiter($dbs->name,12,'...')?></span></label><a href="#" class="show_locs expand_link">(show locations)</a>
										
												<div class="clear"></div>
												<ul class="<?=$dbs->id?> locations_container">
													<?php
													$locations=$this->Location_model->get_by_database($dbs->id);
													foreach($locations->result() as $locs) {
													$loc_permissions=$this->Permissions_model->get_by_location($locs->id,$user->id);
													if($loc_permissions) {
														if($loc_permissions->pto_umask !== NULL) {
															if($loc_permissions->pto_umask == 0) {
																$loc_write='';
															} elseif ($loc_permissions->pto_umask == 2) {
																$loc_write='';
															} elseif ($loc_permissions->pto_umask == 3) {
																$loc_write="checked";
															} elseif ($loc_permissions->pto_umask == 1) {
																$loc_write="checked";
															}else {
																$loc_write='';
															}
														} else {
																$loc_write='';
														}
													} else {
														$loc_write='';
													}
												?>
														<li><input type="checkbox" name="<?="loc_w_new_".$locs->id?>" id="<?="loc_w_new_".$locs->id?>" value="yes" <?=$loc_write?>/><label href="#" for="cboxA04" class="label">View <strong><em>and Edit</em></strong>  <span class="loc_name"><?=character_limiter($locs->name,12,'...')?></span></label><div class="clear"></div></li>
													<?php
													}
													?>	
										</ul>
									</li>
								<?php
								}
								?>
								</ul> <!-- .databases_list -->
							</li>
						</ul>
						
                            <h3 style="font-size:.8em; margin-top:10px; padding-bottom:10px; color:gray;">If you don't want a user to see a certain database or location, just uncheck both of the corresponding checkboxes!</h3>
						<div class="clear"></div>
						
						</div> <!-- .user_permissions_container -->

				<p id="footnote"><strong>*</strong> By selecting the "View" or "View <strong><em>and Edit</em></strong>" options for all databases, this option also applies for databases created in the future.</p>

						<?php endif; ?>
				
				<div id="result"></div>
								<input type="hidden" name="user_id" value="<?=$user->id?>">	
			<div id="submit_container">	<span><input type="submit" name="submit" id="submit" value="Save Changes to this user"/></span><span class="small gray"><span>or</span><a href="<?=site_url()?>people" class="close red">Cancel</a></span>	<?php if($user_data['id'] !== $user->id): ?>
		          <span id="trashcan" class="small right"><img src="<?=site_url()?>img/trashcan.png" alt="trashcan"/><a href="<?=site_url()?>people/delete/<?=$user->id?>" class="red" rel="facebox_fb modal_fb">Delete This User</a></span>
		        <?php endif; ?></div>
			
			<div class="clear"></div>
			
						</form>
						
		</div> <!-- #db_tools -->
	
	</div> <!-- #db_tools_wrapper -->	
