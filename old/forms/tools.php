<?php class tools extends form{
	public function __construct(){
		parent::__construct('name=tools');
		parent::add_html('<div class="row">
			<div class="col-md-4">
				<div class="card">
					<div class="card-block">
						<h2>Test Email</h2>
						<p>Send a test email to an address</p>');
						parent::add_field(array(
							'label'			=>'Email',
							'name'			=>'test_email_to',
							'placeholder'	=>'Email Address',
							'required'		=>2,
							'type'			=>'email'
						));
						parent::add_html('<p class="actions">');
							parent::add_button(array(
								'class'	=>'btn-success btn-login',
								'name'	=>'test_email',
								'type'	=>'submit',
								'value'	=>'Send'
							));
						parent::add_html('</p>
					</div>
				</div>
			</div>
		</div>');
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
					if($results['test_email']){
						$status=email($results['test_email_to'],'Test Email','This is a test email','<p>This is some content for the test email.');
						if($status['status']){
							$app->set_message('success','Email sent successfully');
						}else{
							$app->set_message('error','Email did not send.<br>'.print_pre($status['data'],1));
						}
					}
				}
			}
		}
	}
}