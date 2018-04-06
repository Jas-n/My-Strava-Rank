<?php $app_require=array(
	'form.forgot'
);
require_once('init.php');
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