<?php class login extends form{
	public function __construct(){
		parent::__construct('name=login');
		parent::add_field(array(
			'label'			=>'Email',
			'name'			=>'email',
			'placeholder'	=>'Email Address',
			'required'		=>1,
			'type'			=>'email'
		));
		parent::add_field(array(
			'label'			=>'Password',
			'name'			=>'password',
			'placeholder'	=>'Password',
			'required'		=>1,
			'type'			=>'password'
		));
		parent::add_html('<p class="actions">');
			parent::add_button(array(
				'class'	=>'btn-success btn-login',
				'name'	=>'login',
				'type'	=>'submit',
				'value'	=>'Login'
			));
			parent::add_html('<a class="btn btn-secondary btn-forgot_password" href="./forgot">Reset Password</a>
		</p>');
	}
	public function process(){
		global $app,$db,$user;
		if($_POST['form_name']==$this->data['name']){
			if($_POST['login_forgot']){
				header('Location: forgot');
				exit;
			}else{
				$results=parent::process();
				if($results['status']!='error'){
					$results=parent::unname($results['data']);
					if($password=$db->get_row(
						"SELECT
							`users`.`id`,`users`.`role_id`,`users`.`first_name`,`users`.`last_name`,`users`.`password`,
							`roles`.`role`
						FROM `users`
						INNER JOIN `roles`
						ON `users`.`role_id`=`roles`.`id`
						WHERE `email`=?",
						$results['email']
					)){
						if(!password_verify($results['password'],$password['password'])){
							$app->log_message(2,'Failed Login','Incorrect password supplied for '.$password['first_name'].' '.$password['last_name'].'.');
							$app->set_message('error',"Email and password do not match");
						}else{
							$_SESSION['user_id']=$password['id'];
							$updates['last_login']=date('Y-m-d H:i:s');
							if(!$password['role']){
								$updates['role_id']=4;
								$app->set_message(1,'Role Reassign','Could not find role so reassigned as "user"',$_SERVER);
							}
							$user->update_user($updates,$password['id']);
							if($_GET['url']){
								header('Location: '.$_GET['url']);
							}elseif(!$_GET['url']){
								header('Location: users');
							}
							exit;
						}
					}else{
						$app->log_message(2,'Failed Login','Login attempt for unlisted user.');
						$app->set_message('error',"Username and password do not match");
					}
				}
			}
		}
	}
}