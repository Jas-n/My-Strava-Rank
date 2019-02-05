<?php class item_user extends form{
	public function __construct($user_id=NULL){
		global $user,$page;
		if($user_id!==NULL){
			$this->user_id=$user_id;
			$user_=$user->get_user($user_id);
		}else{
			$this->user_id=$user->id;
			$user_=$user->get_user($user->id);
		}
		parent::__construct("name=user&class=form-horizontal");
		parent::add_html('<div class="row">
			<div class="col-md-6">');
				$roles=$user->get_roles(1);
				parent::add_select(
					array(
						'label'		=>'Default Role',
						'name'		=>'role_id',
						'required'	=>1,
						'value'		=>$user_['role_id']
					),
					$roles,
					'Select&hellip;'
				);
				$user->user_form_fields($this,$user_);
				parent::add_field(array(
					'checked'=>$user_['can_access'],
					'label'	=>'Can Login',
					'name'	=>'can_access',
					'type'	=>'checkbox',
					'value'	=>1
				));
				parent::add_field(array(
					"label"	=>"Send new Password",
					"name"	=>"reset_password",
					"type"	=>"checkbox",
					"value"	=>1
				));
			parent::add_html('</div>
			<div class="col-md-6">');
			parent::add_html('</div>
		</div>');
		parent::add_html('<p class="text-xs-center">');
			parent::add_button('name=update&type=submit&value=Update&class=btn-success');
			if($page->permissions['delete']){
				parent::add_button(array(
					'class'	=>'btn-danger delete',
					'name'	=>'delete',
					'type'	=>'submit',
					'value'	=>'Delete'
				));
			}
		parent::add_html('</p>');
	}
	public function process(){
		global $app,$db,$user;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results['data']=parent::unname($results['data']);
				$results['files']=parent::unname($results['files']);
				if($results['data']['update']){
					if(($results['data']['new_password_1'] && !$results['data']['new_password_2'])
					|| (!$results['data']['new_password_1'] && $results['data']['new_password_2'])
					|| ($results['data']['new_password_1'] != $results['data']['new_password_2'])){
						$app->set_message('error',"New Passwords do not match.");
					}elseif($results['data']['new_password_1'] && password_verify($results['data']['new_password_1'],$user->password)){
						$app->set_message('error',"'Existing Password' does not match our records.");
					}elseif($db->query("SELECT `id` FROM `users` WHERE `email`=? AND `id` <> ?",array($results['data']['email'],$results['data']['user_id']))){
						$app->set_message('error',"Email is already assigned to another user.");
					}else{
						# Unset the values we won't store in the DB
						unset(
							$results['data']['form_name'],			# Form Name
							$results['data']['update'],				# Update button
							$results['data']['existing_password'],	# Existing password
							$results['data']['new_password_1'],		# New password
							$results['data']['new_password_2']		# New Password confirmation
						);
						# Update the user with the verified results
						$user->update_user($results['data'],$results['data']['user_id']);
						$user->update_avatar($results['files']['avatar'],$results['data']['user_id']);
						# Reconstruct form with new values
						$this->reload($_GET['id']);
						$app->set_message('success',"Profile Updated.");
					}
				}
			}
		}
	}
}