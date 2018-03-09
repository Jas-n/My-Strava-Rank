<?php $app_require=array(
	'php.Jasn\MSR\athletes'
);
require('init.php');
$h1='Athletes';
require('t_header.php');
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
			<div class="item <?=$class?$class.' ':''?>js-load athlete" data-id="<?=$rank['hex_id']?>">
				<h4><?=$position?> - <?=$rank['username']?$rank['username']:$rank['first_name'].' '.substr($rank['last_name'],0,1)?> - <?=number_format($rank['points'])?></h4>
				<?=$bootstrap->progress($rank['points'],$rank['next_rank_points'],$rank['rank'])?>
			</div>
		<?php }
	}
	for($i=3;$i<10;$i++){?>
		<div class="item js-load athlete" data-id="<?=$rank['hex_id']?>">
			<h4><?=$position?> - <?=$rank['username']?$rank['username']:$rank['first_name'].' '.substr($rank['last_name'],0,1)?> - <?=number_format($rank['points'])?></h4>
		</div>
	<?php } ?>
</div>
<?php require('t_footer.php');