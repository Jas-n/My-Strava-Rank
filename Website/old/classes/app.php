<?php # Version 1.6
class app{
	protected $addtofoot=array();
	protected $addtohead=array();
	protected $errors=array();
	protected $information=array();
	protected $success=array();
	
	public $page_title;
	public $require=array();
	
	public function add_to_foot($code){
		$this->addtofoot[]=$code;
	}
	public function add_to_head($code){
		$this->addtohead[]=$code;
	}
	public function get_css(){
		global $db,$form_included;
		if(is_array($this->require)){
			$require=array_map('strtolower',$this->require);
		}
		$out='<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" rel="stylesheet">
		<link href="//gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css" rel="stylesheet">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">';
		if(get_dir()=='users'){
			$out.='<link href="//fonts.googleapis.com/css?family=Titillium+Web:400,600" rel="stylesheet">';
		}
		if($form_included){
			$out.='<link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.min.css" rel="stylesheet">';
		}
		$out.='<link href="/css/core.css" rel="stylesheet">';
		if($require && in_array('js.lightbox',$require)){
			$out.='<link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-lightbox/0.7.0/bootstrap-lightbox.min.css" rel="stylesheet">';
		}
		#Include folder specific CSS
		if(get_dir() && is_file(ROOT.'css/'.get_dir().'.css')){
			$out.='<link href="/css/'.get_dir().'.css" rel="stylesheet">';
		}elseif(!get_dir()){
			$out.='<link href="/css/front.css" rel="stylesheet">';
		}
		$out.='<link rel="stylesheet" media="print" href="/css/print.css">';
		# File specific CSS
		if(is_file(ROOT.'css/'.str_replace('/','-',substr($_SERVER['PHP_SELF'],1,-3)).'css')!=0){
			$out.='<link href="/css/'.str_replace('/','-',substr($_SERVER['PHP_SELF'],1,-3)).'css" rel="stylesheet">';
  		}
		echo $out;
	}
	public function get_head_js(){
		global $page,$user;
		$require=array_map('strtolower',$this->require);
		if($this->addtohead){
			foreach($this->addtohead as $head){
				$out.=htmlspecialchars_decode($head);
			}
		}
		echo $out;
	}
	public function get_icons(){
		if(is_dir(ROOT.'images/icons')){
			echo '<link rel="apple-touch-icon"	href="/images/icons/57.png"		sizes="57x57">
			<link rel="apple-touch-icon"		href="/images/icons/60.png" 	sizes="60x60">
			<link rel="apple-touch-icon"		href="/images/icons/72.png" 	sizes="72x72">
			<link rel="apple-touch-icon"		href="/images/icons/76.png" 	sizes="76x76">
			<link rel="icon"					href="/images/icons96.png"		sizes="96x96"	type="image/png">
			<link rel="apple-touch-icon"		href="/images/icons/114.png"	sizes="114x114">
			<link rel="apple-touch-icon"		href="/images/icons/120.png"	sizes="120x120">
			<link rel="apple-touch-icon"		href="/images/icons/144.png"	sizes="144x144">
			<link rel="apple-touch-icon"		href="/images/icons/152.png"	sizes="152x152">
			<link rel="apple-touch-icon"		href="/images/icons/180.png"	sizes="180x180">
			<link rel="icon"					href="/images/icons/192.png"	sizes="192x192"	type="image/png">
			<link rel="shortcut icon"			href="/images/icons/favicon.ico">
			<link rel="manifest"				href="/images/icons/manifest.json">
			<meta name="msapplication-TileColor"	content="'.COLOUR.'">
			<meta name="msapplication-TileImage"	content="/144.png">
			<meta name="theme-color"				content="'.COLOUR.'">';
		}
	}
	public function get_foot_js(){
		global $db,$form_included,$page,$user;
		$require=array_map('strtolower',$this->require);
		$out='<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>';
		if(in_array('js.tooltip',$require)){
			$out.='<script src="//cdnjs.cloudflare.com/ajax/libs/tether/1.1.0/js/tether.min.js"></script>';
		}
		$out.='<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"></script>
		<script src="//gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js"></script>';
		if($require && in_array('js.lightbox',$require)){
			$out.='<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-lightbox/0.7.0/bootstrap-lightbox.min.js"></script>';
		}
		if($form_included){
			$out.='<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>';
		}
		if(in_array('js.tinymce',$require)){
			$out.='<script src="//cdnjs.cloudflare.com/ajax/libs/tinymce/4.2.6/tinymce.min.js"></script>';
		}
		if(DEBUG){
			$out.='<script src="//formation.software/SCRIPTS/debug.js"></script>';
		}
		$out.='<script>
			var is_logged_in='.(is_logged_in()?'true':'false').';';
			if(DEBUG){
				$out.="var DEBUG=true;";
			}
			if($_GET){
				$out.='var _GET={';
					foreach($_GET as $key=>$value){
						$out.=$key.':"'.$value.'",';
					}
				$out.='};';
			}
			$out.='var page="'.$page->slug.'";
			var user_id='.($user->id?$user->id:0).';';
			if(in_array('js.sortable',$require) || in_array('js.tinymce',$require) || in_array('js.tooltip',$require) || $form_included){
				$out.='$(document).ready(function(){';
					if(in_array('js.tooltip',$require)){
						$out.='$("[data-toggle=tooltip]").tooltip();';
					}
					if(in_array('js.tinymce',$require)){
						$out.='tinymce.init({
							browser_spellcheck:true,
							content_css:"/css/tinymce.css",
							menubar:false,
							plugins:"link,paste",
							paste_auto_cleanup_on_paste:true,
							selector:".tinymce",
							style_formats:[
								{title: "Headers",items:[
									{title:"Header 2",format:"h2"},
									{title:"Header 3",format:"h3"},
									{title:"Header 4",format:"h4"},
									{title:"Header 5",format:"h5"},
									{title:"Header 6",format:"h6"}
								]},
								{title:"Inline",items:[
									{title:"Underline",icon:"underline",format:"underline"},
									{title:"Strikethrough",icon:"strikethrough",format:"strikethrough"},
									{title:"Superscript",icon:"superscript",format:"superscript"},
									{title:"Subscript",icon:"subscript",format:"subscript"}
								]},
								{title:"Blocks",items:[
									{title:"Paragraph",format:"p"},
									{title:"Blockquote",format:"blockquote"}
								]},
								{title:"Alignment",items:[
									{title:"Left",icon:"alignleft",format:"alignleft"},
									{title:"Center",icon:"aligncenter",format:"aligncenter"},
									{title:"Right",icon:"alignright",format:"alignright"},
									{title:"Justify",icon:"alignjustify",format:"alignjustify"}
								]}
							]
						});';
					}
					if(in_array('js.sortable',$require)){
						$out.='$(".sortable").sortable();
						$(".sortable").disableSelection();';
					}
				$out.='});';
			}
		$out.='</script>
		<script src="/js/core.js"></script>';
		if($GLOBALS['form_included']){
			$out.='<script src="/js/form.js"></script>';
		}
		$out.='<script src="/js/locations.js"></script>';
		if(in_array('js.searcher',$require)){
			$out.='<script src="/js/searcher.js"></script>';
		}
		#Include folder specific JS
		if(is_file(ROOT.'/js/'.get_dir().'.js')){
			$out.='<script src="/js/'.get_dir().'.js"></script>';
		}
		# File specific
		if(is_file(ROOT.'/js/'.str_replace('/','-',substr($_SERVER['PHP_SELF'],1,-3)).'js')!=0){
			$out.='<script src="/js/'.str_replace('/','-',substr($_SERVER['PHP_SELF'],1,-3)).'js"></script>';
  		}
		if($this->addtofoot){
			foreach($this->addtofoot as $foot){
				$out.=$foot;
			}
		}
		if($require && in_array('js.maps',$require)){
			$out.='<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEkSOP4ZfNndvghP0KWJ0LL9Xr867kAt0&callback=initMap" async defer></script>';
		}
		echo $out;
	}
	public function get_messages(){
		if($this->errors){
			$out="<div class='alert alert-danger' role='alert'>";
				foreach($this->errors as $error){
					$out.="<p>{$error}</p>";
				}
			$out.="</div>";
		}
		if($this->information){
			$out.="<div class='alert alert-info' role='alert'>";
				foreach($this->information as $information){
					$out.="<p>{$information}</p>";
				}
			$out.="</div>";
		}
		if($this->success){
			$out.="<div class='alert alert-success' role='alert'>";
				foreach($this->success as $success){
					$out.="<p>{$success}</p>";
				}
			$out.="</div>";
		}
		echo $out;
	}
	# Log something
	public function log_message($level,$title,$message,$data=NULL){
		global $db;
		switch($level){
			case 'danger':
			case 'error':
				$level=1;
				break;
			case 'warning':
				$level=2;
				break;
			case 'info':
			case 'information':
			case 'success':
				$level=3;
				break;
		}
		if($data){
			$data['data']=$data;
		}
		if($level==1){
			$data['trace']=debug();
		}
		$db->query(
			'INSERT INTO `logs` (`level`,`user_id`,`title`,`message`,`date`,`data`) VALUES (?,?,?,?,?,?)',
			array(
				$level,
				$_SESSION['user_id']?$_SESSION['user_id']:-1,
				$title,
				$message,
				DATE_TIME,
				$data
			),0
		);
		return $db->insert_id();
	}
	# Get page title
	public function page_title(){
		if($this->page_title){
			echo $this->page_title." | ";
		}else{
			$page=basename($_SERVER['PHP_SELF'],'.php');
			$this->page_title=$page;
			if($page=='index' && get_dir()){
				echo "Dashboard | ";
			}else{
				if($pos=strpos($page,'.')){
					$page=substr($page,0,$pos);
				}
				echo ucwords(str_replace('-',' ',$page))." | ";
			}
		}
		echo $out.(defined('SITE_NAME')?SITE_NAME:'glowt');
	}
	# Set message for visual output
	public function set_message($type,$message){
		switch(strtolower($type)){
			case 'error':
				$this->errors[]=$message;
				break;
			case 'info':
			case 'information':
				$this->information[]=$message;
				break;
			case 'success':
				$this->success[]=$message;
				break;
		}
	}
}