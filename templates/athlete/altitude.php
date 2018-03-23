<?php $elevation=$athlete->elevation();?>
<div class="story">
	<h2>Altitude</h2>
	<p>Since I first joined Strava on the <strong><?=date('jS \of F Y',strtotime($athlete->strava_join))?></strong> I've ascended over <strong><?=number_format($athlete->total_elevation,1)?> miles</strong>. I'm currently ascending the equivelant of <strong><?=$elevation['like']['to_text']?></strong> and I'm over <strong><?=number_format($elevation['like']['complete'])?>%</strong> of the way there.</p>
</div>
<div class="graphic <?=$elevation['like']['to']?>">
</div>