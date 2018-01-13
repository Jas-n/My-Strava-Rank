<?php $app_require=array(
	'form.login'
);
$default_permissions=array(
	2=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0),	# Admin
	3=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>0,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require('init.php');
if(is_logged_in()){
	header('Location: /users');
	exit;
}
$login=new login();
$login->process();
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
if($_GET['reset']){
	$app->set_message('success','Your new password has been emailed to you.');
}elseif($_GET['registered']){
	$app->set_message('success','You have been successfully registered. Your login details have been sent to the supplied email address.');
}
require('header.php');?>
<div class="hidden-md-up ad ad-banner">
	<a href="https://www.awin1.com/cread.php?s=390789&v=2547&q=179323&r=127430"><img src="https://www.awin1.com/cshow.php?s=390789&v=2547&q=179323&r=127430" border="0"></a>
</div>
<div class="card">
	<div class="card-block">
		<h1 class="h2">Login</h1>
		<?php $app->get_messages();
		$login->get_form();?>
	</div>
</div>
<?php require('footer.php');