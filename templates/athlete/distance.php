<?php $distance=$athlete->distances();?>
<div class="story">
	<h2>Distance</h2>
	<p>Since I first joined Strava on the <strong><?=date('jS \of F Y',strtotime($athlete->strava_join))?></strong> I've travelled over <strong><?=number_format($athlete->total_miles,1)?> miles</strong>. I'm currently travelling the equivelant of <strong><?=$distance['like']['to_text']?></strong> and I'm over <strong><?=number_format($distance['like']['complete'])?>%</strong> of the way there.</p>
</div>
<div class="graphic <?=$distance['to']?>">
	<?php print_pre($distance); ?>
	// Current Distace with progression
</div>