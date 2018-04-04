<?php use Formation\Core\Form;
class login extends form{
	public function __construct($data=NULL){
		parent::__construct('name='.__CLASS__);
		parent::add_field(array(
			'label'			=>'Email',
			'name'			=>'email',
			'placeholder'	=>'Email Address',
			'required'		=>1,
			'type'			=>'email',
			'value'			=>$data['email']
		));
		parent::add_field(array(
			'label'			=>'Password',
			'name'			=>'password',
			'placeholder'	=>'Password',
			'required'		=>1,
			'type'			=>'password'
		));
		parent::add_html('<div class="text-center">');
			parent::add_button(array(
				'class'	=>'btn-primary btn-login',
				'name'	=>'login',
				'type'	=>'submit',
				'value'	=>'Login'
			));
			parent::add_html('<a class="btn btn-sm btn-white btn-forgot_password" href="./forgot">Reset Password</a>
		</div>');
	}
}