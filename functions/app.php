<?php # Auto-load Classes
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
function distance_like($miles){
	if($miles<26.21875){
		$max=false;
		$to='marathon';
		$to_text='the length of a marathon';
		$distance=26.21875;
	}elseif($miles<271){
		$max=false;
		$to='uk_width';
		$to_text='the width of the UK';
		$distance=271;
	}elseif($miles<622){
		$max=false;
		$to='uk_height';
		$to_text='the height of the UK';
		$distance=622;
	}elseif($miles<1339){
		$max=false;
		$to='europe';
		$to_text='across Europe';
		$distance=1339;
	}elseif($miles<2511){
		$max=false;
		$to='australia';
		$to_text='across Australia';
		$distance=2680;
	}elseif($miles<2680){
		$max=false;
		$to='america';
		$to_text='across North America';
		$distance=2680;
	}elseif($miles<4160){
		$max=false;
		$to='nile';
		$to_text='along the river nile';
		$distance=4160;
	}elseif($miles<4355){
		$max=false;
		$to='africa';
		$to_text='across Africa';
		$distance=4355;
	}elseif($miles<5515){
		$max=false;
		$to='asia';
		$to_text='across Asia';
		$distance=5515;
	}elseif($miles<6786){
		$max=false;
		$to='moon';
		$to_text='around the moon';
		$distance=6786;
	}elseif($miles<9522){
		$max=false;
		$to='mercury';
		$to_text='around Mercury';
		$distance=9522;
	}else{
		$max=($miles>5515?true:false);
		$to='world';
		$to_text='around the world';
		$distance=7917.5;
	}
	return array(
		'max'		=>$max,
		'to'		=>$to,
		'to_text'	=>$to_text,
		'times'		=>floor($miles/$distance),
		'complete'	=>round(($miles/$distance-floor($miles/$distance))*100,2)
	);
}
function elevation_like($miles){
	$meters=$miles/0.0006213712;
	if($meters<1281){
		$max=false;
		$mountain='Mt. Vesuvious';
		$mountain_height=1281;
		$to='vesuvious';
	}elseif($meters<3376){
		$max=false;
		$mountain='Mt. Fuji';
		$mountain_height=3376;
		$to='fuji';
	}elseif($meters<4808){
		$max=false;
		$mountain='Mont Blanc';
		$mountain_height=4810;
		$to='mont_blanc';
	}elseif($meters<5895){
		$max=false;
		$mountain='Mt. Kilimanjaro';
		$mountain_height=5895;
		$to='kilimanjaro';
	}elseif($meters<8611){
		$max=false;
		$mountain='K2';
		$mountain_height=8611;
		$to='k2';
	}else{
		$max=($meters>8848?true:false);
		$mountain='Mt. Everest';
		$mountain_height=8848;
		$to='everest';
	}
	return array(
		'max'		=>$max,
		'to'		=>$to,
		'to_text'	=>$mountain,
		'times'		=>floor($meters/$mountain_height),
		'complete'	=>round(($meters/$mountain_height-floor($meters/$mountain_height))*100,2)
	);
}
# Checks if a user is logged in
function is_logged_in(){
	return !!($_SESSION['user_id']);
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
function date_difference($from,$to){
	$from	=date_create($from);
	$to		=date_create($to);
	$diff	=date_diff($from,$to);
	$years	=$diff->format('%y');
	$months	=$diff->format('%m');
	$days	=$diff->format('%d');
	if($years){
		$years=pluralize_if($years,'year');
	}
	if($months){
		$months=pluralize_if($months,'month');
	}
	if($days){
		$days=pluralize_if($days,'day');
	}
	if($years && $months && $days){
		$years.=',';
	}elseif($years && ($months || $days)){
		$years.=' and';
	}
	if($months && $days){
		$months.=' and';
	}
	return implode(' ',[$years,$months,$days]);
}
# activity to noun
function to_noun($word){
	switch($word){
		case 'ride':
			return 'cycling';
		case 'run':
			return 'running';
		case 'swim':
			return 'swimming';
	}
}