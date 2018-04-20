<div class="story">
	<h2>Clubs</h2>
	<p>Since I first joined Strava on the <strong><?=date('jS \of F Y',strtotime($athlete->strava_join))?></strong> I've joined <strong><?=inflect::pluralize_if(sizeof($athlete->clubs),'club')?></strong>.</p>
</div>
<div class="graphic p-3">
	<?php if($athlete->clubs){
		foreach($athlete->clubs as $club){ ?>
			<h3><?=$club['name']?></h3>
		<?php }
	} ?>
</div>