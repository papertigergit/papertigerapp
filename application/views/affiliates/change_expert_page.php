<link href="<?=site_url()?>/css/people.css" media="screen" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?=site_url()?>js/affiliates.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

<div id="dashboard">

    <div id="dashboard_right">

				<div class="tip">
					<div  style="margin-top:15px;" class="databases">Please choose from the list of experts</div>

					<p style="margin-top:-10px;">
                        Paper Tiger Experts are productivity coaches authorized to set up and convert your organization to Paper Tiger software and can provide you with the highest quality implementation and training services available, including customizing a work-flow solution to help you get organized and working more efficiently and productively.</p>
                        <p>These experts are independent consultants, so please contact them directly for pricing information for their professional services. Many Paper Tiger Experts will offer a complimentary 15-30 minute phone consultation to get your initial questions answered and will share their professional service package to help you implement Paper Tiger and get you working fast!</p><p>Note these experts will be able to help you set up and use Paper Tiger more efficiently, but they are not technical support. They will not be able to help you if something seems to be broken in the app. For technical problems, please search our <a target="_blank" href="http://thepapertiger.com/support">knowledge base</a> or email support@thepapertiger.com.</p>
                        <p>Thank you for using Paper Tiger!</p>
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
	
	<div id="dashboard_header">
<div class="expert_location">
Search by Location:
<select id="country_list">
<option value="all">All</option>
<option value="United_States">United States</option>
<option value="Canada">Canada</option>
<option value="International">International</option>
</select>

<select id="source_can" class= "source_can" name="state">
<option value="all">All</option>
<option value="AB">Alberta</option>
<option value="BC">British Columbia</option>
<option value="MB">Manitoba</option>
<option value="NB">New Brunswick</option>
<option value="NL">Newfoundland and Labrador</option>
<option value="NS">Nova Scotia</option>
<option value="NT">Northwest Territories</option>
<option value="NU">Nunavut</option>
<option value="ON">Ontario</option>
<option value="PE">Prince Edward Island</option>
<option value="QC">Quebec</option>
<option value="SK">Saskatchewan</option>
<option value="YT">Yukon</option>
</select>

<select class="source_usa" id="source_usa" name="state">
<option value="all">All</option>
<option value="AL">Alabama</option>
<option value="AK">Alaska</option>
<option value="AZ">Arizona</option>
<option value="AR">Arkansas</option>
<option value="CA">California</option>
<option value="CO">Colorado</option>
<option value="CT">Connecticut</option>
<option value="DE">Delaware</option>
<option value="DC">District of Columbia</option>
<option value="FL">Florida</option>
<option value="GA">Georgia</option>
<option value="HI">Hawaii</option>
<option value="ID">Idaho</option>
<option value="IL">Illinois</option>
<option value="IN">Indiana</option>
<option value="IA">Iowa</option>
<option value="KS">Kansas</option>
<option value="KY">Kentucky</option>
<option value="LA">Louisiana</option>
<option value="ME">Maine</option>
<option value="MD">Maryland</option>
<option value="MA">Massachusetts</option>
<option value="MI">Michigan</option>
<option value="MN">Minnesota</option>
<option value="MS">Mississippi</option>
<option value="MO">Missouri</option>
<option value="MT">Montana</option>
<option value="NE">Nebraska</option>
<option value="NV">Nevada</option>
<option value="NH">New Hampshire</option>
<option value="NJ">New Jersey</option>
<option value="NM">New Mexico</option>
<option value="NY">New York</option>
<option value="NC">North Carolina</option>
<option value="ND">North Dakota</option>
<option value="OH">Ohio</option>
<option value="OK">Oklahoma</option>
<option value="OR">Oregon</option>
<option value="PA">Pennsylvania</option>
<option value="RI">Rhode Island</option>
<option value="SC">South Carolina</option>
<option value="SD">South Dakota</option>
<option value="TN">Tennessee</option>
<option value="TX">Texas</option>
<option value="UT">Utah</option>
<option value="VT">Vermont</option>
<option value="VA">Virginia</option>
<option value="WA">Washington</option>
<option value="WV">West Virginia</option>
<option value="WI">Wisconsin</option>
<option value="WY">Wyoming</option>
</select>
</div>
 <a style="color:lightgray; float:right; font-size:.7em; margin-top:-16px;" href="http://thepapertiger.com/experts" target="_blank">View Experts Page</a></div>

	<div id="dashboard_content">

<div id="add_location_form">
	<div>
<form action="<?=site_url()?>affiliates/change_expert" method="POST" id="import_db_form" name="affiliate_approval">


<h2 style="text-align:center;">Choose an expert:</h2>
<br/>
<div class ="section">
<div class="form_divider choose_expert">
<?php $q = $this->Organization_model->get_all_experts();
echo $q;
foreach ($q->result() as $r){ 
$state_underscore = str_replace(" ", "_", $r->state); 
?>
  <div class="expert_blurb <?php echo $r->country ?>" id="<?php echo $r->state; ?>" >
    <li>
      <div class="expert_preview">
        <div class="expert_preview_container">
          <div style="text-align:center;">
          <div><?php echo $r->first_name .' '. $r->last_name;?>  </div>
          <?php if ($r->city) {?>
            <span class="expert_location">
              <br/>
                <?php echo " " . $r->city; ?><?php if ($r->state){echo ", ";}else{echo "";} ?><?php echo $r->state . " ";?>
            </span>
            <?php }else{echo " ";} ?>
            <div class="expert_info">
            <?php if ($r->city) {?>
              <div id="expert_preview_photo"><img width="100px;" src="<?php echo $r->affiliate_photo; ?>" /></div>
            <?php }else{echo " ";} ?>
              <div style="font-size:14px;" class="white small button read_more_expert" id="<?php echo $r->affiliate_code; ?>">read more…</div>
            </div>
            <label class="black small button"  id="expert_label_button" for="expert_label<?php echo $r->affiliate_code; ?>"><a style="color:#fff;font-size:14px;"id="<?php echo $r->affiliate_code; ?>" class="" title="submit">Choose</a> </label>
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
