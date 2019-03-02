<?php # Trim backtrace
# Updated 14/03/2017 14:06
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
function json_return($data){
	echo json_encode($data);
	exit;
}
# Return formatted __LINE__
# Updated 19/02/2018 13:55
function line($return=false){
	$stack=debug_backtrace()[0];
	$line=$stack['file'].': '.$stack['line'].'<br>';
	if($return){
		return $line;
	}
	echo $line;
}
# Print Preformated
# Updated 19/02/2018 13:54
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
# Updated 02/11/2018 16:50
function sql_date($date_from_sql=NULL){
	if($date_from_sql===NULL){
		$time=time();
	}elseif(!is_numeric($date_from_sql)){
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
# Updated 02/11/2018 16:50
function sql_datetime($datetime_from_sql=NULL){
	if($datetime_from_sql===NULL){
		$time=time();
	}elseif(!is_numeric($datetime_from_sql)){
		$time=strtotime($datetime_from_sql);
	}else{
		$time=$datetime_from_sql;
	}
	if($time<=0){
		return false;
	}
	return sql_date($datetime_from_sql).' at '.date(TIME_FORMAT,$time);
}
# Returns a date and time reformated to ISO 8601
# Updated 29/03/2017 21:27
function iso_datetime($time){
	if(!is_numeric($time)){
		$time=strtotime($time);
	}
	if($time<=0){
		return false;
	}
	return date('Y-m-d\TH:i:s',$time);
}

# Calculates days Hours:Minutes
# Updated 13-05-2016 09:31
function seconds_to_time($seconds){
	if($seconds){
	    $dtF = new DateTime("@0");
	    $dtT = new DateTime("@".$seconds);
		$date=$dtF->diff($dtT);
		$days=$date->format('%a');
		$hours=$date->format('%h');
		$minutes=$date->format('%i');
		return ($seconds<0?'-':'').sprintf('%02d',$hours+($days*24)).':'.sprintf('%02d',$minutes);
	}
	return '00:00';
}
# Time ago
# Updated 01/11/2016 15:18
function time_ago($sql_date){
	$time=time()-strtotime($sql_date);
	if($time<60){
		return number_format($time).' second'.(ceil($time)>1?'s':'').' ago';
	}
	if($time/60<60){
		$time=floor($time/60);
		return $time.' minute'.($time>1?'s':'').' ago';
	}
	if($time/60/60<24){
		$time=floor($time/60/60);
		return $time.' hour'.($time>1?'s':'').' ago';
	}
	if($time/60/60/24<7){
		$time=floor($time/60/60/24);
		return $time.' day'.($time>1?'s':'').' ago';
	}
	if($time/60/60/24/7<7){
		$time=floor($time/60/60/24/7);
		return $time.' week'.($time>1?'s':'').' ago';
	}
	if($time/60/60/24/30.4375<12){
		$time=floor($time/60/60/24/30.4375);
		return $time.' month'.($time>1?'s':'').' ago';
	}
	$time=floor($time/60/60/24/365.25);
	return $time.' year'.($time>1?'s':'').' ago';
}