<?php class search extends form{
	function __construct(){
		parent::__construct("name=search&class=navbar-form navbar-right");
		parent::add_field(array(
			'name'			=>'search',
			'placeholder'	=>'Search...',
			'type'			=>'search',
			'value'			=>$_GET['term']
		));
	}
	public function process(){
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']=='error'){
				$this->set_message('error',$results['data']);
			}else{
				$results=$this->unname($results['data']);
				header('Location: search?term='.urlencode($results['search']));
				exit;
			}
		}
	}
}?>