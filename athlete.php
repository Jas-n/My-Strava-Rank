<?php $app_require=array(
	'js.maps',
	'php.athletes'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Admin
	3=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('init.php');
if(!$athlete=$athletes->get_athlete($_GET['id'])){
	header('Location: /');
	exit;
}
require('header.php');
if($_GET['added']){
	$app->set_message('success','Hi '.$athlete['first_name'].', You\'ve now been added to our leaderboards. Your account will be updated based on the update procedure explained in our Frequently Asked Questions.');
}elseif($_GET['updated']){
	$app->set_message('information','Hi '.$athlete['first_name'].', You\'re already on our leaderboards. Thanks for coming back.');
}
$app->get_messages();?>
<h1 class="m-b-0"><?=$athlete['first_name'].' '.substr($athlete['last_name'],0,1)?></h1>
<p><em>User details updated <?=sql_datetime($athlete['updated'])?></em></p>
<div class="hidden-md-up ad ad-banner">
	<a href="https://www.awin1.com/cread.php?s=390789&v=2547&q=179323&r=127430"><img src="https://www.awin1.com/cshow.php?s=390789&v=2547&q=179323&r=127430" border="0"></a>
</div>
<div class="row clearfix">
	<div class="col-md-7">
		<h2>About</h2>
		<table class="table table-sm table-hover table-striped">
			<tr>
				<th>Points</th>
				<td colspan="2"><?=number_format($athlete['points'])?></td>
			</tr>
			<tr>
				<th rowspan="3">Ranks</th>
				<?php $i=0;
				foreach($athlete['ranks'] as $type=>$rank){
					if($i!=0){
						echo '<tr>';
					} ?>
						<td><?=ucwords($type)?></td>
						<td><?=ordinal($rank)?></td>
					</tr>
					<?php $i++;
				}?>
			<tr>
				<th>Gender</th>
				<td colspan="2"><?=$athlete['sex']=='M'?'Male':'Female'?></td>
			</tr>
			<tr>
				<th>Location</th>
				<td colspan="2"><?=implode(', ',array($athlete['location']['town'],$athlete['location']['county'],$athlete['location']['country']))?></td>
			</tr>
			<tr>
				<th>Joined Strava</th>
				<td colspan="2"><?=sql_datetime($athlete['strava_join'])?></td>
			</tr>
			<tr>
				<th>Joined <?=SITE_NAME?></th>
				<td colspan="2"><?=sql_datetime($athlete['added'])?></td>
			</tr>
			<?php if($athlete['last_activity']!=='0000-00-00 00:00:00'){ ?>
				<tr>
					<th>Last Activity</th>
					<td colspan="2"><?=sql_datetime($athlete['last_activity'])?></td>
				</tr>
			<?php } ?>
		</table>
		<h2 class="m-t-3">Activities</h2>
		<div class="activities m-l-2">
			<h3 class="h4">All</h3>
			<table class="table table-sm table-hover table-striped">
				<tr>
					<th>Activities</th>
					<td><?=array_sum(array_column($athlete['activities'],'count'))?></td>
				</tr>
				<tr>
					<th>Distance</th>
					<td><?=number_format(array_sum(array_column($athlete['activities'],'miles')),2)?> miles</td>
				</tr>
				<tr>
					<th>Moving Time</th>
					<td><?=seconds_to_time(array_sum(array_column($athlete['activities'],'moving_hours'))*60*60)?></td>
				</tr>
				<tr>
					<th>Recording Time</th>
					<td><?=seconds_to_time(array_sum(array_column($athlete['activities'],'total_hours'))*60*60)?></td>
				</tr>
				<tr>
					<th>Wasted Time</th>
					<td><?=seconds_to_time((array_sum(array_column($athlete['activities'],'total_hours'))*60*60)-(array_sum(array_column($athlete['activities'],'moving_hours'))*60*60))?></td>
				</tr>
				<tr>
					<th>Total Elevation</th>
					<td><?=number_format(array_sum(array_column($athlete['activities'],'elevation_gain')),2)?> Miles</td>
				</tr>
			</table>
			<?php foreach($athlete['activities'] as $type=>$data){
				if($data['count']){
					$total_elevation+=$data['elevation_gain'];
					$total_miles+=$data['miles'];?>
					<h3 class="h4"><?=ucwords($type)?></h3>
					<table class="table table-sm table-hover table-striped">
						<tr>
							<th><?=ucwords($type)?>s</th>
							<td><?=$data['count']?></td>
						</tr>
						<tr>
							<th>Distance</th>
							<td><?=number_format($data['miles'],2)?> miles</td>
						</tr>
						<tr>
							<th>Moving Time</th>
							<td><?=seconds_to_time($data['moving_hours']*60*60)?></td>
						</tr>
						<tr>
							<th>Recording Time</th>
							<td><?=seconds_to_time($data['total_hours']*60*60)?></td>
						</tr>
						<tr>
							<th>Wasted Time</th>
							<td><?=seconds_to_time(($data['total_hours']*60*60)-($data['moving_hours']*60*60))?></td>
						</tr>
						<tr>
							<th>Total Elevation</th>
							<td><?=number_format($data['elevation_gain'],2)?> Miles</td>
						</tr>
					</table>
				<?php }
			} ?>
		</div>
	</div>
	<div class="col-md-5">
		<h2>Stats</h2>
		<?php $elevation=$strava->elevation_like($total_elevation);
		if($elevation['max']){ ?>
			<p class="m-b-0"><?=$athlete['first_name']?> has ascended the equivelant of <?=$elevation['mountain']?> <?=$elevation['times']?> times.</p>
		<?php }else{ ?>
			<p class="m-b-0"><?=$athlete['first_name']?> is ascending the equivelant of <?=$elevation['mountain']?>.</p>
		<?php } ?>
		<progress class="progress" value="<?=$elevation['complete']?>" max="100"></progress>
		<?php $distance=$strava->distance_like($total_miles);
		if($distance['max']){ ?>
			<p class="m-b-0"><?=$athlete['first_name']?> has travelled the equivelant of <?=$distance['to']?> <?=$distance['times']?> times.</p>
		<?php }else{ ?>
			<p class="m-b-0"><?=$athlete['first_name']?> is travelling the equivelant of <?=$distance['to']?>.</p>
		<?php } ?>
		<progress class="progress" value="<?=$distance['complete']?>" max="100"></progress>
		<?php $clubs=$athletes->get_athlete_clubs($athlete['id'],false);
		if($clubs){ ?>
			<h2 class="m-t-3">Clubs</h2>
			<table class="table table-hover table-sm table-striped">
				<thead>
					<th>Rank</th>
					<th>Name</th>
					<th>Athletes</th>
					<th>Points</th>
				</thead>
				<tbody>
					<?php foreach($clubs as $i=>$club){ ?>
						<tr>
							<td><?=$i+1?></td>
							<td><a href="/club/<?=$club['hex_id']?>"><?=$club['name']?></td>
							<td><?=$club['athletes']?></td>
							<td><?=number_format($club['points'])?></td>
						</tr>
					<?php }?>
				</tbody>
			</table>
		<?php } ?>
		<h2 class="m-t-3">Location</h2>
		<div id="location_map"></div>
	</div>
</div>
<?php $app->add_to_foot(
	'<script>
		var map;
		function initMap(){
			var map=document.getElementById(\'location_map\');
			$(map).height($(map).width());
			map = new google.maps.Map(map,{
				center: {lat:'.$athlete['location']['lat'].', lng: '.$athlete['location']['lng'].'},
				zoom:10
			});
		}
	</script>'
);
require('footer.php');