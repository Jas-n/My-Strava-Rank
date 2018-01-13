<?php $app_require=array(
	'php.strava'
);
$default_permissions=array(
	2=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Admin
	3=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1),	# Manager
	4=>array('view'=>1,'add'=>1,'edit'=>1,'delete'=>1), # User
	5=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('../init.php');
require('header.php');?>
<div class="page-header">
	<h1>Dashboard</h1>
	<ol class="breadcrumb">
		<li class="active">Dashboard</li>
	</ol>
</div>
<div class="card">
	<div class="card-header">To Do</div>
	<div class="card-block">
		<ol class="cols-md-6 cols-lg-4">
			<li>Adverts</li>
			<li>Athlete
				<ul>
					<li>Show Ranking data, including:
						<ul>
							<li>Club</li>
							<li>Global</li>
							<li>Gender,</li>
							<li>Country</li>
							</ul>
						</li>
				</ul>
			</li>
			<li>Cron
				<ul>
					<li>Populate any towns with a 0 lat and 0 lng</li>
				</ul>
			</li>
			<li>Pages
				<ul>
					<li>About</li>
					<li>Data Protection</li>
					<li>FAQ's</li>
					<li>Privacy</li>
					<li>Remove from emails</li>
				</ul>
			</li>
			<li>Search for athlete/club</li>
		</ol>
	</div>
</div>
<?php require('footer.php');