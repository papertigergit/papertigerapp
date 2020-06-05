<?php
//Remove \r and \n from item names
function sanitize_items($item_name)
{
	$item_name=strtr ($item_name,chr(13),chr(32)); // replace carriage return with space. Fred, should I replace with nothing?
	$item_name=strtr ($item_name,chr(10),chr(32)); // replace line feed with space
	return $item_name; // Sane item name
}
$hostname="darkl0rd";
$username="root";
$password="maggie";
$database="paper_tiger";

$con_id=mysql_connect($hostname,$username,$password);
if(!$con_id) {
	die("DB Server has gone away");
} else {
	$db_id=mysql_select_db($database,$con_id);
	if($db_id) {
		//This is to test out the particular location
		//$query="select * from items where location_id=13468";
		$query="select * from items";
		$get_items=mysql_query($query);
		$counter=0;
		while($items_data=mysql_fetch_array($get_items,MYSQL_ASSOC)) {
			//loop over item names and remove all \r's and \n's
			$id=$items_data['id'];
			$name=sanitize_items($items_data['name']);
			$insert_id=mysql_query("update items set name='$name' where id='$id'");
			if($insert_id) {
				$counter++;
			}
		}
	}
	echo "Total Fixed ".$counter."\n";
}
?>
