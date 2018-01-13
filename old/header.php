<?php $strava_url=$strava->authenticationUrl(SERVER_NAME.'add','auto');?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link type="text/plain" rel="author" href="/humans.txt">
		<?php $app->get_css();
		$app->get_head_js();
		$app->get_icons();?>
		<title><?=$app->page_title()?></title>
    </head>
    <body>
		<div class="container-fluid">
			<header class="row">
				<nav class="navbar navbar-fixed-top">
					<div class="navbar-header">
						<button class="navbar-toggler hidden-md-up" type="button" data-toggle="collapse" data-target="#mainnav">
							&#9776;
						</button>
					</div>
					<div class="collapse navbar-toggleable-sm" id="mainnav">
						<a class="navbar-brand" href="/">My Strava Rank</a>
						<ul class="nav navbar-nav">
							<li class="nav-item"><a class="nav-link<?=in_array($page->slug,array('athlete','athletes'))?' active':''?>" href="/athletes">Athletes</a></li>
							<li class="nav-item"><a class="nav-link<?=in_array($page->slug,array('club','clubs'))?' active':''?>" href="/clubs">Clubs</a></li>
							<li class="nav-item"><a class="nav-link<?=in_array($page->slug,array('location','locations'))?' active':''?>" href="/locations">Locations</a></li>
							<li class="nav-item"><a class="nav-link" href="<?=$strava_url?>">Add My Rank</a></li>
							<li class="nav-item pull-sm-right"><a class="nav-link<?=in_array($page->slug,array('login'))?' active':''?>" href="/login"><?=is_logged_in()?"Account":"Login"?></a></li>
						</ul>
					</div>
				</nav>
			</header>
			<div class="content">
				<div class="row">
					<div class="ad ad-skyscraper hidden-sm-down col-md-2">
						<a href="https://www.awin1.com/cread.php?s=591899&v=2547&q=178777&r=127430"><img src="https://www.awin1.com/cshow.php?s=591899&v=2547&q=178777&r=127430" border="0"></a>
					</div>
					<div class="col-md-8">