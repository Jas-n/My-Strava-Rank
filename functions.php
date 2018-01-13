<?php # Auto-load Classes
# Auto-load Classes
spl_autoload_register(function($class){
	if(is_file(ROOT.'classes/'.$class.'.php')){
		require_once(ROOT.'classes/'.$class.'.php');
	}
});
set_error_handler('log_errors',~E_NOTICE);
register_shutdown_function(function(){
	$error = error_get_last();
	if($error["type"]==E_ERROR){
		log_errors($error["type"],$error["message"],$error["file"],$error["line"]);
	}
});
# Limit $text to $length (Default 50)
function crop($text,$length=50){
	if(strlen($text)>$length){
		$text=strip_tags(substr($text,0,$length-1)).'&hellip;';
	}
	return $text;
}
function debug(){
	$traces=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	if(is_array($traces)){
		$traces=array_slice($traces,1);
		$key=sizeof($traces);
		foreach($traces as &$t){
			$trace[$key]=array(
				'file'		=>$t['file'],
				'line'		=>$t['line'],
				'function'	=>$t['function'].'()'
			);
			if($t['args']){
				$trace[$key]['args']=$t['args'];
			}
			$key--;
		}
		return $traces;
	}
	return $traces;
}
# Checks if a user is logged in
function is_logged_in(){
	return !!$_SESSION['user_id'];
}
# Return formatted __LINE__
# Updated 26/01/2017 13:08
function line($return=false){
	$stack=debug_backtrace()[0];
	$line=$stack['file'].': '.$stack['line'].'<br>';
	if($return){
		return $line;
	}
	echo $line;
}
function log_errors($severity,$message,$file,$line,array $context=NULL){
	global $db;
	if(isset($db)){
		$db->error('PHP',$message,$file,$line,$severity,debug());
	}else{
		restore_error_handler();
	}
	return true;
}
function get_dir($level=0){
	$dir=explode('/',str_replace(ROOT,'',getcwd().'/'));
	array_pop($dir);
	return $dir[sizeof($dir)-1-$level];
}
# Print Preformated
# Updated 08-09-2016 17:22
function print_pre($expression,$return=false){
	$history=debug_backtrace();
	$history=$history[0];
	$out='<div class="print_pre">
		Debug<br><small><em>'.$history['file'].': '.$history['line'].'</em></small>
		<pre>'.htmlspecialchars(print_r($expression,true)).'</pre>
	</div>';
	if($return){
		return $out;
	}else{
		echo $out;
	}
}
# Returns a date reformated fron SQL
# Updated 29/03/2017 21:29
function sql_date($date_from_sql){
	if(!is_numeric($date_from_sql)){
		$time=strtotime($date_from_sql);
	}else{
		$time=$date_from_sql;
	}
	if($time<=0){
		return false;
	}
	return date(DATE_FORMAT,$time);
}
# Returns a date and time reformated from SQL
# Updated 29/03/2017 21:25
function sql_datetime($datetime_from_sql){
	if(!is_numeric($datetime_from_sql)){
		$time=strtotime($datetime_from_sql);
	}else{
		$time=$datetime_from_sql;
	}
	if($time<=0){
		return false;
	}
	return sql_date($datetime_from_sql).' at '.date(TIME_FORMAT,$time);
}