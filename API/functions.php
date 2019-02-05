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