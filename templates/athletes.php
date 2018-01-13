<?php $app_require=array(
	'php.athletes'
);
require('../init.php');
$h1='Athletes';
require('header.php');
$ranks=$athletes->get_athletes();?>
<div class="list">
	<?php if($ranks['count']){
		foreach($ranks['data'] as $i=>$rank){
			$position=($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0)+$i+1;
			switch($position){
				case 1:
					$class='first';
					break;
				case 2:
					$class='second';
					break;
				case 3:
					$class='third';
					break;
			}?>
			<div class="item js-load athlete" data-id="<?=$rank['hex_id']?>">
				<h4><?=$position?> - <?=$rank['username']?$rank['username']:$rank['first_name'].' '.substr($rank['last_name'],0,1)?> - <?=number_format($rank['points'])?></h4>
				<div class="row">
					<div class="col-sm-6">
						<h5>Clubs</h5>
						<?php if($rank['clubs']){ ?>
							<ol>
								<?php foreach($rank['clubs'] as $id=>$club){ ?>
									<li><a href="/club/<?=$club['hex_id']?>"><?=$club['name']?> (Ranked <?=number_format($club['rank'])?>)</a></li>
								<?php } ?>
							</ol>
						<?php } ?>
					</div>
					<div class="col-sm-6">
						<p><strong class="tab-7">Gender</strong><?=$rank['gender']?></p>
						<p><strong class="tab-7">Last Activity</strong><?=sql_datetime($rank['last_activity'])?></p>
						<p><strong class="tab-7">Joined Strava</strong><?=sql_datetime($rank['strava_join'])?></p>
						<p><strong class="tab-7">Joined MSR</strong><?=sql_datetime($rank['added'])?></p>
						<p><strong class="tab-7">Updated</strong><?=sql_datetime($rank['updated'])?></p>
					</div>
				</div>
			</div>
		<?php }
	} ?>
</div>
<?php require('footer.php');