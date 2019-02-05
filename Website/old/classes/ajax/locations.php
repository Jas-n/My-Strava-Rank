<?php include_once('../init.php');
if(!$_GET || !$_GET['term']){
	die("Access Denied");
}
$locations=array();
$locations=$db->query(
	"SELECT * FROM `locations` WHERE `town` LIKE ? ORDER BY `town` ASC LIMIT 0,10",
	"%".$_GET['term']."%"
);
echo json_encode($locations);