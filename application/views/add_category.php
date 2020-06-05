<div id="add_category_form" class="facebox_form">
	<div id="facebox_header"><div>Add A New Category</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">Give the category a name and a description so you can remember what it is used for!</p>
		<form action="<?=site_url()?>categories/add" method="POST" class="form" name="add_category">
			<div id="form_padding">
				<div class="form_divider">
					<p class="tooltip_content">What the category will be called. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194212&catId=230>Learn More</a></p>					
					<label for="name">Category Name<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="first_input input" name="name" id="name"/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">A textual description of the category so that you can remember what it pertains to. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=141146&catId=230>Learn More</a></p>										
					<label for="description">Description<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<textarea type="text" name="description" id="description" class="input"/>
					<div class="clear"></div>
				</div>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Add This Category"/></span>
				<span class="small"><span>or</span><a href="#" class="close red">Cancel</a></span>
			</div>
		</form>
	</div>
</div>
