<?php $default_permissions=array(
	2=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Admin
	3=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('init.php');
if(!template_exists($_GET['slug'])){
	include('error.php');
	exit;
}
require('header.php');
get_template(
	$_GET['slug'],
	array(
		'contents'	=>$page->contents,
		'page_title'=>$page->title
	)
);
require('footer.php');