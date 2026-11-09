<link href="<?=site_url()?>/css/people.css" media="screen" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?=site_url()?>js/affiliates.js"></script>
<div id="dashboard">

    <div id="dashboard_right">

				<div class="tip">
					<div  style="margin-top:15px;" class="databases">How a Paper Tiger Expert Can Help You</div>

					<p style="margin-top:-10px;">
                        Here you can choose form a wide list of professional organizers.
                    </p>
		    <?php $max_users=(array($packages->result_array())); ?>
			    
				</div>			

	</div>
	<!-- the input fields that will hold the variables we will use -->
	<input type='hidden' id='current_page' />
	<input type='hidden' id='show_per_page' />


	<!-- An empty div which will be populated using jQuery -->
	<div id='page_navigation'></div>

<div id="dashboard_left">
	
	<div id="dashboard_header">Manage users for <em><?php print htmlspecialchars($this->Organization_model->get_organization($user_data['organization_id'])->row()->name)?></em> <a style="color:lightgray; float:right; font-size:.7em; margin-top:5px;" href="http://thepapertiger.com/experts" target="_blank">View Experts Page</a></div>

	<div id="dashboard_content">

<div id="add_location_form">
	<div>
<form action="<?=site_url()?>affiliates/choose_expert" method="POST" id="import_db_form" name="affiliate_approval">


<h2 style="text-align:center;">Choose an expert:</h2>
<br/>
<div class ="section">
<div class="form_divider choose_expert">
<?php $q = $this->Organization_model->get_all_experts();
foreach ($q->result() as $r){ ?>
  <div class="expert_blurb">
    <li>
      <div class="expert_preview">
        <div class="expert_preview_container">
          <div style="text-align:center;">
          <div><?php echo $r->first_name .' '. $r->last_name;?>  </div>
          <?php if ($r->city) {?>
            <span class="expert_location">
              <br/>
                <?php echo " " . $r->city; ?><?php if ($r->state){echo ",";}else{echo "";} ?><?php echo $r->state . " ";?>
            </span>
            <?php }else{echo " ";} ?>
            <div class="expert_info">
            <?php if ($r->city) {?>
              <div id="expert_preview_photo"><img width="100px;" src="<?php echo $r->affiliate_photo; ?>" /></div>
            <?php }else{echo " ";} ?>
              <div style="font-size:14px;" class="white small button read_more_expert" id="<?php echo $r->affiliate_code; ?>">read more…</div>
            </div>
            <label class="black small button"  id="expert_label_button" for="expert_label<?php echo $r->affiliate_code; ?>"><a style="font-size:14px;"id="<?php echo $r->affiliate_code; ?>" class="" title="submit">Choose</a> </label>
          </div>
          <input style="position:relative;left:-9999px;" type="radio" name="expert_code" id="expert_label<?php echo $r->affiliate_code; ?>" class="expert_label" value="<?php echo $r->affiliate_code; ?>" />
        </div> <!-- expert_preview_container -->
      </div> <!-- expert_preview -->
    </li>
  <p id="choose_expert_profile<?php echo $r->affiliate_code; ?>" name="old_expert" value="<?php echo $r->email_address; ?>" class="choose_expert_profile"><?php echo $r->profile; ?><p>
  </div> <!-- expert_blurb -->
<?php    }    
?>
</div>
</div>
</form>
<?php	
/* $data = $this->input->post('user_organization'); */
?>




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

	</div> <!-- #dashboard_content -->

</div> <!-- #dashboard_left -->

<div class="clear"></div>
