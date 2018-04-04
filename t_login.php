<?php $app_require=array(
	'form.login'
);
require_once('init.php');
include_once(CORE.'classes/form.php');
include_once(ROOT.'forms/login.php');
$login=new login();
require('t_header.php');?>
<div class="card card-body">
	<h1>Login</h1>
	<?php $core->get_messages();
	$login->get_form(); ?>
</div>
<?php require('t_footer.php');