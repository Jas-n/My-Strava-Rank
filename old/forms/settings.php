<?php class settings extends form{
	public function __construct(){
		global $db,$user;
		$temps=$db->query("SELECT `name`,`value` FROM `settings`");
		foreach($temps as $temp){
			if(in_array($temp['name'],array('titles'))){
				$temp['value']=json_decode($temp['value'],1);
			}
			$settings[$temp['name']]=$temp['value'];
		}
		parent::__construct('name=settings&class=form-horizontal');
		parent::add_html('<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="tab" href="#api" role="tab">API</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#contact">Contact</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#site">Site</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#themes">Theme</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#user">User</a>
			</li>');
		parent::add_html('</ul>
		<div class="tab-content">
			<!-- API -->
			<div role="tabpanel" class="tab-pane active" id="api">
				<h2>'.SITE_NAME.'</h2>');
				parent::add_field(array(
					'label'			=>'API Key',
					'type'			=>'static',
					'value'			=>md5(INSTALL_DATE)
				));
				parent::add_html('<h2>Google reCAPTCHA</h2>
				<p><em class="text-muted">Used to help prevent spam submissions on supported forms. You must <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank" title="Google reCAPTCHA">create a configuration</a> before this is functionality is enabled.</em></p>');
				parent::add_fields(array(
					array(
						"label"			=>"Site Key",
						"name"			=>"recaptcha_site_key",
						"placeholder"	=>"Site Key",
						"type"			=>"text",
						"value"			=>$settings['recaptcha_site_key']
					),
					array(
						"label"			=>"Secret Key",
						"name"			=>"recaptcha_secret_key",
						"placeholder"	=>"Secret Key",
						"type"			=>"text",
						"value"			=>$settings['recaptcha_secret_key']
					)
				));
			parent::add_html('</div>
			<!-- Contact -->
			<div role="tabpanel" class="tab-pane" id="contact">
				<h2>General</h2>');
				parent::add_fields(array(
					array(
						'label'			=>'Company Name',
						'name'			=>'company_name',
						'placeholder'	=>'Company Name',
						'required'		=>1,
						'type'			=>'text',
						'value'			=>$settings['company_name']
					),
					array(
						'label'			=>'Company Address',
						'name'			=>'company_address',
						'placeholder'	=>'Company Address',
						'required'		=>1,
						'type'			=>'textarea',
						'value'			=>$settings['company_address']
					),
					array(
						'label'			=>'Telephone',
						'name'			=>'company_tel',
						'placeholder'	=>'Telephone',
						'type'			=>'text',
						'value'			=>$settings['company_tel']
					),
					array(
						'label'			=>'Site Email',
						'name'			=>'site_email',
						'note'			=>'The site\'s \'From\' email address',
						'required'		=>1,
						'placeholder'	=>'Site Email',
						'type'			=>'email',
						'value'			=>$settings['site_email']
					)
				));
				parent::add_html('<h2>Social Media</h2>');
				parent::add_fields(array(
					array(
						'label'			=>'Facebook URL',
						'name'			=>'facebook_url',
						'placeholder'	=>'https://www.facebook.com/facebook',
						'type'			=>'text',
						'value'			=>$settings['facebook_url']
					),
					array(
						'label'			=>'LinkedIn URL',
						'name'			=>'linkedin_url',
						'placeholder'	=>'https://www.linkedin.com/company/linkedin',
						'type'			=>'text',
						'value'			=>$settings['linkedin_url']
					),
					array(
						'label'			=>'Twitter Username',
						'name'			=>'twitter_username',
						'placeholder'	=>'twitter',
						'prefield'		=>'@',
						'type'			=>'text',
						'value'			=>$settings['twitter_username']
					),
					array(
						'label'			=>'YouTube Profile',
						'name'			=>'youtube_url',
						'placeholder'	=>'https://www.youtube.com/user/YouTube',
						'type'			=>'text',
						'value'			=>$settings['youtube_url']
					)
				));
			parent::add_html('</div>
			<!-- Site -->
			<div role="tabpanel" class="tab-pane" id="site">');
				parent::add_fields(array(
					array(
						'label'	=>'Install Date',
						'type'	=>'static',
						'value'	=>sql_datetime($settings['install_date'])
					),
					array(
						'label'			=>'Site Name',
						'name'			=>'site_name',
						'placeholder'	=>'Site Name',
						'required'		=>1,
						'type'			=>'text',
						'value'			=>$settings['site_name']
					),
					array(
						'label'			=>'Date Format',
						'name'			=>'date_format',
						'note'			=>'Refer to <a href="http://php.net/manual/en/function.date.php" title="PHP Date">PHP Date</a> for supported formats',
						'placeholder'	=>'d m Y',
						'required'		=>1,
						'type'			=>'text',
						'value'			=>$settings['date_format']
					),
					array(
						'label'			=>'Time Format',
						'name'			=>'time_format',
						'note'			=>'Refer to <a href="http://php.net/manual/en/function.date.php" title="PHP Date">PHP Date</a> for supported formats',
						'placeholder'	=>'H:i:s',
						'required'		=>1,
						'type'			=>'text',
						'value'			=>$settings['time_format']
					),
					array(
						'label'			=>'Items per page',
						'name'			=>'items_per_page',
						'note'			=>'Number of items to display per page',
						'placeholder'	=>'20',
						'required'		=>1,
						'type'			=>'number',
						'value'			=>$settings['items_per_page']
					),
					array(
						'label'			=>'Log Age',
						'name'			=>'logs_age',
						'note'			=>'Number of Days to keep <a href="logs" title="Logs">logs</a>',
						'placeholder'	=>'7',
						'required'		=>1,
						'type'			=>'number',
						'value'			=>$settings['logs_age']
					)
				));
				if(is_file(ROOT.'images/logos/75.png')){
					parent::add_field(array(
						'label'	=>'Existing Logo',
						'type'	=>'static',
						'value'	=>'<img src="/images/logos/75.png" height="75">'
					));
				}
				parent::add_fields(array(
					array(
						'accept'=>'jpeg,jpg,png',
						'label'	=>'New Logo',
						'name'	=>'logo',
						'note'	=>'Logos to be used throughout the site. Recommended size 1000px wide.',
						'type'	=>'file'
					)
				));
				if(is_file(ROOT.'images/icons/120.png')){
					parent::add_field(array(
						'label'	=>'Existing Icon',
						'type'	=>'static',
						'value'	=>'<img src="/images/icons/72.png" height="72">'
					));
				}
				parent::add_fields(array(
					array(
						'accept'=>'jpeg,jpg,png',
						'label'	=>'New Icon',
						'name'	=>'icon',
						'note'	=>'Square icon used for site and device icons. Should be square and at least 228px<sup>2</sup>.',
						'type'	=>'file'
					)
				));
				$temps=$user->get_users(100,2);
				if(!$temps['count']){
					$temps=$user->get_users(100,3);
					if(!$temps['count']){
						$temps=$user->get_users(100,1);
					}
				}
				if($temps){
					foreach($temps['users'] as $temp){
						$users[$temp['role']][$temp['id']]=$temp['first_name'].' '.$temp['last_name'];
					}
					parent::add_select(
						array(
							'label'		=>'Main Admins',
							'multiple'	=>1,
							'name'		=>'main_admins[]',
							'required'	=>2,
							'value'		=>$settings['main_admins']
						),
						$users
					);
				}
				parent::add_fields(array(
					array(
						'label'			=>'Month Length',
						'name'			=>'month_length',
						'note'			=>'Used for <a href="statistics" title="Statistics">statistics</a>',
						'placeholder'	=>30,
						'required'		=>1,
						'type'			=>'number',
						'value'			=>$settings['month_length']
					),
					array(
						'label'			=>'Site URL',
						'name'			=>'server_name',
						'note'			=>'Only change when moving site to new domain',
						'placeholder'	=>SERVER_NAME,
						'required'		=>1,
						'type'			=>'url',
						'value'			=>$settings['server_name']
					),
				));
			parent::add_html('</div>
			<!-- Theme -->
			<div role="tabpanel" class="tab-pane" id="themes">');
				parent::add_fields(array(
					array(
						'label'	=>'Colour',
						'name'	=>'colour',
						'type'	=>'color',
						'value'	=>$settings['colour']
					)
				));
			parent::add_html('</div>
			<!-- User -->
			<div role="tabpanel" class="tab-pane" id="user">');
				if($other_roles){
					ksort($other_roles);
					parent::add_html('<div class="row">
						<div class="col-sm-'.$this->label_width.'">Default Additional Roles</div>
						<div class="col-sm-'.$this->value_width.'">');
							foreach($other_roles as $module=>$module_roles){
								parent::add_html('<strong>'.$module.'</strong>
								<div class="row">');
									asort($module_roles);
									foreach($module_roles as $id=>$role){
										parent::add_field(array(
											'checked'	=>in_array($id,array_keys($user_['roles'])),
											'class'		=>'m-b-0',
											'name'		=>'default_other_roles[]',
											'postfield'	=>$role,
											'type'		=>'checkbox',
											'value'		=>$id,
											'wrapclass'	=>'m-b-0'
										));
									}
								parent::add_html('</div>');
							}
						parent::add_html('</div>
					</div>');
				}
				parent::add_fields(array(
					array(
						'checked'		=>$settings['registration_allowed'],
						'label'			=>'Registration Allowed?',
						'name'			=>'registration_allowed',
						'type'			=>'checkbox',
						'value'			=>1
					),
					array(
						'label'			=>'Password Length',
						'name'			=>'password_strength',
						'required'		=>1,
						'placeholder'	=>'Password Length',
						'type'			=>'number',
						'value'			=>$settings['password_strength']
					),
					array(
						'label'		=>'Social Titles',
						'name'		=>'titles',
						'note'		=>'One per line',
						'required'	=>1,
						'type'		=>'textarea',
						'value'		=>implode("\r\n",$settings['titles'])
					)
				));
			parent::add_html('</div>
		</div>
		<p class="m-t-075 text-xs-center">');
			parent::add_button(array(
				'class'	=>'btn-success',
				'name'	=>'save',
				'type'	=>'submit',
				'value'	=>'Save'
			));
		parent::add_html('</p>');
	}
	public function process(){
		global $app,$db,$user;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results['data']=$this->unname($results['data']);
				if($results['data']['recaptcha_site_key'] && !$results['data']['recaptcha_secret_key']){
					$app->set_message('error','A site key was supplied without a secret key');
				}elseif($results['data']['recaptcha_secret_key'] && !$results['data']['recaptcha_site_key']){
					$app->set_message('error','A secret key was supplied without a site key');
				}else{
					if($this->uploaded_files){
						$results['files']=$this->unname($results['files']);
						if($results['files']['logo']['size']){
							$file=$results['files']['logo'];
							rrmdir(ROOT.'images/logos');
							mkdir(ROOT.'images/logos',0777,1);
							copy(ROOT.'images/index.php',ROOT.'images/logos/index.php');
							list($width,$height)=getimagesize($file['tmp_name']);
							smart_resize_image($file['tmp_name'],NULL,0,$height>=75?75:$height,		1,ROOT.'images/logos/75.png',	0,'png');
							smart_resize_image($file['tmp_name'],NULL,0,$height>=150?150:$height,	1,ROOT.'images/logos/150.png',	0,'png');
							smart_resize_image($file['tmp_name'],NULL,0,$height>=300?300:$height,	1,ROOT.'images/logos/300.png',	0,'png');
							smart_resize_image($file['tmp_name'],NULL,0,$height>=500?500:$height,	1,ROOT.'images/logos/500.png',	0,'png');
							smart_resize_image($file['tmp_name'],NULL,0,$height>=1000?1000:$height,	1,ROOT.'images/logos/1000.png',	0,'png');
						}
						if($results['files']['icon'] && $results['files']['icon']['size']>0){
							generate_icons($results['files']['icon']['tmp_name']);
						}
					}
					if(!$results['data']['main_admins']){
						$results['data']['main_admins'][]=1;
					}
					foreach($results['data'] as $name=>&$value){
						# JSON Encode
						if(is_array($value)){
							$value=json_encode($value);
						}
						if($name=='titles'){
							$titles=explode("\r\n",$value);
							$value=json_encode(array_combine($titles,$titles));
						}
					}
					foreach(array('debug','registration_allowed','show_contact_map','user_uploads') as $checkbox){
						if(!$results['data'][$checkbox]){
							$results['data'][$checkbox]=0;
						}
					}
					unset(
						$results['data']['form_name'],
						$results['data']['save']
					);
					foreach($results['data'] as $setting=>$value){
						$db->query("UPDATE `settings` SET `value`=? WHERE `name`=?",array($value,$setting));
					}
					$app->set_message('success','Settings Updated');
					$app->log_message(3,'Settings Updated','Updated the settings');
					$this->reload();
				}
			}
		}
	}
}