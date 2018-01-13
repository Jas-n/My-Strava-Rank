<?php include('init.php'); ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<?php $app->get_css();
		$core->get_head_js();
		$app->get_icons();?>
		<title><?=$app->page_title()?></title>
  		<base href="<?=SERVER_NAME?>">
	</head>
	<body class="container-fluid">
		<nav class="row">
			<section class="col-md js-load athletes">
				<h2>Athletes</h2>
			</section>
			<section class="col-md js-load clubs">
				<h2>Clubs</h2>
			</section>
			<section class="col-md js-load locations">
				<h2>Locations</h2>
			</section>
			<section class="col-md other">
				<section class="js-load add-rank">
					<h2>Add Rank</h2>
				</section>
				<section class="js-load community">
					<h2>Community</h2>
				</section>
				<section class="js-load login">
					<h2>Login</h2>
				</section>
			</section>
		</nav>
		<main>
			<h1><?=SITE_NAME?></h1>
		</main>
		<?=$app->get_foot_js();?>
	</body>
</html>