<?php include('./includes/includes.php');
$db=new database;
$settings=$db->query("SELECT * FROM `settings`");
foreach($settings as $setting){
	define(strtoupper($setting['name']),$setting['value']);
}
$limit=max(0,$_POST['page']?(ceil($_POST['page']-1)*ITEMS_PER_PAGE):0);
define('SQL_LIMIT',' LIMIT '.$limit.','.ITEMS_PER_PAGE.' ');