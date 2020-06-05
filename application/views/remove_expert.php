<script type="text/javascript">
 
$('.choose_expert_profile').hide(); 
  $('span.read_more_expert').click(function () {
	var ind_id = $(this).attr('id');
    $('#choose_expert_profile'+ind_id).toggle('slow');
    $(this).text($(this).text() == 'hide' ? 'read more' : 'hide');
    return false;
  });
</script>

<div id="add_location_form" class="facebox_form">
<div id="facebox_header"><div>Are you sure you want to remove this expert?</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
<p>
</p>


<div style="text-align:center;" id="submit_container" class="db_import_submit">
<a style="margin-left: 20px;text-decoration:none;" href="/dashboard/remove_confirm/">Confirm</a>
<a href="/dashboard/" class="close red">Cancel</a></span>
</div>

        	<?php

        	
        	
        	
/*
				$query = $this->Organization_model->get_all_experts();
				foreach ($query->result() as $row){
				echo '<li>' . $row->first_name .' <a href="#">Select</a>' . '</li>';			
				} 
*/
			?> 
	</div>
</div>
