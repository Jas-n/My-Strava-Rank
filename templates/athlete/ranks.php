<div class="story">
	<h2>Ranks</h2>
	// Ranks
	<h3>Distance</h3>
	<table class="ranks table table-sm">
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
				<td><?=$athlete->username?></td>
				<td>[DISTANCE]</td>
			</tr>
		</tbody>
	</table>
	<h3>Altitude</h3>
	<table class="ranks table table-sm">
		<thead>
			<tr>
				<th>Rank</th>
				<th>Name</th>
				<th>Elevation</th>
			</tr>
		</thead>
		<tbody>
			<tr class="me">
				<td>[RANK]</td>
				<td><?=$athlete->username?></td>
				<td>[ELEVATION]</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="graphic">
	// Current Ranks with progression
	<?php print_pre($athlete->ranks); ?>
</div>