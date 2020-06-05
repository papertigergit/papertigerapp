<?php
$package=(array($packages->result()));
if($package[0][0]->allow_db_import == 0)
{
	$this->session->set_flashdata('error', "Upgrade to Pro account to import databases.");
	redirect('/');
}
?>
<script type="text/javascript" src="<?=site_url()?>js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?=site_url()?>js/new_db_import.js"></script>
<style>
p, ol, #db_import_tool li, #db_import_tool label {font-family:arial; font-size:.9em; margin-bottom:1em;}
#db_import_tool img{border:solid 1px gray;}
</style>



<!--
/* 


<div id="db_tools_page">
  <h2>Import an old Paper Tiger database</h2>
  <img style="display:block; margin:0 auto;" src="http://localhost//img/imports_header.png" alt="Import your existing Paper Tiger Desktop database!">
  <h3 style="font-size:1.9em; text-align:center; margin: .6em 0">Want to import your existing Paper Tiger Desktop data?</h3>
  <h3 style="margin:1.5em 0 1em 0; border-bottom: solid 1px black; padding-bottom:3px;">Automatic Database Import Tool</h3>
  <p style="font-family:arial; font-size:.9em;">If you can browse to find your database and follow instructions included, you can try our Automatic Database Import tool. This process takes about 10 minutes and will import your data immediately.</p>
  <p style="font-family:arial; font-size:.9em; margin-top: 10px;">
    You will have to follow a set of simple steps to get your database converted. 
    <strong></strong>
    <a style="display:block; background:#C72028; padding:15px; font-size:1.5em; font-family:Arial; color:white; text-align:center; width:300px; margin: 10px auto;" href="http://thepapertiger.com/support/file_ticket.php?catId=230" id="db_import_tool_toggle" class="red" target="_blank">Continue with the Automatic Database Import Tool.</a>
  </p>
  <h3 style="margin:1.5em 0 1em 0; border-bottom: solid 1px black; padding-bottom:3px;">
    
Need Help?
  
    <em style="margin-left:5px">Relax! We've got you covered.</em>
  </h3>
  <p>
  Click the button below to open a support ticket and upload your database.  We will import your database for you into your Paper Tiger Online account.
  </p>
  <a style="display:block; background:#2b3e66; padding:15px; font-size:1.5em; font-family:Arial; color:white; text-align:center; width:300px; margin: 10px auto;" href="http://thepapertiger.com/support/file_ticket.php?catId=230" target="_blank">File a Support Ticket!</a>
  <h3 style="margin:1.5em 0 1em 0; border-bottom: solid 1px black; padding-bottom:3px;">
</h3>
  <div id="db_import_tool" style="margin-right: 0px; margin-left: 0px; background-attachment: scroll; background-repeat: repeat; background-image: none; background-position: 0% 0%; background-size: auto; background-origin: padding-box; background-clip: border-box; background-color: rgb(236, 236, 236); padding-right: 15px; padding-left: 15px; display: none; margin-top: 10px; margin-bottom: 10px; padding-top: 15px; padding-bottom: 15px">
    <h3 style="font-size:20px;">For Version 1, Version 2, or Version 3, skip to Step 3 below</h3>
    <hr>
    <h3 style="font-size:20px;">For Version 4:</h3>
    <br>
    <p style="font-size: 14px;">In order to import your existing Version 4 database into Paper Tiger Online, you will have to make sure of a couple of things before you upload your database. Follow this guide from start to finish. You will first clear all pending items from your database, and then create a backup file to upload to Paper Tiger Online.</p>
    <h2>1. Clear all pending transfers</h2>
    <p style="font-size: 14px;">In order to clear all pending transfers, do the following:</p>
    <ol style="font-size: 14px;">
      <li>Open Paper Tiger</li>
      <li>
        Click on the "Confirm" button
        <br>
        <br>
        <img src="http://support.thepapertiger.com/attachments/token/x2nwfq0wo3tojiu/?name=confirm_button.png" border="0" alt="confirm_button.png">
      </li>
      <li>
        Either confirm or cancel all the pending transfers
        <br>
        <br>
        <img src="http://support.thepapertiger.com/attachments/token/nk0l7jveech1nvk/?name=confirm_rows.png" border="0" alt="confirm_rows.png">
      </li>
    </ol>
    <h2>2. Make sure your backup file is current</h2>
    <p style="font-size: 14px;">To make sure your backup file is current, follow these steps:</p>
    <ol style="font-size: 14px;">
      <li>
        Go to File > Database Backup Manager
        <br>
        <br>
        <img src="http://support.thepapertiger.com/attachments/token/4xdv28855p4jg6p/?name=db_manager.png" border="0" alt="db_manager.png">
      </li>
      <li>
        Make sure you select "Create New Backup File" for the type of backup
        <br>
        <br>
        <br>
        <img src="http://support.thepapertiger.com/attachments/token/ut0ichxt9t8kyn5/?name=backup_type.png" border="0" alt="backup_type.png">
      </li>
      <li>
        Click on the "Backup Database" button
        <br>
        <br>
        <br>
        <img src="http://support.thepapertiger.com/attachments/token/fodcxcdi2eitzv4/?name=backup_button.png" border="0" alt="backup_button.png">
      </li>
      <li>
        Repeat steps 2 and 3 one more time. This will create 2 new backup files as seen here:
        <br>
        <br>
        <img src="http://support.thepapertiger.com/attachments/token/7wp2zzqg9dc6g46/?name=2files_2.png" border="0" alt="2files_2.png">
      </li>
      <li>
        Now select the 
        <strong>second to last</strong>
         backup file entry and click the "Archive Backup File" button
        <br>
        <br>
        <img src="http://support.thepapertiger.com/attachments/token/1scbod6cidzihph/?name=second.png" border="0" alt="second.png">
      </li>
      <li>Choose a place to save your backup file. We suggest you save it on your desktop so that you can find it later!</li>
    </ol>
    <p style="font-size: 14px;">You should be ready to import your database into Paper Tiger Online! If your database still fails to import, don't hesitate to open a ticket and send us your database! We will prepare it for you to import.</p>
    <h2>3. Upload it to Paper Tiger</h2>
    <form action="http://localhost/imports/new_database" method="POST" name="import_db" id="import_db_form" enctype="multipart/form-data">
      <label for="notes">
        Select your database backup file either 
        <strong>PTD, BAK, or MDF</strong>
        :
      </label>
      <br>
      <span style="font-size:12px;">
        (
        <a target="_blank" href="http://thepapertiger.com/support/articles.php?id=23348&catId=230">Where to find your database?</a>
        )
      </span>
      <br>
      <div id="new_db_import_upload">
        <input type="file" class="input new_db_file" name="file" id="file">
      </div>
     </form>
  </div>
</div>


*/	
-->
	<div id="db_tools_page_wrapper">
	
		<div id="db_tools_page">			
				
				<h2>Import an old Paper Tiger database</h2>
				
				<img style="display:block; margin:0 auto;" src="<?=site_url()?>/img/imports_header.png" alt="Import your existing Paper Tiger Desktop database!">
				
				<h3 style="font-size:1.9em; text-align:center; margin: .6em 0">Want to import your existing Paper Tiger Desktop data?</h3>
				
				<h3 style="margin:1.5em 0 1em 0; border-bottom: solid 1px black; padding-bottom:3px;">Automatic Database Import Tool</h3>
  <p style="font-family:arial; font-size:.9em;">If you can browse to find your database and follow instructions included, you can try our Automatic Database Import tool. This process takes about 10 minutes and will import your data immediately.</p>
  <p style="font-family:arial; font-size:.9em; margin-top: 10px;">
    You will have to follow a set of simple steps to get your database converted.
				

				
				<a style="display:block; background:#C72028; padding:15px; font-size:1.5em; font-family:Arial; color:white; text-align:center; width:300px; margin: 10px auto;" href="https://appdev2.thepapertiger.com/imports/new_database" id="db_import_tool_toggle" class="red" target="_blank" >Continue with the Automatic Database Import Tool.</a>
</p>
  
				
				<h3 style="margin:1.5em 0 1em 0; border-bottom: solid 1px black; padding-bottom:3px;">Need Help?<em style="margin-left:6px;">Relax!</em> We've got you covered.</h3>
				
				<p>Click the button below to open a support ticket and upload your database.  We will import your database for you into your Paper Tiger Online account.</p>
				<a style="display:block; background:#2b3e66; padding:15px; font-size:1.5em; font-family:Arial; color:white; text-align:center; width:300px; margin: 10px auto;" href="http://thepapertiger.com/support/" target="_blank" >File a Support Ticket!</a>
				<h3 style="margin:1.5em 0 1em 0; border-bottom: solid 1px black; padding-bottom:3px;"></h3>
				
				
				
										
					<div id="db_import_tool" style="margin:10px 0; background: #ececec; padding:15px; display:none">
					<br/><br/>
					    <h3 style="font-size:20px;">For Version 1, Version 2, or Version 3, skip to Step 3 below</h3>
    <hr>
    <h3 style="font-size:20px;">For Version 4:</h3>
    <br>
    <p style="font-size: 14px;">In order to import your existing Version 4 database into Paper Tiger Online, you will have to make sure of a couple of things before you upload your database. Follow this guide from start to finish. You will first clear all pending items from your database, and then create a backup file to upload to Paper Tiger Online.</p>
							<h2>1. Clear all pending transfers</h2>
							<p style="font-size: 14px;">In order to clear all pending transfers, do the following:</p>
							 <ol style="font-size: 14px;"> <li>Open Paper Tiger</li>
								 <li>Click on the "Confirm" button<br /><br />
								 <img src="http://support.thepapertiger.com/attachments/token/x2nwfq0wo3tojiu/?name=confirm_button.png" border="0" alt="confirm_button.png" /></li>
								 <li>Either confirm or cancel all the pending transfers<br /><br />
								 <img src="http://support.thepapertiger.com/attachments/token/nk0l7jveech1nvk/?name=confirm_rows.png" border="0" alt="confirm_rows.png" /></li> </ol>
								 
								<h2>2. Make sure your backup file is current</h2>
								<p style="font-size: 14px;">To make sure your backup file is current, follow these steps:</p> <ol style="font-size: 14px;">
									<li>Go to File &gt; Database Backup Manager<br /><br><img src="http://support.thepapertiger.com/attachments/token/4xdv28855p4jg6p/?name=db_manager.png" border="0" alt="db_manager.png" /></li>
									<li>Make sure you select "Create New Backup File" for the type of backup<br /><br /><br />
									<img src="http://support.thepapertiger.com/attachments/token/ut0ichxt9t8kyn5/?name=backup_type.png" border="0" alt="backup_type.png" /></li>
									<li>Click on the "Backup Database" button<br /><br /><br />
									<img src="http://support.thepapertiger.com/attachments/token/fodcxcdi2eitzv4/?name=backup_button.png" border="0" alt="backup_button.png" /></li>
									<li>Repeat steps 2 and 3 one more time. This will create 2 new backup files as seen here:<br /><br /><img src="http://support.thepapertiger.com/attachments/token/7wp2zzqg9dc6g46/?name=2files_2.png" border="0" alt="2files_2.png" /></li>
									<li>Now select the <strong>second to last</strong> backup file entry and click the "Archive Backup File" button<br /><br /><img src="http://support.thepapertiger.com/attachments/token/1scbod6cidzihph/?name=second.png" border="0" alt="second.png" /></li>
									<li>Choose a place to save your backup file. We suggest you save it on your desktop so that you can find it later!</li>
									</ol>
								<p style="font-size: 14px;">You should be ready to import your database into Paper Tiger Online! If your database still fails to import, don't hesitate to open a ticket and send us your database! We will prepare it for you to import.</p>

								<h2>3. Upload it to Paper Tiger</h2>
							
								<form action="<?=site_url()?>imports/new_database" method="POST" name="import_db" id="import_db_form" enctype="multipart/form-data">

									      <label for="notes">Select your database backup file either <strong>PTD, BAK, or MDF</strong>:</label><br />
									            <span style="font-size:12px;"> (<a target="_blank" href="http://thepapertiger.com/support/articles.php?id=23348&catId=230">Where to find your database?</a>)</span><br/>
							
									<div id="new_db_import_upload">
										<input type="file" class="input new_db_file" name="file" id="file"/>
									</div>
									<div class="clear"></div>

									<label for="name">Name the new database <span class="db_name_examples">("Johnson Family Files" or "Industrial Design Department")</span></label><br />
									<div id="new_db_import_upload">
										<input type="text" class="input new_db_name" name="name" id="name" value="<?= isset($name)&&$name!==FALSE?$name:''?>"/>
									</div>
									<div class="clear"></div>

									<input type="hidden" name="new_db" value="true"/>

									<div id="submit_container" class="db_import_submit">
										<span><input type="submit" name="sub" id="submit" value="Import my old database"/></span>
										<span class="small gray"><span>or</span><a href="/" class="red">Cancel</a></span>
									</div>														
							
								</form>	
						
								<?php if(count($errors)>0){?>
								<div id="messageBox" class="import_error error"><?php foreach($errors as $error){?>
									<p><?=$error?></p>
								<?php } ?></div>
								<?php } ?>
								<?php if(isset($success_msg) && $success_msg!=''){?><div class="success">
									<p><?=$success_msg?></p>
								</div><?php } ?>
						
					</div>
						
		</div> <!-- #db_tools -->
	
	</div> <!-- #db_tools_wrapper -->	

	<script type="text/javascript">
	var zenbox_params = {};
	zenbox_params.tab_id = "support";
	zenbox_params.hide_tab = true;
	zenbox_params.tab_color = "black";
	zenbox_params.title = "Paper Tiger Database Import Request";
	zenbox_params.text = "Get in touch with us and we'll schedule an appointment during regular business ours (9:00 AM to 5:00 PM Eastern +5:00 GMT). We'll give you a call and help you get your database into Paper Tiger!<br/><br/><strong>Make sure to include a phone number that we can reach you at so that we can schedule the appointment!</strong>";
	zenbox_params.tag = "pto_conversions";
	zenbox_params.url = "thepapertiger.zendesk.com";
	zenbox_params.email = "<?=$user_data['email_address'];?>";
	zenbox_params.subject = "<?=$user_data['first_name'];?> at <?=$user_data['organization_name'];?> needs help importing a database!";
	
	</script>
	<style type='text/css'>@import url('//assets0.zendesk.com/external/zenbox/overlay.css');</style>
	<script type='text/javascript' src='//assets0.zendesk.com/external/zenbox/overlay.js'></script>