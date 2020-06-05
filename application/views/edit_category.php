<div id="edit_category_form" class="facebox_form">
	<div id="facebox_header"><div>Edit Category: <?=character_limiter(htmlspecialchars($this_category->name),37,'...')?></div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
		<p id="facebox_intro">Give the category a name and a description so you can remember what it is used for!</p>
		<form action="<?=site_url()?>categories/edit/<?=$this_category->id?>" method="POST" class="form" name="add_category">
			<div id="form_padding">
				<div class="form_divider">
					<p class="tooltip_content">What the category will be called. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=194212&catId=230>Learn More</a></p>					
					<label for="name">Category Name<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<input type="text" class="first_input input" name="name" id="name" value="<?=htmlspecialchars($this_category->name)?>"/>
					<img class="required" src="<?=site_url()?>img/asterisk.png" width="15" height="15">
					<div class="clear"></div>
				</div>
				<div class="form_divider">
					<p class="tooltip_content">A textual description of the category so that you can remember what it pertains to. <a target="_blank" href=http://www.thepapertiger.com/support/articles.php?id=141146&catId=230>Learn More</a></p>															
					<label for="description">Description<img width="16" height="16" class="tooltip" title="This a textual description" src="<?=site_url()?>img/tooltip.png"></label>
					<textarea type="text" name="description" id="description" class="input"><?=htmlspecialchars($this_category->description)?></textarea>
					<div class="clear"></div>
				</div>
				<?php if($organization_admin == $this->user_data['id']): ?>
				<div class="form_divider">
                    <p class="tooltip_content">The category owner is the only person that can edit or delete a category. Use this dropdown if you want to change the owner of this category.</p>
                    <label for="name">Change Owner<img width="16" height="16" class="tooltip" src="<?=site_url()?>img/tooltip.png"></label>
					<select class="select" name="category_owner" id="category_owner">
					<?php
					foreach($users->result() as $org_users) {
						$default='';
						if($this_category->owner == $org_users->id):
							$default='selected="selected"';
						endif;	
						?>
						<option value="<?=$org_users->id?>" <?=$default?>><?=$org_users->first_name?> <?=$org_users->last_name?></input>
						<?php
					}
					?>
					</select>
					<div class="clear"></div>
				</div>
				<?php endif; ?>
				<?php if($organization_admin != $this->user_data['id']): ?>
					<input type="hidden" name="category_owner" value="<?=$this->user_data['id']?>">
				<?php endif; ?>
			</div>
			<div id="result"></div>
			<div id="submit_container">
				<span><input type="submit" name="submit" id="submit" value="Save Changes"/></span>
				<span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
				<span id="trashcan" class="small right"><img src="<?=site_url()?>img/trashcan.png" alt="trashcan"/><a href="<?=site_url()?>categories/delete/0/<?=$this_category->id?>" class="red" rel="facebox_fb modal_fb">Delete This Category</a></span>

			</div>

			<?php
			$this_category->last_edited_time;
			$this_category->last_edited_by;
			if($is_network):
				if($is_network->num_rows() > 0):
					if($this_category->last_edited_time):
						if($this_category->last_edited_by):
			?>
			<div class="right">
				<span class="small gray">Last edited by <?=$this->User_model->get_user($this_category->last_edited_by)->row()->first_name?> <?=$this->User_model->get_user($this_category->last_edited_by)->row()->last_name?> on <?=unix_to_human($this_category->last_edited_time)?></span>
			</div>
			<?php 
						endif;
					endif;
				endif;
			endif;	

			?>

			<input type="hidden" name="edit_category" value="true"/>
			<input type="hidden" name="edit_time" value="<?=$last_edit_time?>"/>
		</form>
	</div>
</div>
