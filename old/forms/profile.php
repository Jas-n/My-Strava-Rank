<?php class profile extends form{
	public function __construct(){
		global $addresses,$user;
		parent::__construct("name=profile&class=form-horizontal");
		$this->set_label_width(3);
		$user->user_form_fields($this,(array) $user);
		$user->new_password_fields($this);
		parent::add_html('<p class="text-xs-center">');
		parent::add_button('name=update&showlabel=no&type=submit&value=Update&class=btn-success');
		parent::add_html('</p>');
	}
	public function process(){
		global $app,$db,$user;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results['data']=parent::unname($results['data']);
				if($this->uploaded_files){
					$results['files']=parent::unname($results['files']);
					$user->update_avatar($results['files']['avatar']);
				}
				if($results['data']['new_password_1'] || $results['data']['new_password_2'] || $results['data']['existing_password']){
					if(!$results['data']['new_password_1'] || !$results['data']['new_password_2'] || !$results['data']['existing_password']){
						$app->set_message('error','Please complete all password fields to change your password.');
					}elseif($results['data']['new_password_1'] != $results['data']['new_password_2']){
						$app->set_message('error',"New Passwords do not match.");
					}elseif(strlen($results['data']['new_password_1'])<PASSWORD_STRENGTH){
						$app->set_message('error',"New password must be at least ".PASSWORD_STRENGTH." characters long.");
					}elseif($results['data']['new_password_1'] && !password_verify($results['data']['existing_password'],$user->password)){
						$app->set_message('error',"'Existing Password' does not match our records");
					}else{
						$results['data']['password']=password_hash($results['data']['new_password_1'],PASSWORD_BCRYPT);
					}
				}
				if($db->query("SELECT `id` FROM `users` WHERE `email`=? AND `id` <> ?",array($results['data']['email'],$user->id))){
					$err[]="Email is already assigned to another user.";
				}
				# Update the user with the verified results
				$user->update_user($results['data']);
				# Reconstruct form with new values
				$this->reload();
				$app->set_message('success',"Profile Updated.");
			}
		}
	}
}