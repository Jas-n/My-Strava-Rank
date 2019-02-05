<?php $default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
$pages=$page->get_pages(0,false);
require('header.php');?>
<div class="page-header">
	<h1>Content (<?=$pages['count']?>)</h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li class="active">Contents</li>
	</ol>
</div>
<table class="table table-hover table-striped">
	<thead>
		<th>ID</th>
		<th>Title</th>
		<th>Path</th>
		<th>Module</th>
		<th>Theme File?</th>
		<th>Actions</th>
	</thead>
	<tbody>
		<?php if($pages['count']){
			foreach($pages['pages'] as $pg){
				if($pg['content_count'] || $user->is_role('Formation')){?>
					<tr>
						<td><?=$pg['id']?></td>
						<td><?=$pg['title']?></td>
						<td><?=$pg['path']?></td>
						<td><?=$pg['module']?$app->all_modules[$pg['module']]['name']:''?></td>
						<td><?=$pg['theme_file']?'Yes':'No'?></td>
						<td><a class="btn btn-primary" href="./content?pid=<?=$pg['id']?>">Edit</a></td>
					</tr>
				<?php }
			}
		}else{ ?>
			<tr><td class='danger' colspan="4">No pages found</td></tr>
		<?php }?>
	</tbody>
</table>
<?php pagination($pages['count']);
require('footer.php');