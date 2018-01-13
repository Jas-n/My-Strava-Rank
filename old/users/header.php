<?php $search=new search();
$search->process();
$page->get_permissions(array(
	'users/add_user',
	'users/contents',
	'users/index',
	'users/logs',
	'users/permissions',
	'users/profile',
	'users/search',
	'users/settings',
	'users/statistics',
	'users/tools',
	'users/users'
));?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<?php $app->get_css();
		$app->get_head_js();
		$app->get_icons();?>
		<title><?=$app->page_title()?></title>
		<base href="<?=SERVER_NAME?>users/">
    </head>
    <body class="<?=DEBUG?"dev":""?>" id="<?=strtolower(str_replace(' ','-','users-'.$page->slug))?>">
		<nav id="vertical_nav">
			<ul>
				<li><a href="../"><span class="dashboard_icon fa fa-home fa-fw"></span> Home</a></li>
				<li<?=$page->slug==''?' class="active"':''?>><a href="./">
				<?php if(is_file('../images/icons/57.png')){ ?>
					<img class="dashboard_icon" src="../images/icons/57.png">
				<?php }else{ ?>
					<span class="dashboard_icon fa fa-dashboard fa-fw"></span>
				<?php } ?>
				Dashboard</a></li>
				<?php if($page->has_permission('users/search')){?>
					<li class="<?=$page->slug=='search'?'active ':''?>search_form"><a><span class="fa fa-search fa-fw"></span> <?=$search->get_form()?></a></li>
				<?php } ?>
				<li class="divider"></li>
				<?php if($page->has_permission('users/contents')){ ?>
					<li class="<?=in_array($page->slug,array('contents','content'))?'active ':''?> has_children"><a><span class="fa fa-caret-right pull-xs-right"></span><span class="fa fa-file-text-o fa-fw"></span> Content</a>
						<ul>
							<?php if($page->has_permission('users/contents')){ ?>
								 <li<?=$page->slug=='contents'?' class="active"':''?>><a href="contents">Pages</a></li>
							<?php } ?>
						</ul>
					</li>
				<?php }
				if($page->has_permission('users/users') || $page->has_permission('users/add_user') || $page->has_permission('users/permissions')){ ?>
					<li class="<?=in_array($page->slug,array('users','add_user','permissions'))?'active ':''?> has_children"><a><span class="fa fa-caret-right pull-xs-right"></span><span class="fa fa-group fa-fw"></span> Users</a>
						<ul>
							<?php if($page->has_permission('users/users')){ ?>
								<li<?=$page->slug=='users'?' class="active"':''?>><a href="users">Users</a></li>
							<?php }
							if($page->has_permission('users/add_user')){?>
								<li<?=$page->slug=='add_user'?' class="active"':''?>><a href="add_user">Add</a></li>
							<?php }
							if($page->has_permission('users/permissions')){?>
								<li<?=$page->slug=='permissions'?' class="active"':''?>><a href="permissions">Permissions</a></li>
							<?php }?>
						</ul>
					</li>
				<?php }
				if($page->has_permission('users/logs') || $page->has_permission('users/statistics') || $page->has_permission('users/settings') || $page->has_permission('users/update')){ ?>
					<li class="<?=in_array($page->slug,array('logs','statistics','settings','update'))?'active ':''?> has_children"><a><span class="fa fa-caret-right pull-xs-right"></span><span class="fa fa-wrench fa-fw"></span> Management</a>
						<ul>
							<?php if($page->has_permission('users/logs')){ ?>
								<li<?=$page->slug=='logs'?' class="active"':''?>><a href="logs">Logs</a></li>
							<?php }
							if($page->has_permission('users/statistics')){ ?>
								<li<?=$page->slug=='statistics'?' class="active"':''?>><a href="statistics">Statistics</a></li>
							<?php }
							if($page->has_permission('users/settings') || $page->has_permission('users/update')){?>
								<li class="divider"></li>
							<?php }
							if($page->has_permission('users/settings')){ ?>
								<li<?=$page->slug=='settings'?' class="active"':''?>><a href="settings">Settings</a></li>
							<?php }
							if($page->has_permission('users/tools')){ ?>
								<li<?=$page->slug=='tools'?' class="active"':''?>><a href="tools">Tools</a></li>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>
				<li class="divider"></li>
				<li class="<?=in_array($page->slug,array('profile'))?'active ':''?> has_children"><a><span class="fa fa-caret-right pull-xs-right"></span><span class="fa fa-user fa-fw"></span> <?=$user->first_name?></a>
					<ul>
						<li<?=$page->slug=='profile'?' class="active"':''?>><a href="profile">Profile</a></li>
						<li><a href="../logout">Logout</a></li>
					</ul>
				</li>
			</ul>
			<footer class="row text-xs-center">
				
			</footer>
		</nav>
		<div id="body">
			<div class="container-fluid">
				<?php if($page->contents['help'] && $page->contents['help']['content']){ ?>
					<a class="help_modal" data-toggle="modal" data-target="#help_modal">Help</a>
				<?php } ?>
				<div class="row">
					<div class="col-xs-12 col-xl-offset-1 col-xl-10">