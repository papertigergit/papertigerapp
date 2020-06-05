<div id="delete_category_form" class="facebox_form">
		<div id="facebox_header"><div>Error!</div> <img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_body"><?php echo $this->session->flashdata('settings_error'); ?></p>
		<div id="facebox_footer">
			<div class="right" >
				<div class="small" style="vertical-align:middle"><a href="#" class="close red">Close</a></div>
			</div>
		</div>
	</div>
</div>