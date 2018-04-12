<?php echo __LINE__.'<br>';
require_once('init.php');
line();
if(!$_POST || $_POST['form_name']!='login'){
	header('Location: /login');
	exit;
}
line();
if($password=$db->get_row(
	"SELECT
		`id`,`role_id`,`can_access`,`first_name`,`last_name`,`password`
	FROM `users`
	WHERE `email`=?",
	$_POST['login_login']
)){
	line();
	if(!$password['can_access']){
		line();
		$_SESSION['failed_logins']['count']++;
		$_SESSION['failed_logins']['last_try']=time();
		$core->log_message(2,'Failed Login','User does not have access <strong>'.$password['first_name'].' '.$password['last_name'].'</strong>.');
		$core->set_message('error',"You do not have permission to log in.");
		header('Location: /login');
		exit;
	}elseif(!password_verify($_POST['login_password'],$password['password'])){
		line();
		$_SESSION['failed_logins']['count']++;
		$_SESSION['failed_logins']['last_try']=time();
		$core->log_message(2,'Failed Login','Incorrect password supplied for <strong>'.$password['first_name'].' '.$password['last_name'].'</strong>.');
		$core->set_message('error',"Email and password do not match");
		header('Location: /login');
		exit;
	}elseif($password['role_id']==5 && (!$app->get_module_id('clients') || !defined('CLIENTS_ACCESS') || !$db->get_value("SELECT `active` FROM `modules` WHERE `id`=?",$app->get_module_id('clients')))){
		line();
		$_SESSION['failed_logins']['count']++;
		$_SESSION['failed_logins']['last_try']=time();
		$core->log_message(2,'Failed Login','Clients module is no longer enabled.');
		$core->set_message('error',"You do not have permission to log in.");
		header('Location: /login');
		exit;
	}else{
		line();
		$_SESSION['user_id']=$password['id'];
		$_SESSION['roles'][$password['role_id']]=$password['role'];
		$updates['last_login']=DATE_TIME;
		if(!$password['role']){
			line();
			$core->log_message(1,'Role Reassign','Could not find role so reassigned as "user"');
			$db->query(
				"UPDATE `users`
				SET `role_id`=6
				WHERE `id`=?",
				$password['id']
			);
		}
		line();
		$db->query(
			"UPDATE `users`
			SET `last_login`=?
			WHERE `id`=?",
			array(
				DATE_TIME,
				$password['id']
			)
		);
		if($_GET['url']){
			line();
			header('Location: '.$_GET['url']);
		}elseif(!$_GET['url']){
			line();
			header('Location: users');
		}
		exit;
	}
}
$core->log_message(2,'Failed Login','Login attempt for unlisted user using email <strong>'.$results['email'].'</strong>.');
$core->set_message('error',"Username and password do not match");
header('Location: /login');