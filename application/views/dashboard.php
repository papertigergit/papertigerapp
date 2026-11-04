<div id="dashboard">
  <?php
if($show_messages):
  if($show_messages->num_rows() > 0):
    $message_ids=array();
    foreach($show_messages->result() as $key){
      $messages_ids[]=$key->hide_msg;
    }
  endif;
endif;
$this_package=(array($packages->result()));
$messages=$this->Messeges_model->get_all();
if($messages->num_rows() > 0):
if ($this_package[0][0]->active_msg !=0 ) 
{
  $msg=$this->Messeges_model->get_by_id($this_package[0][0]->active_msg);
  if($msg):
        $show_msg=true;
        if($show_messages->num_rows() > 0):
            if($messages_ids):
              if(array_search($msg->id,$messages_ids) == FALSE):
                $show_msg=false;
              endif;  
            endif;  
        endif;    
    if($show_msg):
      ?>
  <div id="app_message" style="background:lightyellow; color:orange; border-top: solid 1px #bf6900; border-bottom: solid 1px #bf6900; padding: 10px; margin-bottom: 20px;"> 
    
    <!--title-->
    <h3 style="border-top:0; color:#bf6900; margin-bottom:5px; border-bottom: solid 1px #bf6900; padding-bottom:3px;">
      <?=$msg->title?>
      <a style="float:right; margin-right:10px; margin-bottom:-5px; color:#bf6900; font-size:smaller;" href="#hide" id="hide_app_message">Hide this message</a> </h3>
    
    <!--body-->
    <p style="margin:0; padding:0; margin-bottom:10px; color:black; font-size: .95em; font-family: arial;">
      <?=$msg->body?>
    </p>
    
    <!--link--> 
    <a style="display:block; margin: 0 auto; width:500px; text-align:center;" target="_BLANK" href="<?=$msg->anchor?>">
    <?=$msg->text?>
    </a>
    <div class="clear"></div>
  </div>
  <?php 
    endif;  
  endif;
}
endif;
?>
  <div id="dashboard_right"> <img src="<?=site_url()?>img/login/signup_header.png" alt="signup_header" width="190px" style="margin-bottom:20px;"/>
    <?php
  //print_r($organizaton_admin->admin_user);
  $package=(array($packages->result()));
   $num_of_dbs=$this->Database_model->count_dbs_from_organization_by_id($this->user_data['organization_id']); 
  $expert_idd=$this->Database_model->count_dbs_from_organization_by_id($this->user_data['organization_id']);
    ?>
    <?php if($is_network->num_rows() == 1 && $organization_admin->admin_user == $this->user_data['id']) { ?>
    <?php
  if($package[0][0]->max_databases == -1 || !$num_of_dbs )
  {
    ?>
    <a href="<?=site_url()?>databases/add_new" class="sprite new_database_button" rel="facebox nm"></a>
    <?php 
  }

  
  if(($package[0][0]->max_databases <= $num_of_dbs) ){
    if($package[0][0]->max_databases != -1)
      {
    ?>
    <a href="<?= site_url() ?>databases/upgrade" class="sprite new_database_button" rel="facebox"></a>
    <?php 
      }
  }
  ?>
    <?php if($packages->num_rows() == 0){ ?>
    <?php } ?>
    <?php
  if($package[0][0]->allow_db_import == 0)
  {
    ?>
    <a href="<?= site_url(); ?>imports/upgrade" class="sprite import_database_button" rel="facebox"></a>
    <?php
  }
  if($package[0][0]->allow_db_import == 1)
  {
    ?>
    <a href="<?= site_url() ?>imports/new_database" class="sprite import_database_button"></a>
    <?php
  }
}

  ?>
    <!--If not network user, show tabs-->
    <?php if($is_network->num_rows() == 0 ) { ?>
    <?php
    if($package[0][0]->max_databases == -1 || !$num_of_dbs )
    {
      ?>
    <a href="<?= site_url(); ?>databases/add_new" class="sprite new_database_button" rel="facebox nm"></a>
    <?php
    }

    
    if(($package[0][0]->max_databases <= $num_of_dbs) ){
      if($package[0][0]->max_databases != -1)
        {
      ?>
    <a href="<?= site_url(); ?>databases/upgrade" class="sprite new_database_button" rel="facebox"></a>
    <?php
        }
    }
    ?>
    <?php if($packages->num_rows() == 0){ ?>
    <?php } ?>
    <?php
    if($package[0][0]->allow_db_import == 0)
    {
      ?>
    <a href="<?= site_url() ?>imports/upgrade" class="sprite import_database_button" rel="facebox"></a>
    <?php
    }
    if($package[0][0]->allow_db_import == 1)
    {
      ?>
    <a href="<?= site_url() ?>imports/new_database" class="sprite import_database_button"></a>
    <?php
    }
  }

    ?>
    <?php if($databases->num_rows() > 0):
      ?>
    <div class="databases">Your Databases</div>
    <ul id="database_list">
      <?php foreach($databases->result() as $db)
    {

      if($organization_admin->admin_user == $this->user_data['id'] ):
               $pto_db_perms=3;
               $db_pto_umask=null;
      endif;         
      if($organization_admin->admin_user != $this->user_data['id'] ):
             $db_pto_umask=$this->Permissions_model->get_by_database($db->id,$this->user_data['id']);
      endif;       

           if($is_network):
             if($db_pto_umask):
               $pto_db_perms=$db_pto_umask->pto_umask;
             endif;       
             if(!$db_pto_umask):
               $pto_db_perms=0;
             endif;       
           if( ($is_network->num_rows() == 0) || ($is_network->num_rows() > 0 &&  $pto_db_perms > 0) || ($organization_admin->admin_user == $this->user_data['id'] )):  
      ?>
      <?php if($db->status == 1) { ?>
      <li><a href="<?= site_url(); ?>databases/use_db/<?=$db->id?>">
        <?= htmlspecialchars($db->name); ?>
        </a></li>
      <?php } else if($db->status == 0)
        {
        $date_added_plus_ten_mins=$db->date_added + 600;  //ten mins
        $time_now=time();
        if($date_added_plus_ten_mins > $time_now)
          { ?>
      <li><img class="spinner" src="<?php echo site_url(); ?>img/spinner.gif" />Importing
        <?= htmlspecialchars($db->name); ?>
        <br />
        <span class="small gray db_cancel"><a href="<?= site_url() ?>databases/cancel_import/<?= $db->id ?>" class="red" rel="facebox">Cancel Import</a></span> </li>
      <?php 
          } 
        if($date_added_plus_ten_mins < $time_now)
          { ?>
      <li>
        <?= $db->name; ?>
        (<a href="<?= site_url() ?>databases/delete_timedout_import/<?= $db->id; ?>" class="red" rel="facebox">Import failed</a>)</li>
      <?php
          } 
        }
      else if($db->status == 2){ ?>
      <li>
        <?= $db->name; ?>
        (<a href="<?= site_url() ?>databases/delete_import/<?= $db->id; ?>" class="red" rel="facebox">Import failed</a>)</li>
      <?php } else if($db->status == 3){ ?>
      <li>
        <?= $db->name; ?>
        (<a href="<?= site_url() ?>databases/delete_import/<?= $db->id ?>" class="red" rel="facebox">Delete Import</a>)</li>
      <?php } else if($db->status == 4){ ?>
      <li>
        <?= $db->name; ?>
        (<a href="<?= site_url() ?>databases/delete_import/<?= $db->id; ?>" class="red" rel="facebox">Delete Import</a>)</li>
      <?php } else if($db->status == 5){ ?>
      <li>
        <?= $db->name; ?>
        (<a href="<?= site_url() ?>databases/delete_import_review/<?= $db->id; ?>" class="red" rel="facebox">Import failed</a>)</li>
      <?php }
      ?>
      <?php       endif;
    endif;
           if(!$is_network):
      ?>
      <?php if($db->status == 1) {?>
      <li><a href="<?=site_url()?>databases/use_db/<?= $db->id ?>">
        <?= htmlspecialchars($db->name) ?>
        </a></li>
      <?php } else if($db->status == 0)
        {
        $date_added_plus_ten_mins=$db->date_added + 600;  //ten mins
        $time_now=time();
        if($date_added_plus_ten_mins > $time_now)
          { ?>
      <li><img class="spinner" src="<?php echo site_url(); ?>img/spinner.gif" />Importing
        <?= htmlspecialchars($db->name) ?>
        <br />
        <span class="small gray db_cancel"><a href="<?= site_url() ?>databases/cancel_import/<?= $db->id ?>" class="red" rel="facebox">Cancel Import</a></span> </li>
      <?php 
          } 
        if($date_added_plus_ten_mins < $time_now)
          { ?>
      <li>
        <?= $db->name ?>
        (<a href="<?= site_url() ?>databases/delete_timedout_import/<?= $db->id ?>" class="red" rel="facebox">Import failed</a>)</li>
      <?php
          } 
        }
      else if($db->status == 2){ ?>
      <li>
        <?=$db->name?>
        (<a href="<?= site_url() ?>databases/delete_import/<?= $db->id ?>" class="red" rel="facebox">Import failed</a>)</li>
      <?php } else if($db->status == 3){ ?>
      <li>
        <?= $db->name; ?>
        (<a href="<?= site_url() ?>databases/delete_import/<?= $db->id ?>" class="red" rel="facebox">Delete Import</a>)</li>
      <?php } else if($db->status == 4){ ?>
      <li>
        <?= $db->name; ?>
        (<a href="<?= site_url() ?>databases/delete_import/<?= $db->id ?>" class="red" rel="facebox">Delete Import</a>)</li>
      <?php } else if($db->status == 5){ ?>
      <li>
        <?=$db->name?>
        (<a href="<?= site_url() ?>databases/delete_import_review/<?= $db->id ?>" class="red" rel="facebox">Import failed</a>)</li>
      <?php }
      ?>
      <?php       endif;
    } //endfor 
  ?>
      <?php
      //echo "<pre>";
      //print_r($databases);
      //echo "</pre>";
      ?>
    </ul>
    <!--
      <div class="tip">
        <p>
          TIP: If you want to delete a database, you can do so by entering a database and then navigating to the "Preferences" page.
        </p>
      </div>
      --> 
    
    <script type="text/javascript" src="<?= site_url() ?>js/jquery.timers-1.2.0.js"></script> 
    <script type="text/javascript">

$(document).ready(function(){
    $("#database_list").everyTime(60000,function(i){
      $.ajax({
        url: "<?php echo site_url(); ?>imports/db_status",
        cache: false,
        success: function(html){

        if(html=="session_expired")
        {
        //  $("#database_list").html("Out");
        //  $('a[rel*=facebox]').unbind('click');       
        //  $('a[rel*=facebox]').facebox();
        }
        else
        {
          $("#database_list").html(html);
          $('a[rel*=facebox]').unbind('click');       
          $('a[rel*=facebox]').facebox();
        } 
        }
      })
    })
});

</script>
    <?php else: ?>
    <?php if($is_network->num_rows() == 1 && $organization_admin->admin_user != $this->user_data['id']): ?>
    <h2 style="border: solid 1px red; background: pink; color: red; padding:10px;">You do not have access to any databases in this account. Contact
      <?= $this->User_model->get_user($organization_admin->admin_user)->row()->first_name ?>
      <?= $this->User_model->get_user($organization_admin->admin_user)->row()->last_name?>
      (your account administrator) at <a href="mailto:<?= $this->User_model->get_user($organization_admin->admin_user)->row()->email_address ?>">
      <?= $this->User_model->get_user($organization_admin->admin_user)->row()->email_address ?>
      </a> to request access.</h2>
    <?php else: ?>
    <img src="<?php echo site_url(); ?>img/no_db.png" style="display:block; margin-left: 22px;"/>
    <?php endif; ?>
    <?php endif; ?>
    <hr style="width:250px;margin:0 auto;text-align:center;margin-top:15px;margin-bottom:15px;" />
    <div class="tip">
      <div class="databases">Your Digital Locations</div>
      <?php
        $time=time();
        $google_users=$this->google_users_model->get_token($this->user_data['id']);
         ?>
      <ul style="color:black;">
        <li> <span class="person_name" style="vertical-align:middle;"><a target="_blank" href="https://drive.google.com/drive/my-drive">Google Drive</a>
          <?php if($google_users->num_rows() > 0): ?>
          <span class="google_docs_active_label">Active</span>
          <?php endif; ?>
          </span> </li>
            <?php
          foreach($users->result() as $user_ids) {
        if($this->user_data['id'] != $user_ids->id):
          $login_history=$this->User_model->get_user($user_ids->id)->row();
        ?>
        <?php 
            endif;
        } 
        ?>
      </ul>
    </div>
    
    <div class="tip">
    <div class="databases">People in your Account</div>
      <?php
        $time=time();
         ?>
      <ul style="color:black;">
      <div class="people">
        <li> <span class="person_name" style="vertical-align:middle;">
          <?= $this->user_data['first_name'] ?>
          <?= $this->user_data['last_name'] ?>
          <?php if($this->user_data['id'] == $organization_admin->admin_user): ?>
          <span class="admin_label">Admin</span>
          <?php endif; ?>
          </span> <span class="last_seen" style="display:block; font-size:.8em; color:gray;">You are logged in right now</span> </li>
        <?php
          foreach($users->result() as $user_ids) {
        if($this->user_data['id'] != $user_ids->id):
          $login_history=$this->User_model->get_user($user_ids->id)->row();
        ?>
        <li> <span class="person_name">
          <?= $login_history->first_name ?>
          <?= $login_history->last_name ?>
          <?php if($user_ids->id == $organization_admin->admin_user): ?>
          <span span class="admin_label">Admin</span>
          <?php endif; ?>
          </span>
          <?php if($login_history->last_login == 0): ?>
          <span class="last_seen" style="display:block; font-size:.8em; color:gray;">Never logged in</span>
          <?php endif; ?>
          <?php if($login_history->last_login > 0): ?>
          <span class="last_seen" style="display:block; font-size:.8em; color:gray;">Last seen
          <?= time_in_words($login_history->last_login) ?>
          ago</span>
          <?php endif; ?>
        </li>
        <?php 
            endif;
        } 
        ?>
        <?php
      $total_users = $this->User_model->get_user_count_by_org($this->user_data['organization_id']);       if ($total_users > 3){ ?>
        <p><span class="more_people" style="margin-left:-11px;display:block;float:left; font-size:.8em; color:gray">See More</span></p>
        <?php
        }else echo " "; ?>
            </div>  <!-- end .people -->
      
  

      </ul>
    </div>    
    
<hr style="width:250px;margin:0 auto;text-align:center;margin-top:15px;margin-bottom:15px;" />
    <div class="tip">
      <div class="databases">Tutorials & Training</div>
      <div id="side_webinars"> 
        <!-- Dynamic YouTube video pull -->
          <?php $webisode = file_get_contents("http://www.thepapertiger.com/curlwebinar"); 
          echo $webisode;
      ?>
        <ul id="youtube_webinars">
          <li><a target="_blank" href="<?php echo $video_page??""; ?>"><?php echo $media->group->title??""; ?></a></li>
        </ul>

<div style="margin:0 auto;margin-bottom:26px;cursor:pointer;text-align:center;">
<a style="background-color:#D09815;color:#fff;" class="button round medium" href="http://thepapertiger.com/training" target="_blank">Watch Our Training Videos</a>
</div>

        <hr style="width:250px;margin:0 auto;text-align:center;margin-top:15px;margin-bottom:15px;" />
      </div>
    </div>
  </div>

    <?php if($is_network->num_rows() > 0): ?>
        
    <?php endif; ?>
    <div id="dashboard_left">
    <div id="dashboard_header">Dashboard</div>
    <div id="dashboard_content">
      <div class="dashboard_post">
        <?php
/*
$document = "http://www.thepapertiger.com/blog/"

function html2txt($document){
     $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
     '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
     '@<[?]php[^>].*?[?]>@si', //scripts php
     '@<[?][^>].*?[?]>@si', //scripts php
     '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
     '@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments including CDATA
     );$text = preg_replace($search, '', $document);
     return $text;
     echo $text;
}
*/

?>
        <h2>Welcome to Paper Tiger Online!</h2>
        <p></p>
        <p>
        
         <?php $org_expert = $this->Organization_model->get_org_expert($this->user_data['organization_id']); ?> 
        
        </p>
        <?php $this->Organization_model->get_approved_by_expert($this->user_data['affiliate_code']) ?>
        <?php $q = $this->Organization_model->get_pending_by_expert($this->user_data['affiliate_code']);
/*
    
    print_r($q);
    
    foreach ($q->result() as $r){
      echo '<li>' . $r->name . '</li>';
        }  
*/  
        ?>


                

        <p>You are now using the simplest filing system in the world. You can easily <strong>find anything in your office in 5 seconds or less...Guaranteed!</strong></p>
                 
       <p>All of our products are designed to be easy to learn and simple to use. We have prepared some great learning materials to help you better understand great ways to use both <a href="http://thepapertiger.com/tour" target="_blank">Paper Tiger</a> and <a target="_blank" href="http://thepapertiger.com/digital_tiger">Digital Tiger</a> that includes <a href="http://thepapertiger.com/blog/category/webinars" target="_blank">webinars</a> and <a href="http://thepapertiger.com/support" target="_blank">knowledge base articles</a>. See also the right sidebar of this Dashboard page for these learning materials or simply click on the HELP button in the upper right corner. You will be redirected to our Support Portal where you can search for an answer; or click on the Submit a Request link at the top of any support page to file a support ticket request.</p>
       <p>If you have the Basic or Pro plan, in addition to finding your paper files (or other physical items such as storage boxes, etc.), you will be able to find your digital files using the Digital Tiger feature! Paper Tiger Online Basic or Pro plans will connect to your Google Drive account, activating Digital Tiger so that you can find all of your information with just one search to find your paper and other physical items you've indexed in Paper Tiger, and find your digital files that you've stored in Google Drive. Google Drive is a fantastic tool that is very powerful and an extremely low cost digital file storage location. </p>
        
  <span style="color:black;">Here are some things to note:</span>
  <ul>
  <p>(1) Survey - Don't forget to let us know about your initial experience connecting to and using Digital Tiger by completing the <a href="http://www.surveymonkey.com/s/DigitalTigerBeta001" target="_blank">survey</a>.</p>
  <p>(2) Paper Tiger is for indexing the paper files you need to keep in hard copy format, as well as other physical items, such as instruction binders, books, CDs, DVDs, Flashdrives, home or office inventory, and storage boxes, etc. See our examples on our <a target="_blank" href="http://www.thepapertiger.com/tour#4">Not Just for Filing Paper</a> page. Basically anything you can put a number onto, you can Paper Tiger! </p>
<p>(3) Digital Tiger Screencasts - We have two Digital Tiger training screencasts. These will help you easily learn how to set up and start using the Digital Tiger feature. See our <a target="_blank" href="http://www.thepapertiger.com/blog/started-digital-tiger-paper-digital-filing-solution/">BLOG</a> post on these new screencasts.</p>
<p>(4) Productivity Experts - If you need one-on-one assistance getting organized, there are professional productivity experts/organizers who have been certified to help you implement Paper Tiger. They are trained to work with you to find the most efficient and productive way to manage your work and personal life. Click on the Choose an Expert tab above to peruse through the experts and make a selection.  

</p>
  </ul>         
        <p>Sincerely,<br />
          The Paper Tiger Team </p>
      </div>      <!-- end of dashboard_post -->
      
      <div class="dashboard_blog_post">
        <div id="latest_news" style="margin-bottom:20px;">Recent Blog Post<span id="recent_blog_toggle"> [-]</span></div>
        <div class="recent_blog_post center">
          <?php $document = file_get_contents("http://www.thepapertiger.com/curlpage"); 
          echo $document;
      ?>
          <!-- <p style="float:right;position:relative;top:-45px;margin"><a href="http://www.thepapertiger.com/blog" target="_blank">Go To Blog</a></p> -->
          <div id="blog_buttons"> <a href="http://www.thepapertiger.com/blog" target="_blank" class="activity_type" style="vertical-align:middle; border: none;float:left;position:relative;top:-45px;"> <span class="location_button" style="display:block; padding:3px; text-align:left; background:#2ba4d9; color:white;">Read more...</span> </a> <a href="http://www.thepapertiger.com/blog" target="_blank" class="activity_type" style="vertical-align:middle; border: none;float:right;position:relative;top:-45px;"> <span class="location_button" style="display:block; padding:3px; text-align:right; background:#2ba4d9; color:white;">Go To Blog</span> </a> </div>
          <!-- end blog_buttons --> 
        </div>
        <!-- end recent_blog_post --> 
      </div>
      <!-- end of dashboard_post -->
      
<?php 

$user_orgs = $this->Organization_model->get_org_expert($this->user_data['organization_id']);
$e = $this->Organization_model->get_this_org_expert($org_expert);
foreach ($e->result() as $d){

if ($d->affiliate_level == 3) {
if ($user_orgs != '0' && $d->affiliate_level != 2){
      $has_affiliate_code = $this->Organization_model->assign_expert($this->user_data['organization_id']);
?>     
    <div id="anchortext" class="my_expert">
<div id="latest_news" style="margin-bottom:20px;">My Expert<span id="expert_toggle"> [-]</span></div>
        <div class="my_expert_choose center">
          <center>
          <?php $j = $this->user_data['organization_id'];?>
          <?php $t = $this->Organization_model->get_org_expert($j);?>
          <?php $has_expert = $this->Organization_model->get_has_expert($this->user_data['organization_id']);?> 
        <?php 
    foreach ($e->result() as $d){ ?>
    <?php if ($d->affiliate_photo != ""){ ?>
    <center>
    <img style="margin:0 auto;" width="200px" height="250px" src="<?php echo $d->affiliate_photo ;?>" />
        </center>
        <?php } ?>
        
    <ul id="expert_profile">
            <li><span id="expert_user"><b>Name:</b></span> <span id="expert_info"><?php if ($has_expert == '0'){echo "<b style=\"color:red\">Pending Request From:</b> "; }else{}echo $d->first_name .' '. $d->last_name ;?></span></li>
            <li><span id="expert_user"><b>E-Mail:</b></span> <span id="expert_info"><?php echo $d->email_address; ?></span></li>
            
            <?php if ($has_expert != '0'){ ?>
            
            <?php if ($d->phone_number != ""){ ?>
            <li><span id="expert_user"><b>Phone:</b></span> <span id="expert_info"><?php if ($has_expert != '0')echo $d->phone_number; ?></span></li>
            <?php } ?>
              <?php if ($d->website != ""){ ?>
            <li><span id="expert_user"><b>Website:</b></span> <span id="expert_info"><a href=<?php echo $d->website ;?>><?php echo $d->website; ?></a></span></li>
            <?php } ?>
            <br/>   
    </ul>
    
    <?php if ($d->profile != ""){ ?>
    </center>
    <div id="text_profile"><?php echo $d->profile; ?>
    </div>
    <?php } ?>
    <?php } ?> <!-- if has_expert private dat -->

    <?php } ?> <!-- end foreach -->
         

<center>
<div style="margin-top:10px;margin-bottom:20px;">
<?php
if ($has_expert == '0'){ ?>
  <a style="margin-right:15px;cursor:pointer" href="<?= site_url() ?>affiliates/change_expert_page" class="activity_type nice white small">
    <span class="" >Change</span>
  </a>
  <a style="cursor:pointer" href="<?= site_url() ?>dashboard/remove_expert/" rel="facebox nm" class="activity_type nice small white" >
    <span class="" >Remove</span>
  </a>
<?php }else if ($has_expert == '1' && $has_affiliate_code == ""){ ?>
  <a style="margin-right:15px;cursor:pointer" href="<?= site_url() ?>affiliates/change_expert_page" class="activity_type nice small white ">
    <span class="" >Change</span>
  </a>
    <a style="cursor:pointer" href="<?= site_url() ?>dashboard/remove_expert/" rel="facebox nm" class="activity_type nice small white" >
    <span class="" >Remove</span>
  </a>
<?php }else if ($has_expert == '1' && $has_affiliate_code != $d->affiliate_code){ ?>
  <a style="margin-right:15px;cursor:pointer" href="<?= site_url() ?>affiliates/change_expert_page" class="activity_type nice small white ">
    <span class="" >Change</span>
  </a>
    <a style="cursor:pointer" href="<?= site_url() ?>dashboard/remove_expert/" rel="facebox nm" class="activity_type nice small white" >
    <span class="" >Remove</span>
  </a>
<?php } ?>
</div>
</center>
        </div>
        <!-- end  --> 
      </div>
      <!-- end of my_expert -->

<?php } } } ?> <!-- end if ($user_orgs) -->      
      
      <div id="latest_news" style="margin-bottom:20px;">Recent Activity <span id="recent_toggle">[+]</span></div>
      <div class="recent_switch">
        <?php
      
        if($this->Database_model->count_dbs_from_organization_by_id($this->user_data['organization_id']) == 0) {
          echo('
          
          <h2 style="display:block; margin: 30px auto; color:lightgray; font-size:1.6em; text-align:center;">No recent activity. Create a database to get started!</h2>
          
          ');
        }     
      
      foreach($databases->result() as $database_handlers)      
      {
      //  if($is_network->num_rows() == 0 || $organization_admin->admin_user == $this->user_data['id']):
        
        if($database_handlers->status == 1)
        {
      ?>
        <table class="recent_activity" id="recent_activity" border="0" style="border: none; padding:10px; font-size:.8em; margin-bottom:20px;">
          <thead style="background: #ececec;">
            <tr style="display:none;"> <span class="db_name" style="font-family: Trebuchet MS; font-size: 1.2em; padding-left:10px; padding-bottom:5px; display:block; margin-bottom:10px; margin-top:10px; border-bottom: solid 1px black;">
              <?= $database_handlers->name;?>
              </span>
              <th>Type</th>
              <th>Action</th>
              <th>By</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php
      if($logs_databases):
        foreach($logs_databases as $key)
        {
          $this_db_perms=$this->Permissions_model->get_by_database($database_handlers->id,$this->user_data['id']);
          $this_database_id=array_search($database_handlers->id,$key);
          if($this_database_id)
          {
            if($key['action']==0)
            {
              $action_type="was created";
            }
            if($key['action']==1)
            {
              $action_type="was renamed";
            }
            if($key['action']==10)
            {
              $action_type="trash was cleared";
            }
            if($key['action']==13)
            {
              $action_type="was imported";
            }
            if($key['action']==15)
            {
              $action_type="was assigned";
            }
            $this_user=$this->User_model->get_user($key['by_who_id'])->result();
            $first_name='';
            $last_name='';
            foreach($this_user as $names)
            {
              $first_name=$names->first_name;
              $last_name=$names->last_name;
            }
            if(!$first_name){
              $first_name=$this->user_data['first_name'];
            }
            if(!$last_name){
              $last_name=$this->user_data['last_name'];
            }

            $action_date=date("m/d/y",(int) $key['action_time']);?>

            <tr style=" border: none; border-bottom: solid 1px #efefef;">
              <td class="activity_type" style="vertical-align:middle; border: none;"><span class="database" style="display:block; padding:3px; text-align:right; background:#fe6914; color:white;">Database</span></td>
              <td class="activity_action" style="vertical-align:middle; border: none;">"
                <?= htmlspecialchars($database_handlers->name) ?>
                "
                <?= htmlspecialchars($action_type) ?></td>
              <td class="activity_owner" style="vertical-align:middle; border: none;"><span class="by" style="color:gray">by</span>
                <?= htmlspecialchars($first_name) ?>
                <?= htmlspecialchars($last_name) ?></td>
              <td class="activity_time" style="vertical-align:middle; border: none;"><?= $action_date ?></td>
            </tr>
            <?php }
          ?>
            <?php 
        }
      endif;  
        foreach($logs_locations as $key)
        {
          if($key['database_id']==$database_handlers->id)
          {
            if($key['action']==0)
            {
              $action_type="was created";
            }
            if($key['action']==1)
            {
              $action_type="was modified";
            }
            if($key['action']==2)
            {
              $action_type="was deleted";
            }
            $this_user=$this->User_model->get_user($key['by_who'])->result();
            foreach($this_user as $names)
            {
              $first_name=$names->first_name;
              $last_name=$names->last_name;
            }

            $action_date=date("m/d/y",(int) $key['action_time']);?>
            
            <tr style=" border: none; border-bottom: solid 1px #efefef;">
              <td class="activity_type" style="vertical-align:middle; border: none;"><span class="location" style="display:block; padding:3px; text-align:right; background:#2ba4d9; color:white;">Location</span></td>
              <td class="activity_action" style="vertical-align:middle; border: none;">"
                <?= htmlspecialchars($key['name']) ?>
                "
                <?= htmlspecialchars($action_type) ?></td>
              <td class="activity_owner" style="vertical-align:middle; border: none;"><span class="by" style="color:gray">by</span>
                <?= htmlspecialchars($first_name) ?>
                <?= htmlspecialchars($last_name) ?></td>
              <td class="activity_time" style="vertical-align:middle; border: none;"><?= $action_date ?></td>
            </tr>
            <?php } ?>
            <?php   
        }
        foreach($logs_categories as $key)
        {
            if($key['database_id']==$database_handlers->id)
            {
              if($key['action']==0)
              {
                $action_type="was created"; 
              }
              if($key['action']==1)
              {
                $action_type="was modified";
              }
              if($key['action']==2)
              {
                $action_type="was deleted";
              }
              $this_user=$this->User_model->get_user($key['by_who'])->result();
              foreach($this_user as $names)
              {
                $first_name=$names->first_name;
                $last_name=$names->last_name;
              }
            $action_date=date("m/d/y",(int) $key['action_time']);?>
            <tr style=" border: none; border-bottom: solid 1px #efefef;">
              <td class="activity_type" style="vertical-align:middle; border: none;"><span class="category" style="display:block; padding:3px; text-align:right; background:#e94148; color:white;">Category</span></td>
              <td class="activity_action" style="vertical-align:middle; border: none;">"
                <?= htmlspecialchars($key['name']) ?>
                "
                <?= $action_type ?></td>
              <td class="activity_owner" style="vertical-align:middle; border: none;"><span class="by" style="color:gray">by</span>
                <?= htmlspecialchars($first_name) ?>
                <?= htmlspecialchars($last_name) ?></td>
              <td class="activity_time" style="vertical-align:middle; border: none;"><?= htmlspecialchars($action_date) ?></td>
            </tr>
            <?php } ?>
            <?php   
        }
        foreach($logs_items as $key)
        {
          if($key['database_id']==$database_handlers->id)
          {
            if($key['action']==0)
            {
              $action_type="was filed";
            }
            if($key['action']==1)
            {
              $action_type="was edited";
            }
            if($key['action']==2)
            {
              $action_type="was tossed";
            }
            if($key['action']==3)
            {
              $action_type="is pending toss";
            }
            if($key['action']==4)
            {
              $action_type="was duplicated";
            }
            if($key['action']==5)
            {
              $action_type="is pending duplication";
            }
            if($key['action']==6)
            {
              $action_type="was moved";
            }
            if($key['action']==7)
            {
              $action_type="is pending move";
            }
            if($key['action']==8)
            {
              $action_type="was merged (source)";
            }
            if($key['action']==9)
            {
              $action_type="was merged (destination)";
            }
            if($key['action']==11)
            {
              $action_type="was permenetly deleted";
            }
            if($key['action']==12)
            {
              $action_type="was restored from trash";
            }
            $this_user=$this->User_model->get_user($key['by_who'])->result();
            foreach($this_user as $names)
            {
                $first_name=$names->first_name;
                $last_name=$names->last_name;
            }
            $action_date=date("m/d/y",(int) $key['action_time']);?>
            <tr style=" border: none; border-bottom: solid 1px #efefef;">
              <td class="activity_type" style="vertical-align:middle; border: none;"><span class="item" style="display:block; padding:3px; text-align:right; background:#3da77f; color:white;">Item</span></td>
              <td class="activity_action" style="vertical-align:middle; border: none;">"
                <?= htmlspecialchars($key['name']) ?>
                "
                <?= htmlspecialchars($action_type) ?></td>
              <td class="activity_owner" style="vertical-align:middle; border: none;"><span class="by" style="color:gray">by</span>
                <?= htmlspecialchars($first_name) ?>
                <?= htmlspecialchars($last_name) ?></td>
              <td class="activity_time" style="vertical-align:middle; border: none;"><?= $action_date ?></td>
            </tr>
            <?php } ?>
            <?php } ?>
          </tbody>
        </table>
        <?php 
        }
      //  endif;
      } 
      ?>
      </div>
      <!-- end recend_switch --> 
      
    </div>
  </div>
</div>
<?php 

   $this->User_model->last_login($this->user_data['id']); 
   $last_login=$this->User_model->get_user($this->user_data['id'])->row()->logins;
   if($last_login == "0"): 

   ?>
<script>jQuery.facebox({ ajax: '/first_login/' });</script>
<?php endif; ?> 
