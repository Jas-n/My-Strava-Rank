<?php # Auto-load Classes
set_error_handler('log_errors',~E_NOTICE);
register_shutdown_function(function(){
	$error = error_get_last();
	if($error["type"]==E_ERROR){
		log_errors($error["type"],$error["message"],$error["file"],$error["line"]);
	}
});
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
# Sends out HTML Email (Requires PHPMailer: https://github.com/PHPMailer/PHPMailer)
# Updated 02/11/2017 12:51
function email($to,$title,$description,$content,$attachments=NULL,$from=NULL,$delivery=NULL,$read=NULL){
	global $core;
	if(!is_array($to)){
		if(strpos($to,',')!==false){
			$to=explode(',',$to);
		}else{
			$to=array($to);
		}
	}
	foreach($to as $t){
		if(!validate_email($t)){
			$core->set_message('Error','Unable to send email(s), address is not valid: '.$t);
			return false;
		}
	}
	if(is_file(ROOT.'images/logos/150.png')){
		$logo='<img alt="'.SITE_NAME.'" src="'.SERVER_NAME.'images/logos/150.png" style="max-width:600px;text-align:center;" id="headerImage">';
	}else{
		$logo=SITE_NAME;
	}
	$fields=array(
		'{{{TITLE}}}',
		'{{{DESCRIPTION}}}',
		'{{{LOGO}}}',
		'{{{CONTENT}}}',
		'{{{IF TWITTER}}}',
		'{{{TWITTER}}}',
		'{{{ENDIF TWITTER}}}',
		'{{{IF FACEBOOK}}}',
		'{{{FACEBOOK}}}',
		'{{{ENDIF FACEBOOK}}}',
		'{{{YEAR}}}',
		'{{{SITE NAME}}}',
		'{{{COMPANY NAME}}}',
		'{{{COMPANY ADDRESS}}}',
		'{{{LOGIN URL}}}'
	);
	$data=array(
		$title,
		$description,
		$logo,
		$content,
		defined('TWITTER')?'':'<!--',
		TWITTER,
		defined('TWITTER')?'':'-->',
		defined('FACEBOOK')?'':'<!--',
		FACEBOOK,
		defined('FACEBOOK')?'':'-->',
		date('Y'),
		SITE_NAME,
		COMPANY_NAME,
		defined('COMPANY_ADDRESS')?COMPANY_ADDRESS:'',
		SERVER_NAME.'/login'
	);
	$template=ROOT.'themes/'.THEME.'/emails/base.html';
	if(!is_file($template)){
		$template=ROOT.'emails/base.html';
	}
	$html=str_replace($fields,$data,file_get_contents($template));
	$template=ROOT.'themes/'.THEME.'/emails/base.txt';
	if(!is_file($template)){
		$template=ROOT.'emails/base.txt';
	}
	$txt=strip_tags(str_replace($fields,$data,file_get_contents($template)));
	# Include PHPMailer
	include_once(ROOT.'libraries/phpmailer.php');
	$emailer=new PHPMailer();
	$emailer->isHTML(true);
	$emailer->MsgHTML($html);
	$emailer->AltBody=$txt;
	if(validate_email($delivery)){
		$emailer->AddCustomHeader("Return-receipt-to:".$delivery);
	}
	if(validate_email($read)){
		$emailer->ConfirmReadingTo=$read;
	}
	if($from){
		$from=$emailer->parseAddresses($from);
		if($from[0]){
			$emailer->setFrom($from[0]['address'],$from[0]['name']);
		}
	}else{
		$emailer->setFrom(SITE_EMAIL,SITE_NAME);
	}
	$emailer->Subject=$title;
	foreach($to as $t){
		$emailer->addAddress(trim($t));
	}
	if($attachments!==NULL){
		if(!is_array($attachments)){
			$attachments=array($attachments);
		}
		foreach($attachments as &$attachment){
			if(is_file($attachment)){
				$emailer->addAttachment($attachment);
			}elseif(is_file(ROOT.$attachment)){
				$emailer->addAttachment(ROOT.$attachment);
			}
		}
	}
	if($emailer->Send()){
		return array(
			'status'=>true,
			'data'	=>array(
				'to'			=>$to,
				'title'			=>$title,
				'description'	=>$description,
				'content'		=>$content,
				'from'			=>$from
			)
		);
	}else{
		$core->set_message('error',"Error sending Email");
		$core->log_message(1,
			'Error Sending Email',
			$emailer->ErrorInfo,
			array(
				'args'	=>func_get_args(),
				'trace'	=>debug()
			)
		);
		return array(
			'status'=>false,
			'message'=>$emailer->ErrorInfo,
			'data'	=>array(
				'to'			=>$to,
				'title'			=>$title,
				'description'	=>$description,
				'content'		=>$content,
				'from'			=>$from
			)
		);
	}
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