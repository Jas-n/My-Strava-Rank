<?php # namespace Formation\Glowt\Forms\;
use Formation\Core\Form;
class forgot extends form{
	public function __construct(){
		global $password_reset_token;
		parent::__construct(array(
			'action'=>'do-forgot',
			'name'=>__CLASS__
		));
		parent::add_field(array(
			'label'			=>'Registered Email',
			'name'			=>'email',
			'placeholder'	=>'Email Address',
			'required'		=>1,
			'type'			=>'email',
			'value'			=>$data['email']
		));
		if($password_reset_token){
			parent::add_fields(array(
				array(
					'label'			=>'New Password',
					'name'			=>'password_1',
					'placeholder'	=>'New Password',
					'required'		=>1,
					'type'			=>'password'
				),
				array(
					'label'			=>'Repeat Password',
					'name'			=>'password_2',
					'placeholder'	=>'Repeat Password',
					'required'		=>1,
					'type'			=>'password'
				),
				array(
					'name'	=>'t',
					'type'	=>'hidden',
					'value'	=>$_GET['t']
				)
			));
		}
		parent::add_html('<div class="d-flex justify-content-around">
			<div>');
				parent::add_button(array(
					'class'	=>'btn-primary',
					'name'	=>'reset',
					'type'	=>'submit',
					'value'	=>'Reset'
				));
				parent::add_html('<a class="btn btn-sm btn-white js-load login">Login</a>
			</div>
		</div>');
	}
	/*public function process(){
		global $core,$app,$db,$password_reset_token,$users;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			$results=$this->unname($results['data']);
			if($results['t']){
				# Password reset is valid, now store it
				if(!$results['password_1'] || !$results['password_2']){
					$core->set_message('error','Please complete all password fields to change your password.');
					$this->redirect(false,$results);
				}elseif($results['password_1'] != $results['password_2']){
					$core->set_message('error',"New Passwords do not match.");
					$this->redirect(false,$results);
				}elseif(strlen($results['password_1'])<PASSWORD_STRENGTH){
					$core->set_message('error',"New password must be at least ".PASSWORD_STRENGTH." characters long.");
					$this->redirect(false,$results);
				}else{
					$password=password_hash($results['password_1'],PASSWORD_BCRYPT);
					$db->query(
						"UPDATE `users`
						SET
							`password`=?,
							`updated`=?
						WHERE `id`=?",
						array(
							$password,
							DATE_TIME,
							$password_reset_token['user_id']
						)
					);
					$app->expire_token($_GET['t']);
					$core->log_message(3,'Forgot Password','Password successfully reset for \''.$usr[0]['first_name'].' '.$usr[0]['last_name'].'\'');
					$core->set_message('success','You can now log in with your new password.');
					$_SESSION['user_id']=$password_reset_token['user_id'];
					header('Location: /users/');
					exit;
				}
			}elseif($usr=$db->get_row(
				"SELECT `id`,`first_name`,`last_name`,`email`
				FROM `users`
				WHERE `email`=? AND `id` <> ? AND `can_access`=1",
				array(
					$results['email'],
					0
				)
			)){
				$users->reset_password($usr['id']);
				$core->log_message(3,'Forgot Password','Forgot Password requested and sent by <strong>'.$usr['first_name'].' '.$usr['last_name'].'</strong>');
				$core->set_message('success','Instructions to reset your password have ben sent to your email address.');
				header('Location: login');
				exit;
			}else{
				$core->log_message(2,'Forgot Password','Password request for unlisted user using email <strong>'.$results['email'].'</strong>.');
				$core->set_message('error',"There are no users matching your details.");
				$this->redirect(false,$results);
			}
		}
	}*/
}