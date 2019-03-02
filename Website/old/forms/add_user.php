<?php class add_user extends form{
	public function __construct($data=NULL){
		global $addresses,$user;
		parent::__construct("name=new&class=form-horizontal");
		$roles=$user->get_roles(1);
		parent::add_select(
			array(
				'label'		=>'Role',
				'name'		=>'role_id',
				'required'	=>1,
				'value'		=>$data['role_id']
			),
			$roles,
			'Select&hellip;'
		);
		$user->user_form_fields($this,$data);
		parent::add_field(array(
			'checked'	=>$data['can_access'],
			'label'		=>'Can Access',
			'name'		=>'can_access',
			'note'		=>'If checked the user will recieve a password and be able to login to '.SITE_NAME.'.',
			'type'		=>'checkbox',
			'value'		=>1
		));
		parent::add_html('<p class="text-xs-center">');
			parent::add_button(array(
				'class' =>'btn-success',
				'name'  =>'add',
				'type'  =>'submit',
				'value' =>'Add'
			));
		parent::add_html("</p>");
	}
	public function process(){
		if($_POST['form_name']==$this->data['name']){
			global $app,$db,$user;
			$results=parent::process();
			if($results['status']!='error'){
				$results['data']=parent::unname($results['data']);
				$results['files']=parent::unname($results['files']);
				if($user->get_user_by_email($results['data']['email'])){
					$app->set_message('error','Email is already registered');
				}else{
					# Add User
					$newuser=$user->add_user($results['data']);
					$results['data']['user_id']=$newuser['id'];
					# Email User
					if($results['data']['can_access']){
						email(
							$results['data']['email'],
							'New Account',
							'Your account has been created',
							"<p>Your account on <strong>".SITE_NAME."</strong> has been created, you can now <a href='{{{LOGIN URL}}}' title='Login'>login</a> and with the following details:</p>
							<table border='0' cellspacing='10' cellpadding='0' width='560'>
								<tr>
									<td width='20%'><strong>Username:</strong></td>
									<td width='80%'>".$results['data']['email']."</td>
								</tr>
								<tr>
									<td><strong>Password:</strong></td>
									<td>".$newuser['password']."</td>
								</tr>
							</table>"
						);
					}
					$app->set_message('success','Successfully added <a href="/users/user?id='.$results['data']['user_id'].'" title="View '.$results['data']['first_name'].' '.$results['data']['last_name'].'">'.$results['data']['first_name'].' '.$results['data']['last_name'].'</a> to users');
					$app->log_message(3,'New user','Added '.$results['data']['first_name'].' '.$results['data']['last_name'].' to users');
					# Reload form after data
					$this->reload($results['data']);
				}
			}
		}
	}
}