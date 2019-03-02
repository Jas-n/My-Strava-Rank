<?php $app_require=array(
	'form.login'
);
require_once('init.php');
include_once(CORE.'classes/form.php');
include_once(ROOT.'forms/login.php');
$login=new login();
require('t_header.php');?>
<div class="row">
	<div class="col-md-3 mx-auto">
		<div class="card card-body">
			<h1>Login</h1>
			<?php $core->get_messages();
			$login->get_form(); ?>
		</div>
	</div>
</div>
<?php require('t_footer.php');