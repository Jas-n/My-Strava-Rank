<?php $app_require=array(
	'form.contact_subjects',
	'php.users'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
$existing_contacts=new existing_contact_subjects();
$new_contacts=new new_contact_subjects();
$existing_contacts->process();
$new_contacts->process();
require('header.php');?>
<div class="page-header">
	<h1>Contact Subjects</h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li>Management</li>
		<li class="active">Contact Subjects</li>
	</ol>
</div>
<?=$app->get_messages()?>
<div role="tabpanel">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#existing" aria-controls="existing" role="tab" data-toggle="tab">Existing</a></li>
		<li role="presentation"><a href="#new" aria-controls="new" role="tab" data-toggle="tab">New</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="existing">
			<?php $existing_contacts->get_form();?>
		</div>
		<div role="tabpanel" class="tab-pane" id="new">
			<?php $new_contacts->get_form(); ?>
		</div>
	</div>
</div>
<?php require('footer.php');