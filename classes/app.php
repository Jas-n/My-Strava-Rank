<?php namespace Jasn\MSR;
class app{
	public $require=array();
	public function get_css(){
		global $core;
		if(is_array($this->require)){
			$require=array_map('strtolower',$this->require);
		}
		$out='';
		$css_files=$core->get_css();
		if($require && in_array('php.new_calendar',$require)){
			$css_files[]='/css/calendar.css?t='.$core->mtime;
		}
		foreach($css_files as $css_file){
			$out.='<link rel="stylesheet" href="'.$css_file.'">';
		}
		echo $out;
	}
	public function get_icons(){
		if(is_dir(ROOT.'images/icons')){?>
			<link rel="apple-touch-icon"	href="/images/icons/57.png"		sizes="57x57">
			<link rel="apple-touch-icon"	href="/images/icons/60.png" 	sizes="60x60">
			<link rel="apple-touch-icon"	href="/images/icons/72.png" 	sizes="72x72">
			<link rel="apple-touch-icon"	href="/images/icons/76.png" 	sizes="76x76">
			<link rel="icon"				href="/images/icons/96.png"		sizes="96x96"	type="image/png">
			<link rel="apple-touch-icon"	href="/images/icons/114.png"	sizes="114x114">
			<link rel="apple-touch-icon"	href="/images/icons/120.png"	sizes="120x120">
			<link rel="apple-touch-icon"	href="/images/icons/144.png"	sizes="144x144">
			<link rel="apple-touch-icon"	href="/images/icons/152.png"	sizes="152x152">
			<link rel="apple-touch-icon"	href="/images/icons/180.png"	sizes="180x180">
			<link rel="icon"				href="/images/icons/192.png"	sizes="192x192"	type="image/png">
			<link rel="shortcut icon"		href="/images/icons/favicon.ico">
			<link rel="manifest"			href="/images/icons/manifest.json">
			<meta name="msapplication-TileColor"	content="<?=COLOUR?>">
			<meta name="msapplication-TileImage"	content="/144.png">
			<meta name="theme-color"				content="<?=COLOUR?>">
		<?php }
	}
	public function get_foot_js(){
		global $core,$addresses,$form_included,$page,$user;
		$require=array_map('strtolower',$this->require);
		$out='<script src="'.$core->cdn->jquery->core.'"></script>';
		if(get_dir()){
			$out.='<script src="'.$core->cdn->jquery->ui.'"></script>';
		}
		$out.='<script src="'.$core->cdn->bootstrap.'"></script>';
		if($form_included){
			$out.='<script src="'.$core->cdn->datepicker->js.'"></script>';
		}
		if(in_array('js.tinymce',$require)){
			$out.='<script src="'.$core->cdn->tinymce.'"></script>
			<script src="/js/tinymce.js?t='.$core->mtime.'"></script>';
		}
		$out.='<script src="/core/js/php.js?t='.$core->mtime.'"></script>
		<script src="/js/app.js?t='.$core->mtime.'"></script>';
		if(!get_dir()){
			$out.='<script src="/js/root.js?t='.$core->mtime.'"></script>';
		}
		if($form_included){
			$out.='<script src="/core/js/form.js?t='.$core->mtime.'"></script>';
		}
		if(in_array('js.searcher',$require)){
			$out.='<script src="/js/searcher.js?t='.$core->mtime.'"></script>';
		}
		#Include folder specific JS
		if(is_file(ROOT.'js/'.get_dir().'.js')){
			$out.='<script src="/js/'.get_dir().'.js?t='.$core->mtime.'"></script>';
		}
		if(is_file(ROOT.'js/'.substr($_SERVER['PHP_SELF'],1,-3).'js')){
			# New
			$out.='<script src="/js/'.substr($_SERVER['PHP_SELF'],1,-3).'js?t='.$core->mtime.'"></script>';
  		}
		echo $out;
	}
	# Get page title
	public function page_title(){
		global $core;
		if($_GET['module']){
			$module_name=$this->modules[$this->get_module_id($_GET['module'])]['name'];
		}
		if($this->page_title && strtolower($this->page_title)!='index'){
			$out=crop($this->page_title,25).' | ';
		}else{
			$page=$core->page['slug'];
			if($page=='index' && get_dir()){
				$page='Dashboard';
			}elseif($page=='module'){
				if($_GET['file']=='index'){
					$page=$module_name;
				}else{
					$page=$core->page['slug'];
				}
			};
			$page=ucwords(str_replace(array('-','_'),' ',$page));
			$this->page_title=$page;
			$out.=crop($page,25).' | ';
		}
		echo $out.(defined('SITE_NAME')?SITE_NAME:'glowt');
	}
	# Caching Methods
	private function minify_css($css){
		# Strips Comments
		$css = preg_replace('!/\*.*?\*/!s','', $css);
		$css = preg_replace('/\n\s*\n/',"\n", $css);
		# Minifies
		$css = preg_replace('/[\n\r \t]/',' ', $css);
		$css = preg_replace('/ +/',' ', $css);
		$css = preg_replace('/ ?([,:;{}]) ?/','$1',$css);
		# Kill Trailing Semicolon
		$css = preg_replace('/;}/','}',$css);
		# Return Minified CSS
		return $css;
	}
	# Tokens
	public function create_token($user_id=0,$data=NULL,$expiry=NULL){
		global $db;
		$token=$db->next_hex_id('tokens','token');
		$db->query(
			"INSERT INTO `tokens` (
				`user_id`,`token`,`data`,`added`,`expiry`
			) VALUES (?,?,?,?,?)",
			array(
				$user_id,
				$token,
				$data!==NULL?json_encode($data):'',
				DATE_TIME,
				date('Y-m-d H:i:s',strtotime($expiry)?strtotime($expiry):strtotime('+ '.MONTH_LENGTH.' days'))
			)
		);
		return $token;
	}
	public function expire_token($token){
		global $db;
		$db->query("UPDATE `tokens` SET `expiry`=? WHERE `token`=?",array(DATE_TIME,$token));
		return true;
	}
	public function get_token($token){
		global $db;
		$data=$db->get_row("SELECT * FROM `tokens` WHERE `token`=? AND `expiry`>?",array($token,DATE_TIME));
		if($data['data']){
			$data['data']=json_decode($data['data'],1);
		}
		return $data;
	}
}