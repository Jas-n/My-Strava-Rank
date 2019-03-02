<?php header('Access-Control-Allow-Origin: *');
include('./init.php');
if(!$_POST['key'] || $_POST['key']!='b3de695507ba629509ef810d00ca6006'){
	json_return(array(
		'status'=>false,
		'message'=>'Key is invalid'
	));
}elseif(!$_POST['class'] || !is_file(ROOT.'classes/'.$_POST['class'].'.php')){
	json_return(array(
		'status'=>false,
		'message'=>'Class is invalid'
	));
}
include(ROOT.'classes/'.$_POST['class'].'.php');
$class=new $_POST['class'];
if(!$_POST['method'] || !method_exists($class,$_POST['method'])){
	json_return(array(
		'status'=>false,
		'message'=>'Method is invalid'
	));
}
if($return=$class->{$_POST['method']}($_POST['data'])){
	if(is_array($return) && array_key_exists('data',$return)){
		json_return($return);
	}else{
		json_return(array(
			'status'=>true,
			'data'=>$return
		));
	}
}else{
	json_return(array(
		'status'=>false,
		'message'=>'There was an error processing the request',
		'return'=>$return
	));
}