<?php $app_require=array(
	'form.forgot'
);
require_once('init.php');
if(is_logged_in()){
	header('Location: /users');
	exit;
}elseif($_GET['t']){
	if(!$password_reset_token=$core->get_token($_GET['t'])){
		header('Location: '.$_SERVER['REDIRECT_URL']);
		exit;
	}
}
include_once(CORE.'classes/form.php');
include_once(ROOT.'forms/forgot.php');
$forgot=new forgot();
require('t_header.php');?>
<div class="row">
	<div class="col-md-3 mx-auto">
		<div class="card card-body">
			<h1>Forgot Password</h1>
			<?php $core->get_messages();
			$forgot->get_form(); ?>
		</div>
	</div>
</div>
<?php require('t_footer.php');