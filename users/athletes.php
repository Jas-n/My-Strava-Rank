<?php $app_require=array(
	'php.Jasn\MSR\athletes'
);
include('../init.php');
$as=$athletes->get_athletes('first_name:asc');
$h1='Athletes';
$h1_small=$as['count'];
$breadcrumb=array(
	$h1
);
include('header.php'); ?>
<div class="grid-md-4">
	<?php foreach($as['data'] as $a){ ?>
		<a class="card card-body card-details" href="./athlete/<?=$a['id']?>">
			<p class="h2"><?=$a['first_name'].' '.substr($a['last_name'],0,1)?></p>
			<?php if($a['username']){ ?>
				<p><strong class="tab-5">Username</strong><?=$a['username']?></p>
			<?php } ?>
			<p><strong class="tab-5">Points</strong><?=number_format($a['points'])?></p>
			<p><strong class="tab-5">Rank</strong><?=$a['rank']?></p>
		</a>
	<?php } ?>
</div>
<?php include('footer.php');