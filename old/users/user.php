<?php $app_require=array(
	'form.item_user',
	'js.tinymce'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
$item_user=new item_user($_GET['id']);
$item_user->process();
$usr=$user->get_user($_GET['id']);
include('header.php');?>
<div class="page-header">
	<h1><?=$usr['first_name']?> <?=$usr['last_name']?></h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li><a href="./users">Users</a></li>
		<li class="active"><?=$usr['first_name']?> <?=$usr['last_name']?></li>
	</ol>
</div>
<?php $app->get_messages();
$item_user->get_form();
include('footer.php');