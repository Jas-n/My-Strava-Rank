<?php $default_permissions=array(
	2=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Admin
	3=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0),	# Manager
	4=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0), # User
	5=>array('view'=>1,'add'=>0,'edit'=>0,'delete'=>0)	# Visitor
);
require_once('init.php');
$status=$_SERVER['REDIRECT_STATUS'];
$codes=array(
	403	=>array('403 Forbidden','The server has refused to fulfill your request.'),
	404	=>array('404 Not Found','The document/file requested was not found on this server.'),
	405	=>array('405 Method Not Allowed','The method specified in the Request-Line is not allowed for the specified resource.'),
	408	=>array('408 Request Timeout','Your browser failed to send a request in the time allowed by the server.'),
	500	=>array('500 Internal Server Error','The request was unsuccessful due to an unexpected condition encountered by the server.'),
	502	=>array('502 Bad Gateway','The server received an invalid response from the upstream server while trying to fulfill the request.'),
	504	=>array('504 Gateway Timeout','The upstream server failed to send a request in the time allowed by the server.'),
);
$title=$codes[$status][0];
$message=$codes[$status][1];
if($_GET['slug']){
	$title=$codes[404][0];
	$message=$codes[404][1];
}elseif($title==false || strlen($status)!=3){
	$message = 'Please supply a valid status code.';
}
require('header.php');?>
<h1><?=SITE_NAME?> &gt; <?=$title?></h1>
<div class="hidden-md-up ad ad-banner">
	<a href="https://www.awin1.com/cread.php?s=390789&v=2547&q=179323&r=127430"><img src="https://www.awin1.com/cshow.php?s=390789&v=2547&q=179323&r=127430" border="0"></a>
</div>
<div class='col-md-offset-2 col-md-8 alert alert-danger' role='alert'>
<p><?=$message?></p>
<?php require('footer.php');