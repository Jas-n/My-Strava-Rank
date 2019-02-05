<?php require('../init.php');
if($_POST['api_key']!=md5(INSTALL_DATE)){
	echo json_encode(array('status'=>false,'message'=>'Incorrect details'));
	exit;
}
echo json_encode(array('status'=>true,'data'=>$db->query("SELECT * FROM `logs` WHERE `level`=1 AND `date`>?",date('Y-m-d',strtotime('-1 day')))));