<link href="<?=site_url()?>/css/people.css" media="screen" rel="stylesheet" type="text/css"/>
 <script type="text/javascript">
$(document).ready(function() {  
$('.choose_expert_profile').hide(); 
  $('span.read_more_expert').click(function () {
	var ind_id = $(this).attr('id');
    $('#choose_expert_profile'+ind_id).toggle('slow');
    $(this).text($(this).text() == 'hide' ? 'read more' : 'hide');
    return false;
  });

  // bind "click" event for links with title="submit"
  var $choose_expert = $("a[title=submit]");
  
  $choose_expert.click( function(){
  var inds_id = $(this).attr('id');
/*   	e.preventDefault();      */
  var $form = $( this ), term = $( 'input' ).val(), url = $form.attr( 'action' );
  var $submit_expert = $(this).parents("form");
  $('this input:radio[name=expert_code]').checked = true; 

     $.ajax({
			type: "POST",
      url: url,
      data: inds_id,
      success: function() {
      alert("An email has been sent to your Paper Tiger Expert of choice.  You will be notified once the expert has confirmed");
      $submit_expert.submit();
      }      
    });            
 //   return false;
  });
});
</script> 
<div id="dashboard">

    <div id="dashboard_right">

				<div class="tip">

					<div  style="margin-top:15px;" class="databases">Users in your Paper Tiger Account</div>

					<p style="margin-top:-10px;">
                        Here you can invite more users to your Paper Tiger account, as well as control who can see and change your information.
                    </p>
		    <?php $max_users=(array($packages->result_array())); ?>
			    
                    <h2 style="border-top:solid 1px gray; border-bottom:solid 1px gray; padding:5px 0; color:darkgreen; margin-top:20px;">There are <?=$users->num_rows();?> of <?php echo $max_users[0][0]['max_users']==-1?'<em>Unlimited</em>':$max_users[0][0]['max_users']; ?> seats occupied.</h2>
 
				</div>			

	</div>

<div id="dashboard_left">
	
	<div id="dashboard_header">Manage users for <em><?php print htmlspecialchars($this->Organization_model->get_organization($user_data['organization_id'])->row()->name)?></em> <a style="color:lightgray; float:right; font-size:.7em; margin-top:5px;" href="<?=site_url();?>permissions">Change permissions</a></div>

	<div id="dashboard_content">

<div id="add_location_form" class="facebox_form">
	<div id="facebox_header"><div>Choose an expert</div><img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/></div>
	<div id="facebox_content">
<p>
<h4 id="expert_web_link"><a href="http://thepapertiger.com/experts" target="_blank">View Experts Page</a></h4>
</p>
<br/>
<form action="<?=site_url()?>affiliates/choose_expert" method="POST" id="import_db_form" name="affiliate_approval">


<h2 style="text-align:center;">Choose an expert:</h2>
<br/>
<div class ="section">
	<div class="form_divider choose_expert">
	        <?php $q = $this->Organization_model->get_all_experts();
	       echo $q;
		foreach ($q->result() as $r){ ?>
		<div class="expert_blurb">
		<li>
		<div class="expert_preview">
			<div class="expert_preview_container">
				<div style="text-align:center;margin-top:20px;margin-bottom:-20px;">
					<div><?php echo $r->first_name .' '. $r->last_name;?>  </div>
					<?php if ($r->city) {?>
					<span class="expert_location"><br/><?php echo " " . $r->city; ?><?php if ($r->state){echo ",";}else{echo "";} ?><?php echo $r->state . " ";?></span>
					<?php }else{echo " ";} ?>
				<div class="expert_info">
				<?php if ($r->city) {?>
				<div id="expert_preview_photo"><img width="100px;" src="<?php echo $r->affiliate_photo; ?>" /></div>
				<?php }else{echo " ";} ?>
        <p><br/></p><span class="read_more_expert" id="<?php echo $r->affiliate_code; ?>">read more…</span><br/><br/>
        
        <input style="position:relative;left:-9999px;" type="radio" name="expert_code" id="expert_label<?php echo $r->affiliate_code; ?>" class="expert_label" value="<?php echo $r->affiliate_code; ?>" />
        <label id="expert_label_button" for="expert_label<?php echo $r->affiliate_code; ?>"><a id="<?php echo $r->affiliate_code; ?>" title="submit">Choose</a> </label>

			</div> <!-- expert_preview_container -->

		</div> <!-- expert_preview -->
			
			<br/>
		</li>
		<br/>
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
<div id="submit_container" class="db_import_submit">
    <span><input type="submit" name="submit" id="submit" value="Choose Organizer" /></span>
	<span class="small gray"><span>or</span><a href="/locations/" class="close red">Cancel</a></span>
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

	</div> <!-- #dashboard_content -->

</div> <!-- #dashboard_left -->

<div class="clear"></div>
