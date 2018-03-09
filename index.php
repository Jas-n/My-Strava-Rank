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
		<nav>
			<a href="#"><?=SITE_NAME?></a>
		</nav>
		<main class="home">
			<?php include('t_index.php'); ?>
		</main>
		<?=$app->get_foot_js();?>
	</body>
</html>