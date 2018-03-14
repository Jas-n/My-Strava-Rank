<?php include('init.php');
if($_GET['page']=='add'){
	include(ROOT.'includes/add.php');
}?>
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
	<body>
		<nav class="navbar navbar-expand-lg navbar-light">
			<a class="navbar-brand" href="#"><?=SITE_NAME?></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
  			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item"><a class="nav-link active js-load index">Home<span class="sr-only"> (current)</span></a></li>
					<li class="nav-item"><a class="nav-link js-load athletes">Athletes</a></li>
					<li class="nav-item"><a class="nav-link js-load clubs">Clubs</a></li>
					<li class="nav-item"><a class="nav-link js-load locations">Locations</a></li>
					<li class="nav-item"><a class="nav-link js-load community">Community</a></li>
					<li class="nav-item"><a class="nav-link js-load login">Login</a></li>
					<li class="nav-item"><a class="nav-link" data-link="<?=$strava->authenticationUrl(SERVER_NAME.'add','auto');?>">Add Rank</a></li>
				</ul>
			</div>
		</nav>
		<main class="home">
			<?php include('t_index.php'); ?>
		</main>
		<?=$app->get_foot_js();?>
	</body>
</html>