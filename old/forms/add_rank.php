<?php class add_rank extends form{
	public function __construct($data=NULL){
		global $addresses,$user;
		parent::__construct("name=add&class=form-horizontal");
		
		parent::add_html('<p class="text-xs-center">');
			parent::add_button(array(
				'class' =>'btn-success',
				'name'  =>'add',
				'type'  =>'submit',
				'value' =>'Add'
			));
		parent::add_html("</p>");
	}
	public function process(){
		if($_POST['form_name']==$this->data['name']){
			global $app,$db,$user;
			$results=parent::process();
			if($results['status']!='error'){
				$results=parent::unname($results['data']);
				print_pre($results);
				$this->reload($results);
			}
		}
	}
}