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
	width:200px;
	float:left;
	font-weight:normal;
}

div.locations{
/*	background:lightpink;*/
}

li.database_container:nth-child(odd) {
	background:lightgray;
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
	width:500px;
}

div.user_permissions_container {
	border-top:solid 1px gray;
	padding-top:10px;
	font-family: arial;
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
	border-top:solid 1px gray;
	padding-top:20px;
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
			
			var userid = $(this).next().next('ul.databases_list_write').attr('class').split(' ').slice(0);
			$('ul.' + userid).toggle();
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
				
				<h2>Set User Permissions</h2>
				
				<p>Permissions are used to control who can interact with your databases and locations. You can allow your users to see all databases and locations within your Paper Tiger account, or you can allow them to see only a selected group of databases and/or locations.</p>
                            <h3 style="font-size:.8em; margin-top:10px; padding-bottom:10px; color:gray;">If you don't want a user to see a certain database or location, just uncheck both of the corresponding checkboxes!</h3>
				<form id="permissions_grid" name="permissions_grid" action="permissions" method="post">
				<?php
				if ($databases->num_rows() == 0) {
					echo "No databases, create a database to get started";
				} else { 
				if ($organization_users->num_rows() <= 1){
					echo "No users to manage";
				} else {

					foreach($organization_users->result() as $users) {
						//show both active and inactive users
						?>
						
						<?php if($users->id != $organization_admin->admin_user): ?>	
						<div class="user_permissions_container">
						<h3 class="user_name"><strong><?=$users->first_name." ".$users->last_name?></strong> can...</h3>
						<!--View only -->
						<ul class="triState">
							<li><h3 class="view_label">View</h3>
								<div class="clear"></div>
								<ul class="<?=$users->id?> databases_list_read">
								<?php
								foreach($databases->result() as $dbs){
									$db_permissions=$this->Permissions_model->get_by_database($dbs->id,$users->id);
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
									<li class="locations_container"><input type="checkbox" name="<?="db_r_".$users->id."_".$dbs->id?>" id="<?="db_r_".$users->id."_".$dbs->id?>" value="yes" <?=$db_read?> /><label href="#" for="<?="db_r_".$users->id."_".$dbs->id?>" class="label">View</label>
										
										<div class="clear"></div>
										<ul class="<?=$users->id?>_<?=$dbs->id?> locations_container">
												<?php
												$locations=$this->Location_model->get_by_database($dbs->id);
												foreach($locations->result() as $locs) {
													$loc_permissions=$this->Permissions_model->get_by_location($locs->id,$users->id);
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
													<li><input type="checkbox" name="<?="loc_r_".$users->id."_".$locs->id?>" value="yes" <?=$loc_read?>/><label href="#" for="<?="loc_r_".$users->id."_".$locs->id?>" class="label">View</label><div class="clear"></div></li>
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
								<ul class="<?=$users->id?> databases_list_write">
								<?php
								foreach($databases->result() as $dbs){
									$db_permissions=$this->Permissions_model->get_by_database($dbs->id,$users->id);
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
									<li class="locations_container"><input type="checkbox" name="<?="db_w_".$users->id."_".$dbs->id?>" id="<?="db_w_".$users->id."_".$dbs->id?>" value="yes" <?=$db_write?>/><label href="#" for="cboxA02" class="label">View <strong><em>and Edit</em></strong> <span class="db_name"><?=character_limiter($dbs->name,12,'...')?></span></label><a href="#" class="show_locs expand_link">(show locations)</a>
												<div class="clear"></div>
												<ul  class="<?=$users->id?>_<?=$dbs->id?> locations_container">
												<?php
												$locations=$this->Location_model->get_by_database($dbs->id);
												foreach($locations->result() as $locs) {
													$loc_permissions=$this->Permissions_model->get_by_location($locs->id,$users->id);
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
													<li><input type="checkbox" name="<?="loc_w_".$users->id."_".$locs->id?>" id="<?="loc_w_".$users->id."_".$locs->id?>" value="yes" <?=$loc_write?>/><label href="#" for="cboxA04" class="label">View <strong><em>and Edit</em></strong>  <span class="loc_name"><?=character_limiter($locs->name,12,'...')?></span></label><div class="clear"></div></li>
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
						
						</div> <!-- .user_permissions_container -->
						<div class="clear"></div>
						<?php endif; ?>

						<?php

					}
				}
				?>
				
				<p id="footnote"><strong>*</strong> By selecting the "View" or "View <strong><em>and Edit</em></strong>" options for all databases, this option also applies for databases created in the future.</p>
				
						<input type="submit" name="submit" value="Save User Permissions">
						</form>
				<?php } ?>
		</div> <!-- #db_tools -->
	
	</div> <!-- #db_tools_wrapper -->	
