<?php
if($storage_auth->num_rows() == 0) {
	echo "No dropbox storage defined ";
	echo anchor('account/manage/add', 'Add dropbox storage', 'title="Add Storage"');
} else {
	foreach($storage_auth->result_array() as $key) {
		echo "Account Authenticated<br>";
		print_r($key);
		echo "<br>";
		echo anchor('account/manage/delete/'.$key['id'], 'Revoke credentials : '.$key['username'], 'title="Delete Storage"');
	}
}
?>
