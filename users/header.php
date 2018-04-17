<?php $nav=array(
	'Dashboard'=>array(
		'href'=>'./',
		'icon'=>'tachometer'
	)
); ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<?php $app->get_css();
		$core->get_header();?>
		<title><?=$core->page['title']?></title>
		<base href="<?=SERVER_NAME?>users/">
    </head>
	<body id="<?=strtolower(str_replace(' ','-','users-'.$page->slug))?>">
		<nav class="vertical">
			<ul class="icon_nav">
				<?php foreach($nav as $name=>$parent){
					if($parent['children']){
						$parent['count']+=array_sum(array_column($li['children'],'count'));
					}
					if(!$parent['icon']){
						$parent['icon']=$fontawesome->get_prefix().' fa-question';
					} ?>
					<li<?=$parent['children']?' class="has_children"':''?>>
						<a<?=!$parent['children'] && $parent['href']?' href="'.$parent['href'].'"':''?> title="<?=$name?>">
							<?php echo $fontawesome->icon($parent['icon']);
							if($li['count']){?>
								<span class="badge badge-info"><?=number_format($li['count'])?></span>
							<?php }?>
							<span class="d-none d-sm-block"><?=$name?></span>
						</a>
					</li>
				<?php } ?>
			</ul>
		</nav>
		<header>
			<div class="search">
				<input type="search" class="form-control header_search js-global-search" aria-describedby="email_label" placeholder="Search&hellip;">
			</div>
			<div class="actions">
			</div>
			<div class="account">
				<a class="acc-profile" data-placement="left" data-toggle="tooltip" href="profile" title="Profile"><img class="rounded" src="<?=0#$user->get_avatar($usr['id'],50)?>" height="50"></a>
				<a class="acc-logout" href="../logout">Logout</a>
			</div>
		</header>
		<main class="default">