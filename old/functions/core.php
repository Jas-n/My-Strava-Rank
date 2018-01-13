<?php basename($_SERVER['PHP_SELF'])=='core.php'?die('Access Denied'):'';
# PHP compatibility functions
foreach(scandir(ROOT.'functions') as $dir){
	if(!in_array($dir,array('.','..')) && is_dir(ROOT.'functions/'.$dir)){
		foreach(scandir(ROOT.'functions/'.$dir) as $file){
			if(!in_array($file,array('.','..','index.php'))){
				include(ROOT.'functions/'.$dir.'/'.$file);
			}
		}
	}
}
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
function log_errors($severity,$message,$file,$line,array $context=NULL){
	global $db;
	if(isset($db)){
		$db->error('PHP',$message,$file,$line,$severity,debug());
	}else{
		restore_error_handler();
	}
	return true;
}
# Base64url encode
function base64url_encode($string){
	return rtrim(strtr(base64_encode($string),'+/','-_'),'=');
}
# Base64url decode
function base64url_decode($string){
	return base64_decode(str_pad(strtr($string,'-_','+/'),strlen($string)%4,'=',STR_PAD_RIGHT));
}
# <br> to \r\n
function br2nl($text){
	return preg_replace('/<br\\s*?\/??>/i','',$text);
}
# Limit $text to $length (Default 50)
function crop($text,$length=50){
	if(strlen($text)>$length){
		$text=strip_tags(substr($text,0,$length-1)).'&hellip;';
	}
	return $text;
}
function curl($url, $method = 'GET', $data = false, $headers = false, $returnInfo = false){
    $ch = curl_init();
    if($method == 'POST') {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        if($data !== false) {
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        }
    } else {
        if($data !== false) {
            if(is_array($data)) {
                $dataTokens = array();
                foreach($data as $key => $value) {
                    array_push($dataTokens, urlencode($key).'='.urlencode($value));
                }
                $data = implode('&', $dataTokens);
            }
            curl_setopt($ch, CURLOPT_URL, $url.'?'.$data);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    if($headers !== false) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $contents = curl_exec($ch);
    
    if($returnInfo) {
        $info = curl_getinfo($ch);
    }
    curl_close($ch);
    if($returnInfo) {
        return array('contents' => $contents, 'info' => $info);
    } else {
        return $contents;
    }
}
# Sends out HTML Email
function email($to,$title,$description,$content,$attachments=NULL,$from=NULL){
	global $app;
	if(is_file(ROOT.'images/logos/150.png')){
		$logo='<img alt="'.SITE_NAME.'" src="'.SERVER_NAME.'/images/logos/150.png" style="max-width:600px;text-align:center;" id="headerImage">';
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
		defined('COMPANY_ADDRESS')?COMPANY_ADDRESS:'',
		SERVER_NAME.'/login'
	);
	$template=ROOT.'themes/'.THEME.'/emails/base.html';
	if(!is_file($template)){
		$template=ROOT.'/emails/base.html';
	}
	$html=str_replace($fields,$data,file_get_contents($template));
	$template=ROOT.'themes/'.THEME.'/emails/base.txt';
	if(!is_file($template)){
		$template=ROOT.'/emails/base.txt';
	}
	$txt=strip_tags(str_replace($fields,$data,file_get_contents($template)));
	# Include PHPMailer
	include_once(ROOT.'libraries/phpmailer.php');
	$emailer=new PHPMailer();
	$emailer->isHTML(true);
	$emailer->MsgHTML($html);
	$emailer->AltBody=$txt;
	if($from){
		$emailer->addReplyTo($from,SITE_NAME);
	}else{
		$emailer->addReplyTo(SITE_EMAIL,SITE_NAME);
	}
	$emailer->Subject=$title;
	if(!is_array($to)){
		if(strpos($to,',')!==false){
			$to=explode(',',$to);
		}else{
			$to=array($to);
		}
	}
	foreach($to as $t){
		$emailer->addAddress($t);
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
		$data=array(
			'to'			=>$to,
			'title'			=>$title,
			'description'	=>$description,
			'content'		=>$content,
			'from'			=>$from
		);
		$app->set_message('error',"Error sending Email");
		$app->log_message(1,'Error Sending Email',$emailer->ErrorInfo,$data);
		return array(
			'status'=>false,
			'message'=>$emailer->ErrorInfo,
			'data'	=>$data
		);
	}
}
/*function email_later($to,$title,$description,$content){
	global $db;
	if(is_array($to)){
		foreach($to as $email){
			email_later($email,$subject,$content);
		}
	}
	$db->query(
		"INSTERT INTO `pending_email` (`to_email`,`subject`,`date`,`content`) VALUES (?,?,?,?)",
		array(
			$to,
			$subject,
			date('Y-m-d H:i:s'),
			$content
		)
	);
}*/
# Rounds $value down to $precision
function floor_precision($value,$precision=NULL){if($precision<0){$precision=$precision*-1;return floor($value/pow(10,$precision))*pow(10,$precision);}elseif($precision==0 || $precision==NULL){return floor($value);}else{$dec=strpos($value,'.');$poi=substr($value,$dec+1,$precision);if($precision>=strlen(substr($value,strpos($value,'.')))){return floor($value).'.'.$poi.substr(pow(10,$precision-strlen(substr($value,strpos($value,'.')))+1),1);}else{return floor($value).'.'.$poi;}}}
# Font Awesome File icon
function font_awesome_file_icon($ext){
	switch($ext){
		case 'doc':
		case 'docx':
			$fa='fa fa-file-word-o';
			break;
		case 'flac':
		case 'mp3':
		case 'wav':
			$fa='fa fa-file-audio-o';
			break;
		case 'pdf':
			$fa='fa fa-file-pdf-o';
			break;
		case 'xls':
		case 'xlsx':
			$fa='fa fa-file-excel-o';
			break;
		case 'png':
			$fa='fa fa-file-image-o';
			break;
		case 'zip':
			$fa='fa fa-file-archive-o';
			break;
		default:
			$fa='fa fa-file-o';
			break;
	}
	return $fa;
}
# Get browser
function getBrowser(){$u_agent=$_SERVER['HTTP_USER_AGENT'];$bname='Unknown';$platform='Unknown';$version="";if(preg_match('/linux/i',$u_agent)){$platform='linux';}elseif(preg_match('/macintosh|mac os x/i',$u_agent)){$platform='mac';}elseif(preg_match('/windows|win32/i',$u_agent)){$platform='windows';}if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){$bname='Internet Explorer';$ub="MSIE";}elseif(preg_match('/Edge/i',$u_agent)){$bname='Edge';$ub="Edge";}elseif(preg_match('/Firefox/i',$u_agent)){$bname='Mozilla Firefox';$ub="Firefox";}elseif(preg_match('/Chrome/i',$u_agent)){$bname='Google Chrome';$ub="Chrome";}elseif(preg_match('/Safari/i',$u_agent)){$bname='Apple Safari';$ub="Safari";}elseif(preg_match('/Opera/i',$u_agent)){$bname='Opera'; $ub="Opera";}elseif(preg_match('/Netscape/i',$u_agent)){$bname='Netscape';$ub="Netscape";}$known=array('Version',$ub,'other');$pattern='#(?<browser>'.join('|',$known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';if(!preg_match_all($pattern,$u_agent,$matches)){}$i=count($matches['browser']);if($i!=1){if(strripos($u_agent,"Version") < strripos($u_agent,$ub)){$version=$matches['version'][0];}else{$version=$matches['version'][1];}}else{$version=$matches['version'][0];}if($version==null || $version==""){$version="?";}return array('userAgent'=>$u_agent,'name'=>$bname,'version'=>$version,'platform'=>$platform,'pattern'=>$pattern);}
# Current Directory
function get_dir($level=0){$dir=explode('/',str_replace(ROOT,'',getcwd().'/'));array_pop($dir);return $dir[sizeof($dir)-1-$level];}
function text2rgba($text,$opacity=1){
	$hex=substr(md5($text),0,6);
	list($r,$g,$b)=array($hex[0].$hex[1],$hex[2].$hex[3],$hex[4].$hex[5]);
	$r=hexdec($r);
	$g=hexdec($g);
	$b=hexdec($b);
	return array('red'=>$r,'green'=>$g,'blue'=>$b,'alpha'=>min(array($opacity,1)));
}
# Is JSON
function is_json($string){@json_decode($string);return (json_last_error()==JSON_ERROR_NONE);}
# Checks if a user is logged in
function is_logged_in(){if($_SESSION['user_id']){return true;}return false;}
# Pagination
function pagination($result_count,$echo=true){
	$pages=ceil($result_count/ITEMS_PER_PAGE);
	if($pages>1){
		$page=$_GET['page'];
		$out.='<ul class="pagination pagination-sm">';
		if($pages<=10){
			for($i=1;$i<=$pages;$i++){
				$out.='<li class="page-item'.($page==$i || ($i==1 && !$page)?' active':'').'">'.pagination_link($i).'</li>';
			}
		}else{
			/*First Page*/
			$out.='<li class="page-item'.($page==1 || !$page?' active':'').'">'.pagination_link(1).'</li>';
			/*Page= 1-4*/
			if($page<5){
				if($page<4){
					$toout=6;
				}else{
					$toout=7;
				}
				for($i=2;$i<$toout;$i++){
					$out.='<li class="page-item'.($page==$i?' active':'').'">'.pagination_link($i).'</li>';
				}
				$out.='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
			}
			/*Page>=5*/
			elseif($page<$pages-5){
				$out.='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
				for($i=$page-2;$i<$page;$i++){
					$out.='<li class="page-item'.($page==$i?' active':'').'">'.pagination_link($i).'</li>';
				}
				$out.='<li class="page-item active">'.pagination_link($page).'</li>';
				for($i=$page+1;$i<=$page+2;$i++){
					$out.='<li class="page-item'.($page==$i?' active':'').'">'.pagination_link($i).'</li>';
				}
				$out.='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
			}
			/*If page last 5*/
			elseif($page>$pages-4){
				$out.='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
				if($pages-$page==3){
					$toout=5;
				}else{
					$toout=4;
				}
				for($i=$pages-$toout;$i<$pages;$i++){
					$out.='<li class="page-item'.($page==$i?' active':'').'">'.pagination_link($i).'</li>';
				}
			}else{
				$out.='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
				for($i=$page-2;$i<$page;$i++){
					$out.='<li class="page-item'.($page==$i?' active':'').'">'.pagination_link($i).'</li>';
				}
				
				$out.='<li class="page-item active">'.pagination_link($page).'</li>';
				for($i=$page+1;$i<=$page+2;$i++){
					$out.='<li'.($page==$i?' class="active"':'').'>'.pagination_link($i).'</li>';
				}
				$out.='<li class="page-item disabled"><a class="page-link"><span aria-hidden="true">&hellip;</span></a></li>';
			}
			/*Last Page*/
			$out.='<li class="page-item'.($page==$pages?' active':'').'">'.pagination_link($pages).'</li>';
		}
		$out.='</ul>';
		if($echo){
			echo $out;
		}else{
			return $out;
		}
	}
}
# Recreate pagination link
function pagination_link($page){
	$get=$_GET;
	parse_str($_SERVER['QUERY_STRING'],$query_string);
	$get=array_diff($get,$query_string);
	$out.='<a class="page-link" href="'.$_SERVER['REDIRECT_URL'];
	unset($get['page']);
	if(sizeof($get)>0){
		$out.='?'.http_build_query($get).'&';
	}else{
		$out.='?';
	}
	$out.='page='.$page.'">'.$page.'</a>';
	return $out;
}
# Print Preformated
function print_pre($expression,$return=false){
	if($expression){
		$out.='<div class="debug">
			<p>Data Debug</p>
			<pre>'.htmlspecialchars(print_r($expression,true)).'</pre>
		</div>';
		if($return){
			return $out;
		}else{
			echo $out;
		}
	}
}
# Calculates days Hours:Minutes
function seconds_to_time($seconds){
	if($seconds){
		$seconds=floor($seconds);
	    $dtF = new DateTime("@0");
	    $dtT = new DateTime("@".$seconds);
		$date=$dtF->diff($dtT);
		if($days=$date->format('%a')){
			$out[]=$days.'d';
		}
		if($hours=$date->format('%h')){
			$out[]=$hours.'h';
		}
		if($minutes=$date->format('%i')){
			$out[]=$minutes.'m';
		}
		if($seconds=$date->format('%s')){
			$out[]=$seconds.'s';
		}
		return ($seconds<0?'-':'').implode(' ',$out);
	}
	return '0d 0h 0m';
}
function time_ago($sql_date){
	$time=time()-strtotime($sql_date);
	if($time<60){
		return number_format($time,0)." seconds ago";
	}elseif($time/60<60){
		return number_format($time/60)." minutes ago";
	}elseif($time/60/60<60){
		return number_format($time/60/60)." hours ago";
	}elseif($time/60/60/24<24){
		return number_format($time/60/60/24)." days ago";
	}else{
		return number_format($time/60/60/24/7)." weeks ago";
	}
}
# Trim backtrace
function debug(){
	$traces=debug_backtrace();
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
# Regenerate .ICO file with various resolutions
function generate_icons($source=NULL){
	$generated=0;
	$sizes=icon_sizes();
	if(!$source || !is_file($source)){
		$source=ROOT.'images/logos/1000.png';
	}
	$include=ROOT.'libraries/ico.php';
	$destination=ROOT.'images/icons/';
	rrmdir($destination);
	mkdir($destination,0777,1);
	copy(ROOT.'images/index.php',$destination.'index.php');
	# PNG's
	foreach($sizes as $size){
		if(smart_resize_image($source,NULL,$size,$size,0,$destination.$size.'.png',0,'png')){
			$generated++;
		}
	}
	# ICO
	if(is_file($source) && is_file($include)){
		include_once($include);
		$ico=new PHP_ICO(
			$source,
			array(
				array(16,16),
				array(32,32),
				array(48,48),
				array(64,64)
			)
		);
		$ico->save_ico($destination.'favicon.ico');
		$generated++;
	}
	file_put_contents(
		ROOT.'images/icons/manifest.json',
		json_encode((object) array(
			'name'	=>SITE_NAME,
			'icons'	=>array(
				array(
					'src'		=>'/images/icons/36.png',
					'sizes'		=>'36x36',
					'type'		=>'image/png',
					'density'	=>'0.75'
				),
				array(
					'src'		=>'/images/icons/48.png',
					'sizes'		=>'48x48',
					'type'		=>'image/png',
					'density'	=>'1.0'
				),
				array(
					'src'		=>'/images/icons/72.png',
					'sizes'		=>'72x72',
					'type'		=>'image/png',
					'density'	=>'1.5'
				),
				array(
					'src'		=>'/images/icons/96.png',
					'sizes'		=>'96x96',
					'type'		=>'image/png',
					'density'	=>'2.0'
				),
				array(
					'src'		=>'/images/icons/144.png',
					'sizes'		=>'144x144',
					'type'		=>'image/png',
					'density'	=>'3.0'
				),
				array(
					'src'		=>'/images/icons/192.png',
					'sizes'		=>'192x192',
					'type'		=>'image/png',
					'density'	=>'4.0'
				)
			)
		))
	);
	$xml=new DOMDocument('1.0','UTF-8');
	$xml_root=$xml->createElement("browserconfig");
	$xml_root=$xml->appendChild($xml_root);
	$msapplication=$xml->createElement('msapplication');
	$msapplication=$xml_root->appendChild($msapplication);
	$tile=$xml->createElement('tile');
	$tile=$msapplication->appendChild($tile);
	$s70=$xml->createElement('square70x70logo');
	$s70=$tile->appendChild($s70);
	$s70->setAttribute('src','/images/icons/70.png');
	$s150=$xml->createElement('square150x150logo');
	$s150=$tile->appendChild($s150);
	$s150->setAttribute('src','/images/icons/150.png');
	$s310=$xml->createElement('square310x310logo');
	$s310=$tile->appendChild($s310);
	$s310->setAttribute('src','/images/icons/310.png');
	$tc=$xml->createElement('TileColor');
	$tc->nodeValue=COLOUR;
	$tc=$tile->appendChild($tc);
	file_put_contents(ROOT.'browserconfig.xml',$xml->saveXML());
	if($generated){
		return $generated;
	}
	return false;
}
function icon_sizes(){
	return array(
		310,
		192,
		180,
		152,
		150,
		144,
		120,
		114,
		96,
		76,
		72,
		70,
		60,
		57,
		32,
		16
	);
}
# Return formatted __LINE__
function line(){
	$stack=debug_backtrace();
	echo $stack[0]['line'].'<br>';
}
# log level
function log_level($level){
	$l=[3=>'Information',2=>'Warning',1=>'Error'];
	return $l[$level];
}
# Randon text
function random_text($length=10){
	for($i=0;$i<$length/32;$i++){
		$str.=md5(microtime());
	}
	return substr(str_shuffle($str),0,(int)$length);
}
# Recursively remove directory
function rrmdir($dirname){
	if(!is_dir($dirname)){
		return;
	}
	$files=array_diff(scandir($dirname),array('.','..'));
	foreach($files as $file){
		if(is_dir("$dirname/$file")){
			rrmdir("$dirname/$file");
		}else{
			unlink("$dirname/$file");
		}
	}
	return rmdir($dirname);
}
/**
* Image resize
* @param  $file - file name to resize
* @param  $string - The image data, as a string
* @param  $width - new image width
* @param  $height - new image height
* @param  $proportional - keep image proportional, default is no
* @param  $output - name of the new file (include path if needed)
* @param  $delete_original - if true the original image will be deleted
* @param  $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
* @param  $quality - enter 1-100 (100 is best quality) default is 100
* @return boolean|resource
*/
function smart_resize_image($file,$string=NULL,$width=0,$height=0,$proportional=false,$output='file',$delete_original=true,$export=NULL,$use_linux_commands=false,$quality=100){
	if($height<=0 && $width<=0){
		return false;
	}
	if($file===NULL && $string===NULL){
		return false;
	}
	$info=$file!==NULL?getimagesize($file):
	getimagesizefromstring($string);
	$image='';
	$final_width=0;
	$final_height=0;
	list($width_old,$height_old)=$info;
	$cropHeight=$cropWidth=0;
	if($proportional){
		if($width==0){
			$factor=$height/$height_old;
		}elseif($height==0){
			$factor=$width/$width_old;
		}else{
			$factor=min($width/$width_old,$height/$height_old);
		}
		$final_width=round($width_old*$factor);
		$final_height=round($height_old*$factor);
	}else{
		$final_width=($width<=0)?$width_old:$width;
		$final_height=($height<=0)?$height_old:$height;
		$widthX=$width_old/$width;
		$heightX=$height_old/$height;
		$x=min($widthX,$heightX);
		$cropWidth=($width_old-$width*$x)/2;
		$cropHeight=($height_old-$height*$x)/2;
	}
	switch($info[2]){
		case IMAGETYPE_JPEG:
		$file!==null?$image=imagecreatefromjpeg($file):$image=imagecreatefromstring($string);
		break;
	case IMAGETYPE_GIF:
		$file!==null?$image=imagecreatefromgif($file):$image=imagecreatefromstring($string);
		break;
	case IMAGETYPE_PNG:
		$file!==null?$image=imagecreatefrompng($file):$image=imagecreatefromstring($string);
		break;
	default:
		return false;
	}
	$image_resized=imagecreatetruecolor($final_width,$final_height);
	if(($info[2]==IMAGETYPE_GIF) || ($info[2]==IMAGETYPE_PNG)){
		$transparency=imagecolortransparent($image);
		$palletsize=imagecolorstotal($image);
		if($transparency>=0 && $transparency<$palletsize){
			$transparent_color=imagecolorsforindex($image,$transparency);
			$transparency=imagecolorallocate($image_resized,$transparent_color['red'],$transparent_color['green'],$transparent_color['blue']);
			imagefill($image_resized,0,0,$transparency);
			imagecolortransparent($image_resized,$transparency);
		}elseif($info[2]==IMAGETYPE_PNG){
			imagealphablending($image_resized,false);
			$color=imagecolorallocatealpha($image_resized,0,0,0,127);
			imagefill($image_resized,0,0,$color);
			imagesavealpha($image_resized,true);
		}
	}
	imagecopyresampled($image_resized,$image,0,0,$cropWidth,$cropHeight,$final_width,$final_height,$width_old-2*$cropWidth,$height_old-2*$cropHeight);
	if($delete_original){
		if($use_linux_commands){
			exec('rm '.$file);
		}else{
			@unlink($file);
		}
	}
	switch(strtolower($output)){
		case 'browser':
			$mime=image_type_to_mime_type($info[2]);
			header("Content-type: $mime");
			$output=NULL;
			break;
		case 'file':
			$output=$file;
			break;
		case 'return':
			return $image_resized;
			break;
		default:
			break;
	}
	if($export){
		$info[2]=strtolower($export);
	}
	switch($info[2]){
		case IMAGETYPE_GIF:
		case 'gif':
			imagegif($image_resized,$output);
			break;
		case IMAGETYPE_JPEG:
		case 'jpg':
		case 'jpeg':
			imagejpeg($image_resized,$output,$quality);
			break;
		case IMAGETYPE_PNG:
		case 'png':
			$quality=9-(int)((0.9*$quality)/10.0);
			imagepng($image_resized,$output,$quality);
			break;
		default:
			return false;
	}
	return true;
}
# Returns a date reformated fron SQL
function sql_date($date_from_sql){
	return date(DATE_FORMAT,strtotime($date_from_sql));
}
# Returns a date and time reformated fron SQL
function sql_datetime($datetime_from_sql){
	return sql_date($datetime_from_sql).' at '.date(TIME_FORMAT,strtotime($datetime_from_sql));
}
# Removes all the bad from a string
function slug($text){
	return strtolower(str_replace(' ','_',str_replace(array("<",">","#","%","'",'"',"{","}","|","\\","^","[","]","`",";","/","?",":","@","&","=","+","$",",","(",")"),'',$text)));
}
function ordinal($number){
	$ends=array('th','st','nd','rd','th','th','th','th','th','th');
	if((($number % 100) >= 11) && (($number%100) <= 13)){
		return $number. 'th';
	}else{
		return $number.$ends[$number%10];
	}
}