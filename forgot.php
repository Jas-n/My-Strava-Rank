<?php $app_require=array(
	'php.users',
	'form.forgot'
);
$default_permissions=array(
	2=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0),	# Admin
	3=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('init.php');
if(is_logged_in()){
	header('Location: '.USER_DIRECTORY);
	exit;
}
$forgot=new forgot();
$forgot->process();
if(is_file(ROOT.'images/email_logo.png')){
	$src='images/email_logo.png';
	$img=@getimagesize(ROOT.$src);
	$logos['email']=array(
		'dim'	=>$img[3],
		'height'=>$img[1],
		'uri'	=>$src,
		'width'=>$img[0]
	);
}
if(is_file(ROOT.'images/logo.png')){
	$src='images/logo.png';
	$img=@getimagesize(ROOT.$src);
	$logos['site']=array(
		'dim'	=>$img[3],
		'height'=>$img[1],
		'uri'	=>$src,
		'width'=>$img[0]
	);
}
include('header.php');
get_template(
	'forgot',
	array(
		'app'		=>$app,
		'contents'	=>$page->contents,
		'form'		=>$forgot,
		'logos'		=>$logos,
		'page_title'=>$page->title
	)
);
include('footer.php');