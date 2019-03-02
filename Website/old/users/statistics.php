<?php $app_require=array(
	'php.users',
	'php.statistics'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
include('header.php');?>
<div class="page-header">
	<h1>Statistics</h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li>Management</li>
		<li class="active">Statistics</li>
	</ol>
</div>
<div class="row">
	<?=$statistics->get_totals()?>
</div>
<div class="row">
	<?=$statistics->get_orphans().
	$statistics->get_users()?>
</div>
<?php include('footer.php');