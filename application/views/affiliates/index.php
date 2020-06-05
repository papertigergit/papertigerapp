<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

<style>

p {
    font-family: Arial;
    font-size: .9em;
    margin-bottom: 1.2em;
}

h3 {
    margin-bottom:1.2em;
    padding-bottom: 6px;
    border-bottom: solid 1px gray;
}

h4 {
    color:gray;
    font-weight:normal;
    margin-bottom:.8em;
}

</style>
	<div id="db_tools_page_wrapper">
	
		<div id="db_tools_page">			

<?php if(($user_data['id'] == 3) || ($user_data['id'] == 1862)): ?>
<form action="<?=site_url()?>affiliates/update_expert" method="POST" id="affiliate_level_update" name="affiliate_level_update" >

		<div id="label_section">
		<label>User ID:</label><input type="text" name="user_id">
		</div>
		<div id="label_section">
		<label>Affiliate Level:</label><input type="text" name="affiliate_level">
		</div>

    <div id="submit_container" class="">
        <span><input style="cursor:pointer;" class="aff_approve button nice white" type="submit" name="submit" id="submit" value="Update Affiliate Level" /></span>
    </div>

</form>
<?php endif; ?>
                <h2>Paper Tiger Affiliate Program</h2>

                <p>Welcome to the Paper Tiger Affiliate Program. Use the banners below on your site, or use the link below on your Facebook or Twitter to get people signed up. Once someone signs up using one of your affiliate links or banners, you will see them tracked and we will let you know when you are entitled to recieve payment from the referral.</p>

                <p>For more information on how the Affiliate Program works, please visit the <a href="http://www.thepapertiger.com/affiliates" target="_blank">Affiliates Information</a> page on our website.</p>

                <h3>Banners</h3>

                <h4>Banner 1</h4>
                <a href="<?=site_url();?>signup/affiliate/<?=$affiliate_code?>" title="Signup for the Paper Tiger Online!"><img src="<?=site_url();?>img/affiliates/banner_2.png" /></a>

                <br />                
                
                <p>HTML Code: <br/>
                
                <textarea readonly cols="96" rows="2">&lt;a href=&quot;<?=site_url();?>signup/affiliate/<?=$affiliate_code?>&quot; title=&quot;Signup for the Paper Tiger Online!&quot;&gt;&lt;img src=&quot;<?=site_url();?>img/affiliates/banner_2.png&quot; /&gt;&lt;/a&gt;</textarea>
                
                </p>
                
                <h4>Banner 2</h4>
                <a href="<?=site_url();?>signup/affiliate/<?=$affiliate_code?>" title="Signup for the Paper Tiger Online!"><img src="<?=site_url();?>img/affiliates/banner_1.png" /></a>

                <br />                
                
                <p>HTML Code: <br/>
                
                <textarea readonly cols="96" rows="2">&lt;a href=&quot;<?=site_url();?>signup/affiliate/<?=$affiliate_code?>&quot; title=&quot;Sign up for Paper Tiger Online - The Simplest Filing System in the World!&quot;&gt;&lt;img src=&quot;<?=site_url();?>img/affiliates/banner_1.png&quot; /&gt;&lt;/a&gt;</textarea>
                
                </p>
    
                <h4>Text Link</h4>

                <p>Example: <a href="<?=site_url();?>signup/affiliate/<?=$affiliate_code?>" title="Signup for the Paper Tiger Online!">Sign up for Paper Tiger Online - The Simplest Filing System in the World!</a></p>

                <p>HTML Code: <br/>
                
                <textarea readonly cols="96" rows="2">&lt;a href="<?=site_url();?>signup/affiliate/<?=$affiliate_code;?>" title="Sign up for Paper Tiger Online - The Simplest Filing System in the World!"&gt;Sign up for Paper Tiger Online - The Simplest Filing System in the World!&lt;/a&gt;</textarea>
                
                </p>

                <h4>Facebook/Twitter/Social Media Link</h4>

                <p>HTML Code: <br/>
                
                <textarea readonly cols="96" rows="2"><?=site_url();?>signup/affiliate/<?=$affiliate_code;?></textarea>
                
                </p>

                <h3>Your Active Referrals:</h3>
                
                <?php if($referrals->num_rows() == 0): ?>
                
                    You have not referred anyone yet. Use your affiliate links to start!
                
                <?php else: ?>
                
                    <?php foreach($referrals->result() as $r) : ?>
                    
                      <?php 
                      //Calculating 90 days into the future!
                      $ninety_days = ($r->original_date + 7776000);
                      $now = time();
                      ?>

                      Organization: <?=$r->name;?> on  <?=date('M d Y', $r->original_date)?> [<?php if ($now - $ninety_days <= 7776000):?>Not eligible for payment yet...<?php else:?>Eligible for payment!<?php endif;?>]<br/>
                    
                    <?php endforeach;?>
                
                <?php endif; ?>

                <br />
    
                <h3>How do I get paid?</h3>
            
                <p>Payment is disbursed through PayPal every time one of your referred accounts maintains a paid account open for exactly 3 months. We will contact you for more details regarding your payment!</p>
                
                
<hr/><p><br/></p>                


        <?php $affiliate_level =  $this->Organization_model->get_this_affiliate_level($user_data['id']);

                if ($affiliate_level <  3){ ?>

                <h2 style="margin-bottom:20px;">Would you like to become a Paper Tiger Expert?</h2>
                <p>Would you like to be published as a Paper Tiger Expert in Paper Tiger Online app for users to choose you as their expert? If so, you would be willing to help them implement Paper Tiger, answer their questions, and negotiate your professional service package directly with them after an initial consultation. Please note that you may be required to be certified as a Paper Tiger expert.  Paper Tiger has partnered with Simply Productive to certify Paper Tiger Experts. For details and to sign up for certification training, please go to <a href ="http://www.simplyproductive.com/paper-tiger-certification-program/" target="_blank">Paper Tiger Certifcation Program!</a></p>
                <form action="<?=site_url()?>affiliates/request_expert" method="POST" id="affiliate_request" name="affiliate_request" >
<?php   
$data = $this->input->post('user_organization');
?>
<div id="submit_container" class="">
    <span><input style="cursor:pointer;" class="aff_approve button nice white" type="submit" name="submit" id="submit" value="PT Expert Request" /></span>
</div>
</form>  
<br/>
<hr/><p><br/></p>                
<?php } ?>

    <script type="text/javascript" src="<?=site_url()?>js/jquery.timers-1.2.0.js"></script> 
    <script type="text/javascript">

$(document).ready(function(){
		$("#db_tools_page").everyTime(600,function(i){
		})
});

</script>
<?php if ($affiliate_level == 3){ 
?>

<h2>Current Leads</h2>
<?php  $attributes = array('class' => '', 'id' => 'import_db_form');	?>
<div id="approved_leads">
<h4 style="margin-bottom:20px;">Approved Request:</h4>
<p>The following are clients that have requested your services from Paper Tiger Online. They have received an email letting them know that you have been notified of their request. Please be sure to contact them via email as soon as possible or let us know at sales@thepapertiger.com if you cannot.</p>
<div id="approved_leads_head">
    <div class="wrapperField">
        <label><b style="float:left;">Organization</b></label>
    </div>

    <div class="wrapperField">
        <label><b style="float:left;">Admin User</b></label>
    </div>

    <div class="wrapperField">
        <label><b>Email</b></label>
    </div>
</div>	
    <br style="clear:left;" />
	
		    
<div id="approved_leads_body">
<?php $q = $this->Organization_model->get_approved_by_expert($user_data['affiliate_code']);
	foreach ($q->result() as $r){ ?>
    <?php
			$j = $this->Organization_model->get_users($r->id);
		 ?>
    <div class="wrapperField">
        <label><?php echo "<span style=\"float:left;\">" . $r->name . "</span>"; ?></label>

    </div>

    <div class="wrapperField">
        <label><span style="float:left;"><?php echo $j->row()->first_name . " " . $j->row()->last_name; ?></span></label>

    </div>

    <div class="wrapperField">
        <label><?php echo $j->row()->email_address;?></label>

    </div>
    <?php    }    
        ?>
</div>
</div>
	
<div class="clear"></div>
<div id="update_profile">
<?php $autofills = $this->Organization_model->get_users($user_data['organization_id']); ?>
<h2>Update Profile</h2>
</p>Please update your profile to be published in Paper Tiger Online app for customers to be able to choose you as an expert to help them implement Paper Tiger. You can offer an initial limited consultation at no charge and/or negotiate with them your cost for services.</p>
<form method="post" action="<?=site_url()?>affiliates/update_profile" enctype="multipart/form-data" id="" name="affiliate_update"> 
<h3>Update Profile:</h3>
  <div class ="section">
		<div id="label_section">	
		<label>Website:</label><input type="text" value="<?php echo $autofills->row()->website; ?>" name="website">
		</div>
		<div id="label_section">
		<label>Phone:</label><input type="text" value="<?php echo $autofills->row()->phone; ?>" name="phone">
		</div>
<div class="country_section" id="label_section">
<label>Country:</label>
<select id="country" name="country">
<option value="<?php echo $autofills->row()->country; ?>" selected><?php echo $autofills->row()->country; ?></option>
<option value="United_States">United States</option>
<option value="Canada">Canada</option>
<option value="Afghanistan">Afghanistan</option>
<option value="Åland_Islands">Åland Islands</option>
<option value="Albania">Albania</option>
<option value="Algeria">Algeria</option>
<option value="American_Samoa">American Samoa</option>
<option value="Andorra">Andorra</option>
<option value="Angola">Angola</option>
<option value="Anguilla">Anguilla</option>
<option value="Antarctica">Antarctica</option>
<option value="Antigua_And_Barbuda">Antigua and Barbuda</option>
<option value="Argentina">Argentina</option>
<option value="Armenia">Armenia</option>
<option value="Aruba">Aruba</option>
<option value="Australia">Australia</option>
<option value="Austria">Austria</option>
<option value="Azerbaijan">Azerbaijan</option>
<option value="Bahamas">Bahamas</option>
<option value="Bahrain">Bahrain</option>
<option value="Bangladesh">Bangladesh</option>
<option value="Barbados">Barbados</option>
<option value="Belarus">Belarus</option>
<option value="Belgium">Belgium</option>
<option value="Belize">Belize</option>
<option value="Benin">Benin</option>
<option value="Bermuda">Bermuda</option>
<option value="Bhutan">Bhutan</option>
<option value="Bolivia">Bolivia</option>
<option value="Bosnia_And_Herzegovina">Bosnia and Herzegovina</option>
<option value="Botswana">Botswana</option>
<option value="Bouvet_Island">Bouvet Island</option>
<option value="Brazil">Brazil</option>
<option value="British_Indian_Ocean_Territory">British Indian Ocean Territory</option>
<option value="Brunei_Darussalam">Brunei Darussalam</option>
<option value="Bulgaria">Bulgaria</option>
<option value="Burkina_Faso">Burkina Faso</option>
<option value="Burundi">Burundi</option>
<option value="Cambodia">Cambodia</option>
<option value="Cameroon">Cameroon</option>
<option value="Canada">Canada</option>
<option value="Cape_Verde">Cape Verde</option>
<option value="Cayman_Islands">Cayman Islands</option>
<option value="Central_African_Republic">Central African Republic</option>
<option value="Chad">Chad</option>
<option value="Chile">Chile</option>
<option value="China">China</option>
<option value="Christmas_Island">Christmas Island</option>
<option value="Cocos_(Keeling)_Islands">Cocos (Keeling) Islands</option>
<option value="Colombia">Colombia</option>
<option value="Comoros">Comoros</option>
<option value="Congo">Congo</option>
<option value="Congo,_The_Democratic_Republic_Of_The">Congo, The Democratic Republic of The</option>
<option value="Cook_Islands">Cook Islands</option>
<option value="Costa_Rica">Costa Rica</option>
<option value="Cote_D'ivoire">Cote D'ivoire</option>
<option value="Croatia">Croatia</option>
<option value="Cuba">Cuba</option>
<option value="Cyprus">Cyprus</option>
<option value="Czech_Republic">Czech Republic</option>
<option value="Denmark">Denmark</option>
<option value="Djibouti">Djibouti</option>
<option value="Dominica">Dominica</option>
<option value="Dominican_Republic">Dominican Republic</option>
<option value="Ecuador">Ecuador</option>
<option value="Egypt">Egypt</option>
<option value="El_Salvador">El Salvador</option>
<option value="Equatorial_Guinea">Equatorial Guinea</option>
<option value="Eritrea">Eritrea</option>
<option value="Estonia">Estonia</option>
<option value="Ethiopia">Ethiopia</option>
<option value="Falkland_Islands_(Malvinas)">Falkland Islands (Malvinas)</option>
<option value="Faroe_Islands">Faroe Islands</option>
<option value="Fiji">Fiji</option>
<option value="Finland">Finland</option>
<option value="France">France</option>
<option value="French_Guiana">French Guiana</option>
<option value="French_Polynesia">French Polynesia</option>
<option value="French_Southern_Territories">French Southern Territories</option>
<option value="Gabon">Gabon</option>
<option value="Gambia">Gambia</option>
<option value="Georgia">Georgia</option>
<option value="Germany">Germany</option>
<option value="Ghana">Ghana</option>
<option value="Gibraltar">Gibraltar</option>
<option value="Greece">Greece</option>
<option value="Greenland">Greenland</option>
<option value="Grenada">Grenada</option>
<option value="Guadeloupe">Guadeloupe</option>
<option value="Guam">Guam</option>
<option value="Guatemala">Guatemala</option>
<option value="Guernsey">Guernsey</option>
<option value="Guinea">Guinea</option>
<option value="Guinea-bissau">Guinea-bissau</option>
<option value="Guyana">Guyana</option>
<option value="Haiti">Haiti</option>
<option value="Heard_Island_And_Mcdonald_Islands">Heard Island and Mcdonald Islands</option>
<option value="Holy_See_(Vatican_City_State)">Holy See (Vatican City State)</option>
<option value="Honduras">Honduras</option>
<option value="Hong_Kong">Hong Kong</option>
<option value="Hungary">Hungary</option>
<option value="Iceland">Iceland</option>
<option value="India">India</option>
<option value="Indonesia">Indonesia</option>
<option value="Iran,_Islamic_Republic_Of">Iran, Islamic Republic of</option>
<option value="Iraq">Iraq</option>
<option value="Ireland">Ireland</option>
<option value="Isle_Of_Man">Isle of Man</option>
<option value="Israel">Israel</option>
<option value="Italy">Italy</option>
<option value="Jamaica">Jamaica</option>
<option value="Japan">Japan</option>
<option value="Jersey">Jersey</option>
<option value="Jordan">Jordan</option>
<option value="Kazakhstan">Kazakhstan</option>
<option value="Kenya">Kenya</option>
<option value="Kiribati">Kiribati</option>
<option value="Korea,_Democratic_People's_Republic_Of">Korea, Democratic People's Republic of</option>
<option value="Korea,_Republic_Of">Korea, Republic of</option>
<option value="Kuwait">Kuwait</option>
<option value="Kyrgyzstan">Kyrgyzstan</option>
<option value="Lao_People's_Democratic_Republic">Lao People's Democratic Republic</option>
<option value="Latvia">Latvia</option>
<option value="Lebanon">Lebanon</option>
<option value="Lesotho">Lesotho</option>
<option value="Liberia">Liberia</option>
<option value="Libyan_Arab_Jamahiriya">Libyan Arab Jamahiriya</option>
<option value="Liechtenstein">Liechtenstein</option>
<option value="Lithuania">Lithuania</option>
<option value="Luxembourg">Luxembourg</option>
<option value="Macao">Macao</option>
<option value="Macedonia,_The_Former_Yugoslav_Republic_Of">Macedonia, The Former Yugoslav Republic of</option>
<option value="Madagascar">Madagascar</option>
<option value="Malawi">Malawi</option>
<option value="Malaysia">Malaysia</option>
<option value="Maldives">Maldives</option>
<option value="Mali">Mali</option>
<option value="Malta">Malta</option>
<option value="Marshall_Islands">Marshall Islands</option>
<option value="Martinique">Martinique</option>
<option value="Mauritania">Mauritania</option>
<option value="Mauritius">Mauritius</option>
<option value="Mayotte">Mayotte</option>
<option value="Mexico">Mexico</option>
<option value="Micronesia,_Federated_States_Of">Micronesia, Federated States of</option>
<option value="Moldova,_Republic_Of">Moldova, Republic of</option>
<option value="Monaco">Monaco</option>
<option value="Mongolia">Mongolia</option>
<option value="Montenegro">Montenegro</option>
<option value="Montserrat">Montserrat</option>
<option value="Morocco">Morocco</option>
<option value="Mozambique">Mozambique</option>
<option value="Myanmar">Myanmar</option>
<option value="Namibia">Namibia</option>
<option value="Nauru">Nauru</option>
<option value="Nepal">Nepal</option>
<option value="Netherlands">Netherlands</option>
<option value="Netherlands_Antilles">Netherlands Antilles</option>
<option value="New_Caledonia">New Caledonia</option>
<option value="New_Zealand">New Zealand</option>
<option value="Nicaragua">Nicaragua</option>
<option value="Niger">Niger</option>
<option value="Nigeria">Nigeria</option>
<option value="Niue">Niue</option>
<option value="Norfolk_Island">Norfolk Island</option>
<option value="Northern_Mariana_Islands">Northern Mariana Islands</option>
<option value="Norway">Norway</option>
<option value="Oman">Oman</option>
<option value="Pakistan">Pakistan</option>
<option value="Palau">Palau</option>
<option value="Palestinian_Territory,_Occupied">Palestinian Territory, Occupied</option>
<option value="Panama">Panama</option>
<option value="Papua_New_Guinea">Papua New Guinea</option>
<option value="Paraguay">Paraguay</option>
<option value="Peru">Peru</option>
<option value="Philippines">Philippines</option>
<option value="Pitcairn">Pitcairn</option>
<option value="Poland">Poland</option>
<option value="Portugal">Portugal</option>
<option value="Puerto_Rico">Puerto Rico</option>
<option value="Qatar">Qatar</option>
<option value="Reunion">Reunion</option>
<option value="Romania">Romania</option>
<option value="Russian_Federation">Russian Federation</option>
<option value="Rwanda">Rwanda</option>
<option value="Saint_Helena">Saint Helena</option>
<option value="Saint_Kitts_And_Nevis">Saint Kitts and Nevis</option>
<option value="Saint_Lucia">Saint Lucia</option>
<option value="Saint_Pierre_And_Miquelon">Saint Pierre and Miquelon</option>
<option value="Saint_Vincent_And_The_Grenadines">Saint Vincent and The Grenadines</option>
<option value="Samoa">Samoa</option>
<option value="San_Marino">San Marino</option>
<option value="Sao_Tome_And_Principe">Sao Tome and Principe</option>
<option value="Saudi_Arabia">Saudi Arabia</option>
<option value="Senegal">Senegal</option>
<option value="Serbia">Serbia</option>
<option value="Seychelles">Seychelles</option>
<option value="Sierra_Leone">Sierra Leone</option>
<option value="Singapore">Singapore</option>
<option value="Slovakia">Slovakia</option>
<option value="Slovenia">Slovenia</option>
<option value="Solomon_Islands">Solomon Islands</option>
<option value="Somalia">Somalia</option>
<option value="South_Africa">South Africa</option>
<option value="South_Georgia_And_The_South_Sandwich_Islands">South Georgia and The South Sandwich Islands</option>
<option value="Spain">Spain</option>
<option value="Sri_Lanka">Sri Lanka</option>
<option value="Sudan">Sudan</option>
<option value="Suriname">Suriname</option>
<option value="Svalbard_And_Jan_Mayen">Svalbard and Jan Mayen</option>
<option value="Swaziland">Swaziland</option>
<option value="Sweden">Sweden</option>
<option value="Switzerland">Switzerland</option>
<option value="Syrian_Arab_Republic">Syrian Arab Republic</option>
<option value="Taiwan,_Province_Of_China">Taiwan, Province of China</option>
<option value="Tajikistan">Tajikistan</option>
<option value="Tanzania,_United_Republic_Of">Tanzania, United Republic of</option>
<option value="Thailand">Thailand</option>
<option value="Timor-leste">Timor-leste</option>
<option value="Togo">Togo</option>
<option value="Tokelau">Tokelau</option>
<option value="Tonga">Tonga</option>
<option value="Trinidad_And_Tobago">Trinidad and Tobago</option>
<option value="Tunisia">Tunisia</option>
<option value="Turkey">Turkey</option>
<option value="Turkmenistan">Turkmenistan</option>
<option value="Turks_And_Caicos_Islands">Turks and Caicos Islands</option>
<option value="Tuvalu">Tuvalu</option>
<option value="Uganda">Uganda</option>
<option value="Ukraine">Ukraine</option>
<option value="United_Arab_Emirates">United Arab Emirates</option>
<option value="United_Kingdom">United Kingdom</option>
<option value="United_States">United States</option>
<option value="United_States_Minor_Outlying_Islands">United States Minor Outlying Islands</option>
<option value="Uruguay">Uruguay</option>
<option value="Uzbekistan">Uzbekistan</option>
<option value="Vanuatu">Vanuatu</option>
<option value="Venezuela">Venezuela</option>
<option value="Viet_Nam">Viet Nam</option>
<option value="Virgin_Islands,_British">Virgin Islands, British</option>
<option value="Virgin_Islands,_U.S.">Virgin Islands, U.S.</option>
<option value="Wallis_And_Futuna">Wallis and Futuna</option>
<option value="Western_Sahara">Western Sahara</option>
<option value="Yemen">Yemen</option>
<option value="Zambia">Zambia</option>
<option value="Zimbabwe">Zimbabwe</option>
</select>
</div>
		<div class="city_section" id="label_section">
		<label>City:</label><input type="text" value="<?php echo $autofills->row()->city; ?>" name="city">
		</div>
<div <?php if($autofills->row()->country == "Canada"){?> style="display:block" <?php }else{ ?>  <?php } ?> class="province_section" id="label_section">
<label>Province:</label>
<select id="state" name="state">
<option value="<?php echo $autofills->row()->state; ?>" selected>-----</option>
<option value="AB">Alberta</option>
<option value="BC">British Columbia</option>
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
</div>
<div <?php if($autofills->row()->country == "United_States"){?> style="display:block" <?php }else{ ?>  <?php } ?> class="state_section" id="label_section">
<label>State:</label>
<select id="state" name="state">
<option value="<?php echo $autofills->row()->state; ?>" selected>-----</option>
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
<div id="label_section">
    <label>Profile:</label><br/><textarea rows="20" cols="100" name="profile"><?php echo $autofills->row()->profile; ?>
</textarea>
</div>
<div id="label_section">
<img style="width: 250px;" src="<?php echo $autofills->row()->affiliate_photo; ?>" /> <br/>
<input type="file"  value="<?php echo $autofills->row()->affiliate_photo; ?>" name="image1" /><br/>
<span style="font-weight:100"><i>Maximum upload file size:1MB</i></span>
</div>
      <input type="submit" name="go" value="Update" />  

  </div> 

</form>  
</div>
<?php } ?>
<br>
  <div class="clear">
</div>
    </div>
