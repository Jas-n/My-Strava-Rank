<?php $app_require=array(
	'form.profile'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
$profile=new profile();
$profile->process();
include('header.php');?>
<div class="page-header">
	<h1>Profile</h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li class="active">Profile</li>
	</ol>
</div>
<?php $app->get_messages();
$profile->get_form();
include('footer.php');