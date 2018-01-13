<?php $default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
require('header.php');
$logs=$db->get_logs();?>
<div class="page-header">
	<h1>Logs <small class="text-muted"><?=number_format($logs['count'])?></small></h1>
	<ol class="breadcrumb">
		<li><a href="./">Dashboard</a></li>
		<li>Management</li>
		<li class="active">Logs</li>
	</ol>
</div>
<table class="table table-bordered table-hover table-sm table-striped">
	<thead>
		<tr>
			<th class="mw-10">Date</th>
			<th>Type</th>
			<th class="mw-10">Title</th>
			<th>Message</th>
			<th class="mw-10">User</th>
			<th>Trace</th>
		</tr>
	</thead>
	<tbody>
		<?php if($logs['logs']){
			foreach($logs['logs'] as $log){ ?>
				<tr<?=($log['level']==2?' class="table-warning"':($log['level']==1?' class="table-danger"':''))?>>
					<td><?=sql_datetime($log['date'])?></td>
					<td><?=log_level($log['level'])?></td>
					<td><?=$log['title']?></td>
					<td><?=$log['message']?></td>
					<td><?=$log['first_name']?> <?=$log['last_name']?></td>
					<td><?php if($log['data']){
						print_pre(json_decode($log['data'],1));
					}?></td>
				</tr>
			<?php }
		}else{?>
			<tr class="danger"><td colspan="7">No Logs found</td></tr>
		<?php }?>
	</tbody>
</table>
<?php pagination($logs['count']);
require('footer.php');