<?php $app_require=array(
	'php.athletes',
	'php.clubs'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Admin
	3=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('init.php');
require('header.php');?>
<h1><?=SITE_NAME?></h1>
<div class="hidden-md-up ad ad-banner">
	<a href="https://www.awin1.com/cread.php?s=390789&v=2547&q=179323&r=127430"><img src="https://www.awin1.com/cshow.php?s=390789&v=2547&q=179323&r=127430" border="0"></a>
</div>
<div class="row m-b-3">
	<div class="col-sm-6">
		<h2>Newest Athletes</h2>
		<?php if($newest=$athletes->get_athletes('added:DESC,points DESC')){ ?>
			<table class="table table-hover table-sm table-striped">
				<thead>
					<th>Name</th>
					<th>Clubs</th>
					<th>Gender</th>
					<th>Points</th>
				</thead>
				<tbody>
					<?php foreach($newest['data'] as $new){?>
						<tr>
							<td><a href="/old/athlete/<?=$new['hex_id']?>"><?=$new['first_name'].' '.substr($new['last_name'],0,1)?></td>
							<td><?php if($new['clubs']){
								$clubs=array();
								foreach($new['clubs'] as $id=>$club){
									$clubs[]='<a href="/old/club/'.$club['hex_id'].'">'.$club['name'].'</a>';
								}
								echo implode(',<br>',$clubs);
							} ?></td>
							<td><?=$new['gender']?></td>
							<td><?=number_format($new['points'])?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php }?>
	</div>
	<div class="col-sm-6">
		<h2>Top Athletes</h2>
		<?php $tops=$athletes->get_athletes();
		if($tops['count']){ ?>
			<table class="table table-hover table-sm table-striped">
				<thead>
					<th>Name</th>
					<th>Clubs</th>
					<th>Gender</th>
					<th>Points</th>
				</thead>
				<tbody>
					<?php foreach($tops['data'] as $top){?>
						<tr>
							<td><a href="/athletes/<?=$top['hex_id']?>"><?=$top['first_name'].' '.substr($top['last_name'],0,1)?></td>
							<td><?php if($top['clubs']){
								$clubs=array();
								foreach($top['clubs'] as $id=>$club){
									$clubs[]='<a href="/club/'.$club['hex_id'].'">'.$club['name'].'</a>';
								}
								echo implode(',<br>',$clubs);
							} ?></td>
							<td><?=$top['gender']?></td>
							<td><?=number_format($top['points'])?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php }?>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<h2>Top Female Athletes</h2>
		<?php $tops=$athletes->get_athletes('points:desc',array('sex'=>'F')); ?>
		<table class="table table-hover table-sm table-striped">
			<thead>
				<th>Name</th>
				<th>Clubs</th>
				<th>Gender</th>
				<th>Points</th>
			</thead>
			<tbody>
				<?php if($tops['count']){
					foreach($tops['data'] as $top){?>
						<tr>
							<td><a href="/athlete/<?=$top['hex_id']?>"><?=$top['first_name'].' '.substr($top['last_name'],0,1)?></td>
							<td><?php if($top['clubs']){
								$clubs=array();
								foreach($top['clubs'] as $id=>$club){
									$clubs[]='<a href="/club/'.$club['hex_id'].'">'.$club['name'].'</a>';
								}
								echo implode(',<br>',$clubs);
							} ?></td>
							<td><?=$top['gender']?></td>
							<td><?=number_format($top['points'])?></td>
						</tr>
					<?php }
				}else{ ?>
					<tr>
						<td colspan="4">There are no matching athletes. <a href="<?=$strava_url?>">Add yourself</a> to the rankings.</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="col-sm-6">
		<h2>Top Male Athletes</h2>
		<?php $tops=$athletes->get_athletes('points:desc',array('sex'=>'M')); ?>
		<table class="table table-hover table-sm table-striped">
			<thead>
				<th>Name</th>
				<th>Clubs</th>
				<th>Gender</th>
				<th>Points</th>
			</thead>
			<tbody>
				<?php if($tops['count']){
					foreach($tops['data'] as $top){?>
						<tr>
							<td><a href="/athlete/<?=$top['hex_id']?>"><?=$top['first_name'].' '.substr($top['last_name'],0,1)?></td>
							<td><?php if($top['clubs']){
								$clubs=array();
								foreach($top['clubs'] as $id=>$club){
									$clubs[]='<a href="/club/'.$club['hex_id'].'">'.$club['name'].'</a>';
								}
								echo implode(',<br>',$clubs);
							} ?></td>
							<td><?=$top['gender']?></td>
							<td><?=number_format($top['points'])?></td>
						</tr>
					<?php }
				}else{ ?>
					<tr>
						<td colspan="4">There are no matching athletes. <a href="<?=$strava_url?>">Add yourself</a> to the rankings.</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php require('footer.php');