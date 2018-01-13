<div class="story">
	<h2>Biography</h2>
	<p>Hi,<br>my name's <strong><?=$athlete['first_name']?></strong>, you'll find me as <strong><?=$athlete['username']?></strong> on My Strava Rank. I first joined Strava on the <strong><?=date('jS \of F Y',strtotime($athlete['strava_join']))?></strong> and since then have recorded <strong><?=number_format(array_sum(array_column($athlete['activities'],'count')))?> activities</strong> that have taken me over <strong><?=number_format(array_sum(array_column($athlete['activities'],'moving_hours')))?> hours</strong> to achieve distances of over <strong><?=number_format(array_sum(array_column($athlete['activities'],'miles')),1)?> miles</strong>, heights of over <strong><?=number_format(array_sum(array_column($athlete['activities'],'elevation_gain')),1)?> miles</strong>.</p>
	<p>I joined My Strava Rank <strong>[TIME LATER]</strong> on the <strong><?=date('jS \of F Y',strtotime($athlete['added']))?></strong> and have since amassed <strong><?=number_format($athlete['points'])?> points</strong> which has gotten me to <strong>rank <?=$athlete['rank']?></strong>.</p>
	<p>My favourite Strava sporting activity is <strong>[HIGHEST ACTIVITY COUNT NAME]</strong>, as I've recorded them <strong>[MAX ACTIVITY COUNT]</strong>.</p>
</div>
<div class="graphic">
	// Slider:<br>
		- Face / Avatar / Profile<br>
		- Location
	<?php print_pre($athlete); ?>
</div>