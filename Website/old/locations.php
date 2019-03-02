<?php $app_require=array(
	'js.maps',
	'php.locations'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Admin
	3=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('init.php');
$locals=$locations->get_locations();
require('header.php');
$from=($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0)+1;?>
<h1>Locations <small class="text-muted">Ranks <?=$from?> - <?=min($from+ITEMS_PER_PAGE-1,$locals['count'])?></small></h1>
<div class="hidden-md-up ad ad-banner">
	<a href="https://www.awin1.com/cread.php?s=390789&v=2547&q=179323&r=127430"><img src="https://www.awin1.com/cshow.php?s=390789&v=2547&q=179323&r=127430" border="0"></a>
</div>
<table class="table table-hover table-sm table-striped">
	<thead>
		<th>Rank</th>
		<th>Location</th>
		<th>Athletes</th>
		<th>Points</th>
	</thead>
	<tbody>
		<?php if($locals['count']){
			foreach($locals['data'] as $i=>$location){?>
				<tr>
					<td><?=($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0)+$i+1?></td>
					<td><a href="/location/<?=$location['id'].'-'.slug($location['town'])?>"><?=$location['town']?>, <?=$location['county']?>, <?=$location['country']?></td>
					<td><?=$location['athletes']?></td>
					<td><?=number_format($location ['points'])?></td>
				</tr>
			<?php }
		}else{ ?>
			<tr>
				<td colspan="4">There are no matching clubs. <a href="<?=$strava_url?>">Add yourself</a> to the rankings.</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
<?php pagination($ranks['count']); ?>
<p class="text-xs-center"><a class="btn btn-primary btn-xs" href="<?=$strava_url?>">Add Your Rank and Location</a></p>
<?php require('footer.php');