<?php
      $packages = $this->Package_model->get_by_id($this->Organization_model->get_package_id($user_data['organization_id']));
      $package=(array($packages->result()));
	$show_notes=$this->Settings_model->get_all();
	$notes_settings=array($show_notes->result_array());
?>

<link href="<?=site_url()?>css/reports.css" media="screen" rel="stylesheet" type="text/css"/>

<?php if($locations->num_rows() > 0): ?>

<div id="db_tools_page_wrapper">
	<div id="db_tools_page">
		<h2>Reports</h2>		
		<div>
			<div>

				
				

					<div id="form_padding">
							<p>Paper Tiger Online can generate customized reports to help you manage your information. Select a report below and then tailor the report to meet your needs.</p>
						</div>
						
						

<div class="section">
						
							<form action="<?=site_url()?>reports/generate" method="POST" id="import_db_form" name="label_form" target="_blank">
						
								<h3>Item List Report <a class="reports_adv_options gray" id="reports_adv_options_toggle_fileList" href="#" title="Show advanced report options">Show advanced options</a></h3> 
								
								<div class="clear"></div>
								
								<p>See all the information contained in your database in a variety of ways.  Using the advanced options, this report can be fully customized to meet your needs.  For quick reference, a copy of this report can be kept at your desk, in your briefcase, on your file cabinet or close to any physical item (storage boxes, etc.) that you have indexed with The Paper Tiger.</p>
								
								<div id="reports_adv_options_container_fileList" class="reports_adv_options_container">
								
									<h4>Advanced Options</h4>
									
									<div class="field_container">						
										<label for="location_dropdown_file_list" class="dd_label">In location:</label>
										<select name="location_file_list[]" id="location_dropdown_file_list" multiple="multiple">
										<?php		
											$package_id=$this->Organization_model->get_package_id($this->user_data['organization_id']);
											$is_network=$this->Network_model->is_network($package_id);
											$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;

											if($is_network->num_rows() == 0):
												$loc_data = $this->Location_model->get_from_database()->result_array();
											endif;
											if($is_network->num_rows() > 0):
												$loc_data = $this->Location_model->get_from_network_database($this->session->userdata('database_id'))->result_array();
											endif;
											if($is_network->num_rows() > 0 && ($organization_admin == $this->user_data['id'])):
												$loc_data = $this->Location_model->get_from_database()->result_array();
											endif;
											

											foreach($loc_data as $loc) {
												$is_selected='';
												if($this->input->post('location')){
												foreach($this->input->post('location') as $selected_location)
												{
													if($selected_location==$loc['id'])
													{
														$is_selected="selected=\"selected\"";
														echo "";
													}
												}}	
													echo "<option $is_selected value=\"$loc[id]\">".htmlspecialchars($loc['name'])."</option>";
												}
										?>
										</select> <span class="gray">Leave blank to display items in all locations</span>
									</div>
									
									<div class="field_container">
										<label for="category_dropdown_file_list" class="dd_label">In category:</label>
										<?php
										$num_cats=$this->Category_model->get_from_database()->num_rows();
										if($num_cats == 0){
											?>
											<span style="color:red;">You have not added any categories yet.</span>
											<?php
										}
										if($num_cats > 0){
											?>
										<select name="category_file_list[]" id="category_dropdown_file_list" multiple="multiple">
										<?php
											$category_data = $this->Category_model->get_from_database()->result_array();
											foreach($category_data as $category) {
												$is_selected='';
												if($this->input->post('category')){
												foreach($this->input->post('category') as $selected_category)
												{
													if($selected_category==$category['id'])
													{
														$is_selected="selected=\"selected\"";
													}
												}}	
												echo "<option $is_selected value=\"$category[id]\">".htmlspecialchars($category['name'])."</option>";
											}	
										?>
										</select> <span class="gray">Leave blank to display items in all categories</span>
										<?php } ?>
									</div>
						
							<div class="field_container">
								<label for="daterangepicker" class="dd_label">Action Date:</label>
								<input id="daterangepicker" type="text" name="date_range" />			
							</div>						

							<div class="field_container">
								<label class="dd_label">Sort by:</label>
								<input type="radio" name="sort" value="slot_num" id="item_list_location_id" checked="checked"><label for="item_list_location_id">Item Number</label>
								<input type="radio" name="sort" value="name" id="item_list_name"><label for="item_list_name">Item Name (ABC)</label>
								<input type="radio" name="sort" value="action_date" id="item_list_date"><label for="item_list_date">Action Date</label> 
							</div>
							<?php
							$notes_active='';
							if($show_notes->num_rows() > 0) {
								if($notes_settings[0][0]['show_notes'] != 0 || $notes_settings[0][0]['show_notes'] == NULL)
								{
									$notes_active="checked=\"checked\"";
								}
							}
							?>
							<div class="field_container last">
								<label class="dd_label">Columns to display:</label>
								<input type="checkbox" name="item_list_keywords" id="item_list_keywords" checked="checked"><label for="item_list_keywords">Keywords</label>
								<input type="checkbox" name="item_list_notes" id="item_list_notes" <?=$notes_active?>><label for="item_list_notes">Notes</label>
								<input type="checkbox" name="item_list_category" id="item_list_category" checked="checked"><label for="item_list_category">Category</label>
								<input type="checkbox" name="item_list_action_date" id="item_list_action_date" checked="checked"><label for="item_list_action_date">Action Date</label>
							</div>
							
								</div>
								<div class="submit">
									<input type="hidden" name="report_type" value="file_list">
									<span><input type="submit" name="submit" id="submit_transfer" value="Generate Item List Report"/></span>
								</div>
							</form>
						
						</div> <!-- .section -->						


						<div class="section">
						
							<form action="<?=site_url()?>reports/generate" method="POST" id="import_db_form" name="label_form" target="_blank">
						
								<h3>Action Date Report <a class="reports_adv_options gray" id="reports_adv_options_toggle_actionDate" href="#" title="Show advanced report options">Show advanced options</a></h3> 
								
								<div class="clear"></div>
								
								<p>If you have set Action Dates while using Paper Paper Tiger, use this customizable report to see what is due by when.</p>
								
								<div id="reports_adv_options_container_actionDate" class="reports_adv_options_container">
								
									<h4>Advanced Options</h4>
									
									<div class="field_container">						
										<label for="location_dropdown_action_date" class="dd_label">In location:</label>
										<select name="location_file_list[]" id="location_dropdown_action_date" multiple="multiple">
										<?php		
											foreach($loc_data as $loc) {
												$is_selected='';
												if($this->input->post('location')){
												foreach($this->input->post('location') as $selected_location)
												{
													if($selected_location==$loc['id'])
													{
														$is_selected="selected=\"selected\"";
														echo "";
													}
												}}	
													echo "<option $is_selected value=\"$loc[id]\">".htmlspecialchars($loc['name'])."</option>";
												}
										?>
										</select> <span class="gray">Leave blank to display items in all locations</span>
									</div>
									
									<div class="field_container">
										<label for="category_dropdown_action_date" class="dd_label">In category:</label>
										<?php
										$num_cats=$this->Category_model->get_from_database()->num_rows();
										if($num_cats == 0){
											?>
											<span style="color:red;">You have not added any categories yet.</span>
											<?php
										}
										if($num_cats > 0){
											?>
										<select name="category_file_list[]" id="category_dropdown_action_date" multiple="multiple">
										<?php
											$category_data = $this->Category_model->get_from_database()->result_array();
											foreach($category_data as $category) {
												$is_selected='';
												if($this->input->post('category')){
												foreach($this->input->post('category') as $selected_category)
												{
													if($selected_category==$category['id'])
													{
														$is_selected="selected=\"selected\"";
													}
												}}	
												echo "<option $is_selected value=\"$category[id]\">".htmlspecialchars($category['name'])."</option>";
											}	
										?>
										</select> <span class="gray">Leave blank to display items in all categories</span>
										<?php } ?>
									</div>
							
									<div class="field_container">
										<label for="daterangepicker2" class="dd_label">Action Date:</label>
										<input id="daterangepicker2" type="text" name="date_range" />			
									</div>						
									
									<div class="field_container last">
										<label class="dd_label">Sort by:</label>
										<input type="radio" name="action_sort" value="slot_num" id="action_location_id" checked="checked"><label for="action_location_id">Item Number</label>
										<input type="radio" name="action_sort" value="name" id="action_name"><label for="action_name">Item Name</span></label>
										<input type="radio" name="action_sort" value="action_date" id="action_date"><label for="action_date">Action Date</label> 
									</div>
							
							</div>
								
								<div class="submit">
									<input type="hidden" name="report_type" value="action_date">	
									<?php
									if(($packages->num_rows() == 0)  || ($package[0][0]->allow_reports == 0)) {
									?>
									<a class="red" href="<?=site_url()?>/account">Upgrade to Paper Tiger Pro!</a><span><input type="submit" name="submit" id="submit_transfer" value="Generate Action Date Report"/ disabled></span>
									<?php } else if ($package[0][0]->allow_reports == 1) {
										?>
									<span><input type="submit" name="submit" id="submit_transfer" value="Generate Action Date Report"/></span>
									<?php } ?>
						
								</div>
						
							</form>
						
						</div> <!-- .section -->
						
						<div class="section">
						
							<form action="<?=site_url()?>reports/generate" method="POST" id="import_db_form" name="label_form" target="_blank">
						
								<h3>Transfer Report </h3> 
								
								<p>See all pending transfers within your database. Print this report out and use it to confirm that the physical items were moved.</p>																									
										
								<div class="submit">
								
										<input type="hidden" name="report_type" value="transfer">
									<?php
									if(($packages->num_rows() == 0)  || ($package[0][0]->allow_reports == 0)) {
									?>
					<a class="red" href="<?=site_url()?>/account">Upgrade to Paper Tiger Pro!</a><span><input type="submit" name="submit" id="submit_transfer" value="Generate Transfer Report"/ disabled></span>
									<?php } else if ($package[0][0]->allow_reports == 1) {
										?>
									<span><input type="submit" name="submit" id="submit_transfer" value="Generate Transfer Report"/></span>
									<?php } ?>
						
								</div>
						
							</form>
						
						</div> <!-- .section -->

						<div class="section last">
						
							<form action="<?=site_url()?>reports/generate" method="POST" id="import_db_form" name="label_form" target="_blank">
						
								<h3>Cleanout Report <a class="reports_adv_options gray" id="reports_adv_options_toggle_fileCleanout" href="#" title="Show advanced report options">Show advanced options</a></h3> 
								
								<div class="clear"></div>
								
								<p>If you have setup the software to remind you when your physical items (file cabinets, storage boxes, etc.) need to be cleaned out, this report will make the task much easier.</p>
								
								<div id="reports_adv_options_container_fileCleanout" class="reports_adv_options_container">
								
									<h4>Advanced Options</h4>
									
									<div class="field_container">						
										<label for="location_dropdown_file_cleanout"  class="dd_label">In location:</label>
										<select name="location_file_list[]" id="location_dropdown_file_cleanout" multiple="multiple">
										<?php		
											foreach($loc_data as $loc) {
												$is_selected='';
												if($this->input->post('location')){
												foreach($this->input->post('location') as $selected_location)
												{
													if($selected_location==$loc['id'])
													{
														$is_selected="selected=\"selected\"";
														echo "";
													}
												}}	
													echo "<option $is_selected name=\"location_file_list[]\" value=\"$loc[id]\">".htmlspecialchars($loc['name'])."</option>";
												}
										?>
										</select>
									</div>
									
									<div class="field_container">
										<label for="category_dropdown_file_cleanout"  class="dd_label">In category:</label>
										<?php
										$num_cats=$this->Category_model->get_from_database()->num_rows();
										if($num_cats == 0){
											?>
											<span style="color:red;">You have not added any categories yet.</span>
											<?php
										}
										if($num_cats > 0){
											?>
										<select name="category_file_cleanout[]" id="category_dropdown_file_cleanout" multiple="multiple">
										<?php
											$category_data = $this->Category_model->get_from_database()->result_array();
											foreach($category_data as $category) {
												$is_selected='';
												if($this->input->post('category')){
												foreach($this->input->post('category') as $selected_category)
												{
													if($selected_category==$category['id'])
													{
														$is_selected="selected=\"selected\"";
													}
												}}	
												echo "<option $is_selected value=\"$category[id]\">".htmlspecialchars($category['name'])."</option>";
											}	
										?>
										</select> <span class="gray">Leave blank to display items in all categories</span>
										<?php } ?>
									</div>

									<div class="field_container">
										<label for="daterangepicker3" class="dd_label">Action Date:</label>
										<input id="daterangepicker3" type="text" name="date_range" />			
									</div>

									<div class="field_container last">
										<label class="dd_label">Sort by:</label>
										<input type="radio" name="cleanout_sort" value="slot_num"  id="cleanout_location_id" checked="checked"><label for="cleanout_location_id">Item Number</label>
										<input type="radio" name="cleanout_sort" value="name"  id="cleanout_name"><label for="cleanout_name">Item Name</label>
										<input type="radio" name="cleanout_sort" value="action_date"  id="cleanout_date"><label for="cleanout_date">Action Date</label> 
									</div>
								</div>
								
								<div class="submit">
									<input type="hidden" name="report_type" value="file_cleanout">	
									<?php
									if(($packages->num_rows() == 0)  || ($package[0][0]->allow_reports == 0)) {
									?>
									<a class="red" href="<?=site_url()?>/account">Upgrade to Paper Tiger Pro!</a><span><input type="submit" name="submit" id="submit_transfer" value="Generate Cleanout Report"/ disabled></span>
									<?php } else if ($package[0][0]->allow_reports == 1) {
										?>
									<span><input type="submit" name="submit" id="submit_transfer" value="Generate Cleanout Report"/></span>
									<?php } ?>
						
								</div>
						
							</form>
						
						</div> <!-- .section -->
						
																					
					</div>
					
					<script>
						$(document).ready(function() {
						$('#daterangepicker').daterangepicker(); 
							});
						$(document).ready(function() {
						$('#daterangepicker2').daterangepicker(); 
							});
						$(document).ready(function() {
						$('#daterangepicker3').daterangepicker(); 
							});
					</script>					
			</div>
		</div>
	</div>
</div>
<?php endif;?>
