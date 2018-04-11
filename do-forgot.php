<?php require_once('init.php');
if($_POST['forgot_t']){
	# Password reset is valid, now store it
	if(!$_POST['forgot_password_1'] || !$_POST['forgot_password_2']){
		$core->set_message('error','Please complete all password fields to change your password.');
		header('Location: forgot');
		exit;
	}elseif($_POST['forgot_password_1'] != $_POST['forgot_password_2']){
		$core->set_message('error',"New Passwords do not match.");
		header('Location: forgot');
		exit;
	}elseif(strlen($_POST['forgot_password_1'])<PASSWORD_STRENGTH){
		$core->set_message('error',"New password must be at least ".PASSWORD_STRENGTH." characters long.");
		header('Location: forgot');
		exit;
	}else{
		$password=password_hash($_POST['forgot_password_1'],PASSWORD_BCRYPT);
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
		$_POST['forgot_email'],
		0
	)
)){
	$user->reset_password($usr['id']);
	$core->log_message(3,'Forgot Password','Forgot Password requested and sent by <strong>'.$usr['first_name'].' '.$usr['last_name'].'</strong>');
	$core->set_message('success','Instructions to reset your password have ben sent to your email address.');
	header('Location: login');
	exit;
}else{
	$core->log_message(2,'Forgot Password','Password request for unlisted user using email <strong>'.$_POST['forgot_email'].'</strong>.');
	$core->set_message('error',"There are no users matching your details.");
	header('Location: forgot');
	exit;
}