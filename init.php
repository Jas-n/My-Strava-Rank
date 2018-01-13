<?php # Version 1.5
basename($_SERVER['PHP_SELF'] && $_SERVER['PHP_SELF'])=='init.php'?die('Access Denied'):$_;
# If the site is not in a root directory
$document_root=$_SERVER['DOCUMENT_ROOT']?$_SERVER['DOCUMENT_ROOT']:__DIR__;
define("ROOT",$document_root.'/');
# Set PHP Variables
ini_set('display_errors','On');
ini_set('error_log',ROOT.'error_log.txt');
ini_set('memory_limit','256M');
session_start();
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)){
	session_unset();
	session_destroy();
}
$_SESSION['last_activity']=time();
/* Append new query string to _GET */
if(!defined('ROOT')){
	$_get=explode('?',$_SERVER['REQUEST_URI']);
	parse_str($_get[1],$_get);
	$_GET=array_merge($_GET,$_get);
}
if(!in_array(basename(__FILE__,'.php'),array('notifications'))){
	$_SESSION['history'][]=$_SERVER['SCRIPT_FILENAME'].'?'.http_build_query($_GET);
	$_SESSION['history']=array_slice($_SESSION['history'],-10,10,true);
}
/* Global */
# Class Autoloader
spl_autoload_register(function($class){
	$prefixs=array(
		# Core Prefix
		'Formation\\Core\\'=>'/core',
		# project-specific namespace prefix
		'Jasn\\MSR\\'=>''
	);
	foreach($prefixs as $prefix=>$dir){
		$base_dir=__DIR__.$dir.'/classes/';
		# does the class use the namespace prefix?
		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) !== 0) {
		   # no, move to the next registered autoloader
		   continue;
		}
		# get the relative class name
		$relative_class = substr($class, $len);
		# replace the namespace prefix with the base directory, replace namespace
		# separators with directory separators in the relative class name, append
		# with .php
		$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
		# if the file exists, require it
		if (file_exists($file)) {
			require $file;
		}
	}
});
spl_autoload_register(function($class){
	if(is_file(ROOT.'core/classes/'.$class.'.php')){
		require_once(ROOT.'core/classes/'.$class.'.php');
	}elseif(is_file(ROOT.'classes/'.$class.'.php')){
		require_once(ROOT.'classes/'.$class.'.php');
	}
});
include_once(ROOT.'functions.php');
$dir=get_dir();
if($dir && $dir!='ajax' && $dir!='api' && $dir!='CRONS' && $dir!='templates' && !is_logged_in()){
	header('Location: /login.php?url='.urlencode($_SERVER['REQUEST_URI']));
	exit;
}
define('DATE',date('Y-m-d'));
define('TIME',date('H:i:s'));
define('DATE_TIME',DATE.' '.TIME);
$db		=new database();
$core	=new Formation\Core\core;
$app	=new Jasn\MSR\app;
$bootstrap	=new bootstrap;
$fontawesome=new Formation\Core\fontawesome('light');
# Define Settings
foreach($db->query("SELECT `name`,`value` FROM `settings`") as $setting){
	define(strtoupper($setting['name']),nl2br($setting['value']));
}
$user=new user();
$page=new page();
$strava=new strava(10772,'859a748ef3bced2da0cd73fb7be567d91037a2a3');
if($app_require){
	$app->require=$app_require;
	$require=array_map('strtolower',$app->require);
	$form_included=false;
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
			if(class_exists($name)	&& !in_array($name,array('form'))){
				$$name=new $name;
			}
		}elseif(strpos($app_require,'form.')===0){
			$name=substr($app_require,5);
			if(!$form_included){
				include_once(ROOT.'classes/form.php');
				$form_included=1;
			}
			include_once(ROOT.'forms/'.$name.'.php');
		}elseif(strpos($app_require,'db.')===0){
			foreach($db->query("SELECT `name`,`value` FROM `settings` WHERE `name` LIKE ?",substr($app_require,3).'%') as $setting){
				define(strtoupper($setting['name']),nl2br($setting['value']));
			}
		}
	}
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