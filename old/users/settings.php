<?php $app_require=array(
	'form.settings'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
$settings=new settings('name=settings&class=form-horizontal');
$settings->process();
require('header.php');?>
<div class="page-header">
	<h1>Settings</h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li class="active">Settings</li>
	</ol>
</div>
<?php $app->get_messages();
$settings->get_form();
require('footer.php');