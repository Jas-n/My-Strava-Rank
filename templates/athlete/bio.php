<div class="story">
	<h2>Biography</h2>
	<p>Hi,<br>my name's <strong><?=$athlete->first_name?></strong>, you'll find me as <strong><?=$athlete->username?></strong> on My Strava Rank. I first joined Strava on the <strong><?=date('jS \of F Y',strtotime($athlete->strava_join))?></strong> and since then have recorded <strong><?=number_format(array_sum(array_column($athlete->activities,'count')))?> activities</strong> that have taken me over <strong><?=number_format(array_sum(array_column($athlete->activities,'moving_hours')))?> hours</strong> to achieve distances of over <strong><?=number_format(array_sum(array_column($athlete->activities,'miles')),1)?> miles</strong>, heights of over <strong><?=number_format(array_sum(array_column($athlete->activities,'elevation_gain')),1)?> miles</strong>.</p>
	<p>I joined My Strava Rank <strong><?=date_difference($athlete->strava_join,$athlete->added)?></strong> later on the <strong><?=date('jS \of F Y',strtotime($athlete->added))?></strong> and have since amassed <strong><?=number_format($athlete->points)?> points</strong> which has gotten me to <strong>rank <?=$athlete->rank?></strong>.</p>
	<p>My favourite Strava sporting activity is <strong><?=$athlete->favourite_activity['activity']?></strong>, as I've recorded them <strong><?=number_format($athlete->favourite_activity['count'])?></strong> times.</p>
</div>
<div class="graphic athlete">
	<!--div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
		<ol class="carousel-indicators">
			<li data-target="#carouselExampleSlidesOnly" data-slide-to="0" class="active"></li>
			<li data-target="#carouselExampleSlidesOnly" data-slide-to="1"></li>
			<li data-target="#carouselExampleSlidesOnly" data-slide-to="2"></li>
			<li data-target="#carouselExampleSlidesOnly" data-slide-to="3"></li>
		</ol>
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="http://via.placeholder.com/350x150" alt="First slide">
				<div class="carousel-caption d-none d-md-block">
					<h5>Face / Avatar / Profile</h5>
				</div>
			</div>
			<div class="carousel-item">
				<img class="d-block w-100" src="http://via.placeholder.com/350x150" alt="First slide">
				<div class="carousel-caption d-none d-md-block">
					<h5>Photos</h5>
				</div>
			</div>
			<div class="carousel-item">
				<img class="d-block w-100" src="http://via.placeholder.com/350x150" alt="First slide">
				<div class="carousel-caption d-none d-md-block">
					<h5>Equipment</h5>
				</div>
			</div>
			<div class="carousel-item">
				<img class="d-block w-100" src="http://via.placeholder.com/350x150" alt="First slide">
				<div class="carousel-caption d-none d-md-block">
					<h5>Location</h5>
				</div>
			</div>
		</div>
	</div-->
	<?php #print_pre($athlete); ?>
</div>