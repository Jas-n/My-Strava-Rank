<?php class forgot extends form{
	public function __construct(){
		parent::__construct('name=forgot_password');
		parent::add_field(array(
			'label'			=>'Registered Email',
			'name'			=>'email',
			'placeholder'	=>'Email Address',
			'required'		=>1,
			'type'			=>'email'
		));
		parent::add_field(array(
			'label'			=>'Last Name',
			'name'			=>'last_name',
			'placeholder'	=>'Last Name',
			'required'		=>1,
			'type'			=>'text'
		));
		parent::add_button('name=login&type=submit&class=btn-success&value=Reset');
		parent::add_html("<a class='btn btn-secondary' href='/login'>Login</a>");
	}
	public function process(){
		global $app,$db,$users;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results=$this->unname($results['data']);
				if($usr=$db->query(
					"SELECT `id`,`first_name`,`last_name`,`email`
					FROM `users`
					WHERE `email`=? AND `last_name`=? AND `id` <> ?
					LIMIT 1",
					array(
						$results['email'],
						$results['last_name'],
						0
					)
				)){
					$users->reset_password($usr[0]['id']);
					$app->log_message(3,'Forgot Password','Forgot Password requested and sent by \''.$usr[0]['first_name'].' '.$usr[0]['last_name'].'\'');
					header('Location: login.php?reset=1');
					exit;
				}else{
					$app->log_message(2,'Forgot Password','Password request for unlisted user.');
					$app->set_message('error',"There are no users matching your details.");
				}
			}
		}
	}
}