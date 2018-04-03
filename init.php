<?php # If the site is not in a root directory
$namespace=array(
	'company'=>'Jasn',
	'project'=>'MSR'
);
error_reporting(E_ALL & ~E_NOTICE);
require_once(__DIR__.'/core/init.php');
/* Append new query string to _GET */
if(!defined('ROOT')){
	$_get=explode('?',$_SERVER['REQUEST_URI']);
	parse_str($_get[1],$_get);
	$_GET=array_merge($_GET,$_get);
}
# Class Autoloader
spl_autoload_register(function($class){
	if(is_file(ROOT.'libraries/'.$class.'.php')){
		require_once(ROOT.'libraries/'.$class.'.php');
	}elseif(is_file(ROOT.'libraries/'.$class.'/'.$class.'.php')){
		require_once(ROOT.'libraries/'.$class.'/'.$class.'.php');
	}
});
$app		=new Jasn\MSR\app;
$fontawesome=new Formation\Core\fontawesome('light');
$user		=new user();
$page		=new page();
$strava		=new strava(10772,'859a748ef3bced2da0cd73fb7be567d91037a2a3');
if($app_require){
	$app->require=$app_require;
	$require=array_map('strtolower',$app->require);
	$form_included=false;
	foreach($app->require as $require=>$args){
		if(is_numeric($require)){
			$require=$args;
			$args=NULL;
		}
		if(strpos($require,'php.')===0){
			$class=substr($require,4);
			if(strpos($class,'\\')!==FALSE){
				$name=explode('\\',$class);
				$name=$name[sizeof($name)-1];
			}else{
				$name=$class;
			}
			# If class exists		&& we don't want it auto-creating
			if(class_exists($class)	&& !in_array($name,array('form'))){
				$$name=new $class($args);
			}
		}elseif(strpos($require,'form.')===0){
			$name=substr($require,5);
			if(!$form_included){
				include_once(ROOT.'classes/form.php');
				$form_included=1;
				
			}
			include_once(ROOT.'forms/'.$name.'.php');
		}
	}
}
if(!$_GET['file']){
	$_GET['file']='index';
}







/*define('ROOT',($_SERVER['DOCUMENT_ROOT']?$_SERVER['DOCUMENT_ROOT']:__DIR__).'/');
ini_set('error_log',ROOT.'error_log.txt');
ini_set('display_errors','On');
ini_set('memory_limit','256M');
session_start();
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)){
	session_unset();
	session_destroy();
}
if(basename($_SERVER['PHP_SELF'])==basename(__FILE__)){
	header('Location: /');
	exit;
}
define('DATE',date('Y-m-d'));
define('TIME',date('H:i:s'));
define('DATE_TIME',DATE.' '.TIME);
# Set PHP Variables
$_SESSION['last_activity']=time();
# Append new query string to _GET
$_get=explode('?',$_SERVER['REQUEST_URI']);
parse_str($_get[1],$_get);
$_GET=array_merge($_GET,$_get);
# Class Autoloader
include_once(ROOT.'functions.php');
$dir=get_dir();
if($dir && $dir!='ajax' && $dir!='api' && $dir!='CRONS' && !is_logged_in()){
	header('Location: /login?url='.urlencode($_SERVER['REQUEST_URI']));
	exit;
}
$db=new database();
# Define Settings
foreach($db->query("SELECT `name`,`value` FROM `settings`") as $setting){
	define(strtoupper($setting['name']),nl2br($setting['value']));
}
# If not a cron job, then Redirect to the current SERVER_NAME if the http(s) is incorrect
if(isset($_SERVER['REQUEST_METHOD']) && substr(SERVER_NAME,0,strpos(SERVER_NAME,':'))!=$_SERVER['REQUEST_SCHEME']){
	header('Location: '.SERVER_NAME.substr($_SERVER['REQUEST_URI'],1));
	exit;
}
$limit=max(0,$_GET['page']?(ceil($_GET['page']-1)*ITEMS_PER_PAGE):0);
define('SQL_LIMIT',' LIMIT '.$limit.','.ITEMS_PER_PAGE.' ');
unset($limit);
$core		=new Formation\Core\core;
$app		=new Jasn\MSR\app;
$bootstrap	=new bootstrap;
$encryption	=new encryption();
$user		=new user();
$page=new page_permissions();
$app->require=$app_require;
$require=array_map('strtolower',$app->require);
$form_included=false;
$strava=new strava(10772,'859a748ef3bced2da0cd73fb7be567d91037a2a3');
foreach($app->require as $app_require){
	if(strpos($app_require,'lib.')===0){
		$name=substr($app_require,4);
		if(is_file(ROOT.'libraries/'.$name.'.php')){
			include_once(ROOT.'libraries/'.$name.'.php');
		}elseif(is_file(ROOT.'libraries/'.$name.'/'.$name.'.php')){
			include_once(ROOT.'libraries/'.$name.'/'.$name.'.php');
		}
	}elseif(strpos($app_require,'php.')===0){
		$name=substr($app_require,4);
		# If class exists		&& we don't want it auto-creating
		if(class_exists($name) && !in_array($name,array('form'))){
			$$name=new $name;
		}
	}elseif(strpos($app_require,'form.')===0){
		$name=substr($app_require,5);
		if(!$form_included){
			include_once(ROOT.'classes/form.php');
			$form_included=1;
		}
		include_once(ROOT.'forms/'.$name.'.php');
	}
}*/