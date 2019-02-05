<?php $default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
if(!$_GET['term']){
	header('Location: /');
	exit;
}
$app->add_to_head('<script>var term="'.$_GET['term'].'";</script>');
require('header.php');?>
<div class="page-header">
	<h1 id="title">Searching <small class="text-muted"><span id="location"></span> for "<?=$_GET['term']?>"</small></h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li class="active">Search</li>
	</ol>
</div>
<div id="searching"><div></div></div>
<ul class="nav nav-tabs" id="results_nav" role="tablist"></ul>
<div class="tab-content" id="results"></div>
<?php require('footer.php');