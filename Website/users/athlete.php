<?php $app_require=array(
	'php.Jasn\MSR\athlete'
);
include('../init.php');
$athlete=$athlete->get_athlete($_GET['id']);
$h1=$athlete['first_name'].' '.substr($athlete['last_name'],0,1);
$h1_small=$as['username'];
$breadcrumb=array(
	$h1
);
include('header.php');
$meta['Full Name']=$athlete['first_name'].' '.$athlete['last_name'];
if($athlete['username']){
	$meta['Username']=$athlete['username'];
}
$meta=array_merge(
	$meta,
	array(
		'Added'=>sql_datetime($athlete['added']),
		'Updated'=>sql_datetime($athlete['updated']),
		'Joined Strava'=>sql_datetime($athlete['strava_join'])
	)
); ?>
<div class="card card-body card-details cols-md-3">
	<?php foreach($meta as $key=>$value){ ?>
		<p><strong class="tab-7"><?=$key?></strong><?=$value?></p>
	<?php } ?>
</div>
<div class="card card-body">
	<?php print_pre($athlete); ?>
</div>
<?php include('footer.php');