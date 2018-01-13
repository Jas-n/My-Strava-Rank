<?php $distance=$strava->distance_like($athlete['total_miles']);?>
<div class="story">
	<h2>Distance</h2>
	<p>Since I first joined Strava on the <strong><?=date('jS \of F Y',strtotime($athlete['strava_join']))?></strong> I've covered over <strong><?=number_format(array_sum(array_column($athlete['activities'],'miles')),1)?> miles</strong>.</p>
	<table class="ranks table">
		<thead>
			<tr>
				<th>Rank</th>
				<th>Name</th>
				<th>Distance</th>
			</tr>
		</thead>
		<tbody>
			<tr class="me">
				<td>[RANK]</td>
				<td><?=$athlete['username']?></td>
				<td>[DISTANCE]</td>
			</tr>
		</tbody>
	</table>
	// Ranks
</div>
<div class="graphic <?=$distance['to']?>">
	<?php print_pre($distance); ?>
	// Current Distace with progression
</div>