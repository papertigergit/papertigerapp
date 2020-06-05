<?php
$conn=mysql_connect('localhost','root','');
mysql_select_db('paper_tiger',$conn);
$query=mysql_query("select id from items where id not in (select id from items where transfer_status_id = 0) and id not in (select id from items where transfer_status_id > 0)");
$i=0;
while($get_ids=mysql_fetch_array($query,MYSQL_ASSOC)) 
{
	$patch_query=mysql_query("update items set transfer_status_id=0 where id='".$get_ids['id']."'");
	$i++;
	echo $patch_query."\n";
}
echo "Total Rows Fixed : ".$i."\n";
?>
