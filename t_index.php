<?php require_once('init.php');
require('t_header.php');?>
<h1><?=SITE_NAME?></h1>
<div class="sections">
	<section class="js-load athletes">
		<h2>Athletes</h2>
		<div class="container-fluid"></div>
	</section>
	<?php if(is_logged_in()){ ?>
		<section class="js-load clubs">
			<h2>Clubs</h2>
			<div class="container-fluid"></div>
		</section>
	<?php } ?>
	<!--section class="js-load locations">
		<h2>Locations</h2>
		<div class="container-fluid"></div>
	</section>
	<section class="js-load community">
		<h2>Community</h2>
		<div class="container-fluid"></div>
	</section>
	<section class="js-load login">
		<h2>Login</h2>
		<div class="container-fluid"></div>
	</section-->
	<section class="add-rank" data-link="<?=$strava->authenticationUrl(SERVER_NAME.'add','auto');?>">
		<h2>Add Rank</h2>
	</section>
</div>
<?php require('t_footer.php');