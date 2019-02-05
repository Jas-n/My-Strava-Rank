<?php $app_require=array(
	'form.add_user'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
$add_user=new add_user();
$add_user->process();
include('header.php');?>
<div class="page-header">
	<h1>Add User</h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li><a href="./users">Users</a></li>
		<li class="active">Add User</li>
	</ol>
</div>
<?php $app->get_messages();
$add_user->get_form();
include('footer.php');