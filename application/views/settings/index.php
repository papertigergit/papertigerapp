<?php
$organization_admin = $this->Permissions_model->get_organization_admin($this->user_data['organization_id'])->admin_user;
$user_setting=$this->Settings_model->get_all();
if($user_setting->num_rows()==0)
{
  $this_reminder=7;
  $this_confirmation=1;
  $this_digital=0;
  $remind_me=1;
  $show_notes=0;
}
if($user_setting->num_rows() > 0)
{

  foreach($user_setting->result() as $row)
  {
    $this_reminder=(($row->reminder_duration) / 86400);
    $this_confirmation=$row->confirmations_value;
    $remind_me=$row->reminders_enabled;
    $this_digital=$row->digital_search;
    $show_notes=$row->show_notes;

  }

}
?>

<div id="database_preferences_form" class="facebox_form" style="">
  
  <div id="facebox_header">
  
    <div>Database Preferences</div>
    <img src="<?=site_url()?>img/close_button.gif" alt="close" class="close"/>
  
  </div> <!-- #facebox_header -->

  <div id="facebox_content">
  
    <form action="<?=site_url()?>databases/modify/" method="POST" class="form" name="delete_user">
    
      <div class="preferences_container">
      
       <?php if($organization_admin == $this->user_data['id']) { ?> 
        <h3>Database Name</h3>
        
        <p>Change the name of the current database.</p>
        
        <div class="input_container">
        
          <input type="text" name="db_name" id="db_name_field"  value="<?php echo htmlspecialchars($database->name)?>"/>
        
        </div>
      <?php } ?> 
      
      </div> <!-- .preferences_container -->

      <div class="preferences_container">
		<?php if($google_users_data->num_rows() > 0): ?>
        <h3>Activate Digital Search</h3>
        
        <p>Show digital search results. </p>
        
        <div class="input_container">
        
          <div>
            <input type="radio" name="digital_search" id="digitalYes"  value="1" <?=$this_digital ==1 ? "checked" : '' ?>/>
            <label for="digitalYes" class="radioLabel"><strong>Yes.</strong> Show digital search results.</label>
          </div>
          
          <div>
            <input type="radio" name="digital_search" id="digitalNo"  value="0"  <?=$this_digital ==0 ? "checked" : '' ?> />
            <label for="digitalNo" class="radioLabel"><strong>No.</strong> Don't show digital results.</label>
          </div>
        
        </div>
      	<?php endif; ?>
		
        <h3>Confirmation Default Behavior</h3>
        
        <p>When you make a change in Paper Tiger, many times you must also make a change to your physical world (example: if you delete a paper file in Paper Tiger, you must actually toss or shred the actual paper). Would you like Paper Tiger to remind you to make the physical changes?</p>
        
        <div class="input_container">
        
          <div>
            <input type="radio" name="confirmation" id="confirmYes"  value="1" <?=$this_confirmation ==1 ? "checked" : '' ?>/>
            <label for="confirmYes" class="radioLabel"><strong>Yes.</strong>Remind me each time.</label>
          </div>
          
          <div>
            <input type="radio" name="confirmation" id="confirmNo"  value="0"  <?=$this_confirmation ==0 ? "checked" : '' ?> />
            <label for="confirmNo" class="radioLabel"><strong>No.</strong>Do not remind me.</label>
          </div>
        
        </div>
      
      </div> <!-- .preferences_container -->
      <div class='preferences_container'>
			<?php 
			// all timezones
				$zonelist = array(''=>'Select TimeZone','Kwajalein' => '(GMT-12:00) International Date Line West',
				'Pacific/Midway' => '(GMT-11:00) Midway Island',
				'Pacific/Samoa' => '(GMT-11:00) Samoa',
				'Pacific/Honolulu' => '(GMT-10:00) Hawaii',
				'America/Anchorage' => '(GMT-09:00) Alaska',
				'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US &amp; Canada)',
				'America/Tijuana' => '(GMT-08:00) Tijuana, Baja California',
				'America/Denver' => '(GMT-07:00) Mountain Time (US &amp; Canada)',
				'America/Chihuahua' => '(GMT-07:00) Chihuahua',
				'America/Mazatlan' => '(GMT-07:00) Mazatlan',
				'America/Phoenix' => '(GMT-07:00) Arizona',
				'America/Regina' => '(GMT-06:00) Saskatchewan',
				'America/Tegucigalpa' => '(GMT-06:00) Central America',
				'America/Chicago' => '(GMT-06:00) Central Time (US &amp; Canada)',
				'America/Mexico_City' => '(GMT-06:00) Mexico City',
				'America/Monterrey' => '(GMT-06:00) Monterrey',
				'America/New_York' => '(GMT-05:00) Eastern Time (US &amp; Canada)',
				'America/Bogota' => '(GMT-05:00) Bogota',
				'America/Lima' => '(GMT-05:00) Lima',
				'America/Rio_Branco' => '(GMT-05:00) Rio Branco',
				'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
				'America/Caracas' => '(GMT-04:30) Caracas',
				'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)',
				'America/Manaus' => '(GMT-04:00) Manaus',
				'America/Santiago' => '(GMT-04:00) Santiago',
				'America/La_Paz' => '(GMT-04:00) La Paz',
				'America/St_Johns' => '(GMT-03:30) Newfoundland',
				'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
				'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
				'America/Godthab' => '(GMT-03:00) Greenland',
				'America/Montevideo' => '(GMT-03:00) Montevideo',
				'Atlantic/South_Georgia' => '(GMT-02:00) Mid-Atlantic',
				'Atlantic/Azores' => '(GMT-01:00) Azores',
				'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
				'Europe/Dublin' => '(GMT) Dublin',
				'Europe/Lisbon' => '(GMT) Lisbon',
				'Europe/London' => '(GMT) London',
				'Africa/Monrovia' => '(GMT) Monrovia',
				'Atlantic/Reykjavik' => '(GMT) Reykjavik',
				'Africa/Casablanca' => '(GMT) Casablanca',
				'Europe/Belgrade' => '(GMT+01:00) Belgrade',
				'Europe/Bratislava' => '(GMT+01:00) Bratislava',
				'Europe/Budapest' => '(GMT+01:00) Budapest',
				'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
				'Europe/Prague' => '(GMT+01:00) Prague',
				'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
				'Europe/Skopje' => '(GMT+01:00) Skopje',
				'Europe/Warsaw' => '(GMT+01:00) Warsaw',
				'Europe/Zagreb' => '(GMT+01:00) Zagreb',
				'Europe/Brussels' => '(GMT+01:00) Brussels',
				'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
				'Europe/Madrid' => '(GMT+01:00) Madrid',
				'Europe/Paris' => '(GMT+01:00) Paris',
				'Africa/Algiers' => '(GMT+01:00) West Central Africa',
				'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
				'Europe/Berlin' => '(GMT+01:00) Berlin',
				'Europe/Rome' => '(GMT+01:00) Rome',
				'Europe/Stockholm' => '(GMT+01:00) Stockholm',
				'Europe/Vienna' => '(GMT+01:00) Vienna',
				'Europe/Minsk' => '(GMT+02:00) Minsk',
				'Africa/Cairo' => '(GMT+02:00) Cairo',
				'Europe/Helsinki' => '(GMT+02:00) Helsinki',
				'Europe/Riga' => '(GMT+02:00) Riga',
				'Europe/Sofia' => '(GMT+02:00) Sofia',
				'Europe/Tallinn' => '(GMT+02:00) Tallinn',
				'Europe/Vilnius' => '(GMT+02:00) Vilnius',
				'Europe/Athens' => '(GMT+02:00) Athens',
				'Europe/Bucharest' => '(GMT+02:00) Bucharest',
				'Europe/Istanbul' => '(GMT+02:00) Istanbul',
				'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
				'Asia/Amman' => '(GMT+02:00) Amman',
				'Asia/Beirut' => '(GMT+02:00) Beirut',
				'Africa/Windhoek' => '(GMT+02:00) Windhoek',
				'Africa/Harare' => '(GMT+02:00) Harare',
				'Asia/Kuwait' => '(GMT+03:00) Kuwait',
				'Asia/Riyadh' => '(GMT+03:00) Riyadh',
				'Asia/Baghdad' => '(GMT+03:00) Baghdad',
				'Africa/Nairobi' => '(GMT+03:00) Nairobi',
				'Asia/Tbilisi' => '(GMT+03:00) Tbilisi',
				'Europe/Moscow' => '(GMT+03:00) Moscow',
				'Europe/Volgograd' => '(GMT+03:00) Volgograd',
				'Asia/Tehran' => '(GMT+03:30) Tehran',
				'Asia/Muscat' => '(GMT+04:00) Muscat',
				'Asia/Baku' => '(GMT+04:00) Baku',
				'Asia/Yerevan' => '(GMT+04:00) Yerevan',
				'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
				'Asia/Karachi' => '(GMT+05:00) Karachi',
				'Asia/Tashkent' => '(GMT+05:00) Tashkent',
				'Asia/Kolkata' => '(GMT+05:30) Calcutta',
				'Asia/Colombo' => '(GMT+05:30) Sri Jayawardenepura',
				'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
				'Asia/Dhaka' => '(GMT+06:00) Dhaka',
				'Asia/Almaty' => '(GMT+06:00) Almaty',
				'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
				'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
				'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
				'Asia/Bangkok' => '(GMT+07:00) Bangkok',
				'Asia/Jakarta' => '(GMT+07:00) Jakarta',
				'Asia/Brunei' => '(GMT+08:00) Beijing',
				'Asia/Chongqing' => '(GMT+08:00) Chongqing',
				'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
				'Asia/Urumqi' => '(GMT+08:00) Urumqi',
				'Asia/Irkutsk' => '(GMT+08:00) Irkutsk',
				'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
				'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
				'Asia/Singapore' => '(GMT+08:00) Singapore',
				'Asia/Taipei' => '(GMT+08:00) Taipei',
				'Australia/Perth' => '(GMT+08:00) Perth',
				'Asia/Seoul' => '(GMT+09:00) Seoul',
				'Asia/Tokyo' => '(GMT+09:00) Tokyo',
				'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
				'Australia/Darwin' => '(GMT+09:30) Darwin',
				'Australia/Adelaide' => '(GMT+09:30) Adelaide',
				'Australia/Canberra' => '(GMT+10:00) Canberra',
				'Australia/Melbourne' => '(GMT+10:00) Melbourne',
				'Australia/Sydney' => '(GMT+10:00) Sydney',
				'Australia/Brisbane' => '(GMT+10:00) Brisbane',
				'Australia/Hobart' => '(GMT+10:00) Hobart',
				'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
				'Pacific/Guam' => '(GMT+10:00) Guam',
				'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
				'Asia/Magadan' => '(GMT+11:00) Magadan',
				'Pacific/Fiji' => '(GMT+12:00) Fiji',
				'Asia/Kamchatka' => '(GMT+12:00) Kamchatka',
				'Pacific/Auckland' => '(GMT+12:00) Auckland',
				'Pacific/Tongatapu' => '(GMT+13:00) Nukualofa');

			
			?>
		<h3>Update Database Time Zone</h3>
		<p>Choose an appropriate timezone for your database</p>
		<div class="input_container">
			 <select name="timezone">
				
				<?php
					$selected ='';
					foreach($zonelist as $key => $value) {
						if($key ==  $database->timezone){
							echo '<option selected="true" value="' . $key . '">' . $value . '</option>' . "\n";
						}else {
							echo '<option  value="' . $key . '">' . $value . '</option>' . "\n";
						}
						
					}
				?>
			</select>
		 </div>
	  </div>
      <div class="preferences_container">
      
        <h3>Reminders Horizon</h3>
        
        <p>If you set any Action Dates, Paper Tiger will look into the future the number of days listed below to remind you.  How many days would you like Paper Tiger to look into the future?
day(s)</p>
        
        <div class="input_container">
        
          <select name="reminder_days">
          <?php
          for($i=1;$i<31;$i++)
          {
            if($i==$this_reminder)
            {
              echo "<option value=\"$i\"  selected>$i</option>";
            }
            else
            {
              echo "<option value=\"$i\" >$i</option>";
            }
          }
          ?>
          </select>
          
          <label for="reminder_days" style="display:inline;">day(s)</label> 
        
        </div>
          
      </div> <!-- .preferences_container -->      
        
      <div class="preferences_container">
      
        <h3>Reminders Notification</h3>
        
        <p>Do you want Paper Tiger to notify you when you have reminders due?</p>
        
        <div class="input_container">
        
          <div>
            <input type="radio" name="remind_me" id="reminderYes"  value="1" <?=$remind_me ==1 ? "checked" : '' ?>/>
            <label for="reminderYes" class="radioLabel"><strong>Yes.</strong>Notify me.</label>
          </div>
          
          <div>
            <input type="radio" name="remind_me" id="reminderNo"  value="0"  <?=$remind_me ==0 ? "checked" : '' ?> />
            <label for="reminderNo" class="radioLabel"><strong>No.</strong>Do not notify me.</label>
          </div>
        
        </div>
      
      </div> <!-- .preferences_container -->



      <div class="preferences_container">
      
        <h3>Notes</h3>
        
        <p>Notes in Paper Tiger are an extra field of information where you can put information that is not Keyword related, such as who in the office has the paper file, personal information about the client, specific instructions about using the new machine, etc. The contents of Notes are found when doing a Search. Would you like to see Notes?</p>
        
        <div class="input_container">
        
          <div>
            <input type="radio" name="show_notes" id="notesYes"  value="1" <?=$show_notes ==1 ? "checked" : '' ?>/>
            <label for="notesYes" class="radioLabel"><strong>Yes.</strong>I would like to activate notes.</label>
          </div>
          
          <div>
            <input type="radio" name="show_notes" id="notesNo"   value="0"  <?=$show_notes ==0 ? "checked" : '' ?> />
            <label for="notesNo" class="radioLabel"><strong>No.</strong>I do not want to activate notes.</label>
          </div>
        
        </div>
      
      </div> <!-- .preferences_container -->
      
      <div class="preferences_container">
      
         <?php if($organization_admin == $this->user_data['id']):?> 
        <h3>Database Export</h3>
        
        <p>You may want to export your database periodically so that you have a backup.  Also, should you want to cancel your account, you can export your database prior to canceling your account.  Use the links below to export your database.  Note, that if you have more than once database you will need to export each one individually.</p>
        
        <div class="input_container" style="padding: 10px 5px; margin-bottom: 20px;">
          Export (<?=htmlspecialchars($database->name)?>) Database in <a href="/databases/export">XML</a> or <a href="/databases/excel_export">EXCEL</a>
        </div>

                <?php endif;?>
      
      </div> <!-- .preferences_container -->        
      
      <div id="result"></div>
      
      <div id="submit_container" style="border-top: solid 2px black;">
        
        <span><input type="submit" name="submit" id="submit"  value="Update database settings"/></span>

        <span class="small gray"><span>or</span><a href="#" class="close red">Cancel</a></span>
         <?php 
         if($organization_admin == $this->user_data['id']) { ?> 
        <span id="trashcan" class="small right"><img src="<?=site_url()?>img/trashcan.png" alt="trashcan"/><a href="<?=site_url()?>databases/delete/" class="red" rel="facebox_fb modal_fb">Delete this database</a></span>
        <?php } ?>
      </div> <!-- #submit_container -->
      
    </form>   
  
  </div> <!-- #facebox_content -->
  
</div> <!-- #database_preferences_form -->