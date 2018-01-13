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
$ranks=$athletes->get_athletes();
if(!$ranks['data']){
	header('Location: athletes');
	exit;
}
require('header.php');
$from=($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0)+1;?>
<h1>Athletes <small class="text-muted">Ranks <?=$from?> - <?=min($from+ITEMS_PER_PAGE-1,$ranks['count'])?></small></h1>
<div class="hidden-md-up ad ad-banner">
	<a href="https://www.awin1.com/cread.php?s=390789&v=2547&q=179323&r=127430"><img src="https://www.awin1.com/cshow.php?s=390789&v=2547&q=179323&r=127430" border="0"></a>
</div>
<table class="table table-hover table-sm table-striped">
	<thead>
		<th>Rank</th>
		<th>Name</th>
		<th>Clubs</th>
		<th>Gender</th>
		<th>Points</th>
	</thead>
	<tbody>
		<?php if($ranks['count']){
			foreach($ranks['data'] as $i=>$rank){?>
				<tr>
					<td><?=($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0)+$i+1?></td>
					<td><a href="/athlete/<?=$rank['hex_id']?>"><?=$rank['first_name'].' '.substr($rank['last_name'],0,1)?></td>
					<td><?php if($rank['clubs']){
						$clubs=array();
						foreach($rank['clubs'] as $id=>$club){
							$clubs[]='<a href="/club/'.$club['hex_id'].'">'.$club['name'].'</a>';
						}
						echo implode(',<br>',$clubs);
					} ?></td>
					<td><?=$rank['gender']?></td>
					<td><?=number_format($rank['points'])?></td>
				</tr>
			<?php }
		}else{ ?>
			<tr>
				<td colspan="4">There are no matching athletes. <a href="<?=$strava_url?>">Add yourself</a> to the rankings.</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
<?php pagination($ranks['count']); ?>
<p class="text-xs-center"><a class="btn btn-primary btn-xs" href="<?=$strava_url?>">Add Your Rank</a></p>
<?php require('footer.php');