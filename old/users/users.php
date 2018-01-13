<?php $app_require=array(
	'form.list_users',
	'php.users'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
$list_users=new list_users();
$list_users->process();
require('header.php');?>
<div class="page-header">
	<a class="btn btn-success pull-xs-right" href="./add_user" title="Add User">Add User</a>
	<h1>Users <small class="text-muted"><?=$list_users->count?></small></h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li class="active">Users</li>
	</ol>
</div>
<?php $app->get_messages().
$list_users->get_form();
require('footer.php');