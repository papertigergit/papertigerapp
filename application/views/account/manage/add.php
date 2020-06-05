<?php
echo form_open('account/manage/add');
echo form_input('dropbox_email','');
echo form_password('dropbox_password','');
echo form_submit('submit','Authorize Dropbox Account');
?>
