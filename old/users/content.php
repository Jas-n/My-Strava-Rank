<?php $app_require=array(
	'form.content',
	'js.tinymce'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
$pg=$page->get_page($_GET['pid'],1);
$content=new content($pg);
$content->process();
include('header.php');?>
<div class="page-header">
	<h1>Content <small class="text-muted"><?=$pg['title']?></small></h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li><a href="./contents">Content</a></li>
		<?php if($pg['module']){ ?>
			<li><?=$app->all_modules[$pg['module']]['name']?></li>
		<?php } ?>
		<li class="active"><?=$pg['title']?></li>
	</ol>
</div>
<?=$app->get_messages().
$content->get_form();
include('footer.php');