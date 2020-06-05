<div class="col2">
<div class="files_display" id="all_files">

	<div id="files_display_header" class="border_bottom">
		<h2>All Files - <?=$total_digital_files?> files</h2>
	</div>

	<div class="files_display_tools border_bottom">
		<div class="file_action_tools">
			<!--TODO: CLEAN THIS CRAP UP!!-->
			<button style="margin-left:0px;padding-right:5px;padding-left:5px; padding-top:6px;" class="clean-gray" id="checkbox_button"><img class="checkbox_dropdown_img" src="img/checkbox.png"/> &#9662;</button>
			<div class="dropdown" id="checkbox_dropdown">
				<div class="item_select">
					<a class="label_name" id="select_all" href="#">All</a>
				</div>	
				<div class="item_select">
					<a class="label_name" id="select_none" href="#">None</a>
				</div>	
				<div class="item_select">
					<a class="label_name" id="select_inverse" href="#">Inverse</a>
				</div>	
			</div>

			<button style="margin-left:0px;" class="clean-gray">Archive</button>
			<button class="clean-gray">Edit</button>
			<button class="clean-gray">Delete</button>
			<button style="margin-left:0px;" class="clean-gray">Download</button>
			<button style="margin-left:0px;" class="clean-gray" id="label_button">Labels &#9662;</button>
			<div class="dropdown" id="labels_dropdown">
				<div class="dropdown_list_container">
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">instructions</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">paper tiger destkop</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">software</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">filing system stuff</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">software architect</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">mockups</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">psds</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">instructions</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">paper tiger destkop</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">software</div>
					</div>	
					<div class="item_select">
						<div class="label_checkbox"></div>
						<div class="label_name">filing system stuff</div>
					</div>	
				</div>
				<hr />
				<a href="#">Create New Label</a>
				<a href="#">Manage Labels</a>
			</div>
		</div>
		<div class="pagination">
			<span class="pages"><strong>1-30</strong> of <strong>95</strong></span>
			<a href="next_page" class="pagination_link">Next ›</a>
			<a href="last_page" class="pagination_link">Last »</a>
		</div>
	</div> <!-- files_display_tools-->

	<div id="files">
		<div id="files_horizontal_container">

		<?php if($total_digital_files > 0) {
	foreach($digital_files as $file) {
    // print_r($file);
	?>
		<div class="file" id="<?=$file['id']?>">
				<input type="checkbox" class="file_select" />
				<div class="file_attributes">
				<a href="/files/1" class="file_filename"><?=$file['original_file_name']?></a>
					<span class="file_path"><?=$file['original_file_path']?></span>
					<div class="file_tags">
						<span class="file_tag" style="background:teal !important; color:white" id="1">instructions</span>
						<span class="file_tag" style="background:teal !important; color:white" id="1">instructions</span>
						<span class="file_tag" style="background:bisque !important" id="2">paper tiger desktop</span>
						<span class="file_tag" style="background:darkhaki !important; color:white;" id="3">software</span>
						<span class="file_tag" style="background:cornsilk !important" id="4">software architect</span>
						<span class="file_tag" style="background:lightgreen !important" id="3">software</span>
						<span class="file_tag" style="background:orangered !important; color:white" id="1">instructions</span>
						<span class="file_tag" style="background:bisque !important" id="2">paper tiger desktop</span>
						<span class="file_tag" style="background:darkhaki !important; color:white;" id="3">software</span>
						<span class="file_tag" style="background:cornsilk !important" id="4">software architect</span>
						<span class="file_tag" style="background:lightgreen !important" id="3">software</span>
						<span class="file_tag" style="background:orangered !important; color:white" id="1">instructions</span>
					</div>
				</div>
				<img class="file_preview" src="<?=$file['thumbnail_url']?>" />
				<div class="file_information">
					<span class="file_date_uploaded"><?=$file['upload_time_converted']?></span>
				</div>

				<div class="single_file_action_tools">
					<div class="tool">
						<img src="img/file_archive.png" alt="Archive File" />
						<span class="tool_label"></span>
					</div>
					<div class="tool">
						<img src="img/file_edit.png" alt="Edit File" />
						<span class="tool_label"></span>
					</div>
					<div class="tool">
						<img src="img/file_delete.png" alt="Delete File" />
						<span class="tool_label"></span>
					</div>
					<div class="tool">
						<img src="img/file_download.png" alt="Download File" />
						<span class="tool_label"></span>
					</div>
				</div> <!--single_file_action_tools-->
			</div> <!--file-->
			<?php } 
		}
?>
		</div><!-- files_horizontal_container -->
	</div> <!--files-->

</div> <!--all files-->

				</div>
				<!-- Column 1 end -->
			</div>
			<div id="footer">
				<!-- FOOTER STUFF HERE!-->
			</div>
		</body>
	</html>
