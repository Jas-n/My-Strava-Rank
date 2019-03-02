<?php $app_require=array(
	'js.maps',
	'php.athletes',
	'php.locations'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Admin
	3=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('init.php');
if(!$location=$locations->get_location($_GET['id'])){
	header('Location: /');
	exit;
}
require('header.php');?>
<h1 class="m-b-0"><?=$location['town']?>, <?=$location['county']?>, <?=$location['country']?></h1>
<div class="hidden-md-up ad ad-banner">
	<a href="https://www.awin1.com/cread.php?s=390789&v=2547&q=179323&r=127430"><img src="https://www.awin1.com/cshow.php?s=390789&v=2547&q=179323&r=127430" border="0"></a>
</div>
<div class="row clearfix">
	<div class="col-md-6">
		<h2>About</h2>
		<table class="table table-sm table-hover table-striped">
			<tr>
				<th>Points</th>
				<td><?=number_format($location['points'])?></td>
			</tr>
		</table>
		<h2>Activities</h2>
		<div class="activities m-l-2">
			<h3 class="h4">All</h3>
			<table class="table table-sm table-hover table-striped">
				<tr>
					<th>Activities</th>
					<td><?=array_sum(array_column($location['activities'],'count'))?></td>
				</tr>
				<tr>
					<th>Distance</th>
					<td><?=number_format(array_sum(array_column($location['activities'],'miles')),2)?> miles</td>
				</tr>
				<tr>
					<th>Moving Time</th>
					<td><?=seconds_to_time(array_sum(array_column($location['activities'],'moving_hours'))*60*60)?></td>
				</tr>
				<tr>
					<th>Recording Time</th>
					<td><?=seconds_to_time(array_sum(array_column($location['activities'],'total_hours'))*60*60)?></td>
				</tr>
				<tr>
					<th>Wasted Time</th>
					<td><?=seconds_to_time((array_sum(array_column($location['activities'],'total_hours'))*60*60)-(array_sum(array_column($location['activities'],'moving_hours'))*60*60))?></td>
				</tr>
				<tr>
					<th>Total Elevation</th>
					<td><?=number_format(array_sum(array_column($location['activities'],'elevation_gain')),2)?> Miles</td>
				</tr>
			</table>
			<?php foreach($location['activities'] as $type=>$data){
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
	<div class="col-md-6">
		<h2 class="m-t-1">Stats</h2>
		<?php $elevation=$strava->elevation_like($total_elevation);
		if($elevation['max']){ ?>
			<p class="m-b-0"><?=$location['town']?> has ascended the equivelant of <?=$elevation['mountain']?> <?=$elevation['times']?> times.</p>
		<?php }else{ ?>
			<p class="m-b-0"><?=$location['town']?> is ascending the equivelant of <?=$elevation['mountain']?>.</p>
		<?php } ?>
		<progress class="progress" value="<?=$elevation['complete']?>" max="100"></progress>
		<?php $distance=$strava->distance_like($total_miles);
		if($distance['max']){ ?>
			<p class="m-b-0"><?=$location['town']?> has travelled the equivelant of <?=$distance['to']?> <?=$distance['times']?> times.</p>
		<?php }else{ ?>
			<p class="m-b-0"><?=$location['town']?> is travelling the equivelant of <?=$distance['to']?>.</p>
		<?php } ?>
		<progress class="progress" value="<?=$distance['complete']?>" max="100"></progress>
		<?php if($location['athletes']){ ?>
			<h2>Members</h2>
			<table class="table table-hover table-sm table-striped">
				<thead>
					<th>Name</th>
					<th>Gender</th>
					<th>Points</th>
				</thead>
				<tbody>
					<?php foreach($location['athletes']['data'] as $athlete){?>
						<tr>
							<td><a href="/athlete/<?=$athlete['id']?>"><?=$athlete['first_name'].' '.substr($athlete['last_name'],0,1)?></td>
							<td><?=$athlete['gender']?></td>
							<td><?=number_format($athlete['points'])?></td>
						</tr>
					<?php }?>
				</tbody>
			</table>
			<?php pagination($location['athletes']['count']); ?>
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
				center: {lat:'.$location['lat'].', lng: '.$location['lng'].'},
				zoom:10
			});
		}
	</script>'
);
require('footer.php');