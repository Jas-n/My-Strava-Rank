<?php class users{
	# Add User
	public function add_user($data){
		global $addresses,$app,$db,$user;
		$pass=random_text(PASSWORD_STRENGTH);
		$newpassword=password_hash($pass,PASSWORD_BCRYPT);
		if($data['address']){
			$address_id=$addresses->add_address($data);
		}
		$db->query(
			"INSERT INTO `users` (
				`role_id`,
				`can_access`,
				`title`,
				`first_name`,
				`initials`,
				
				`last_name`,
				`email`,
				`password`,
				`landline`,
				`mobile`,
				
				`registered`,
				`updated`
			) VALUES (?,?,?,?,?,	?,?,?,?,?,	?,?)",
			array(
				$data['role_id'],
				$data['can_access'],
				$data['title'],
				$data['first_name'],
				$data['initials'],
				
				$data['last_name'],
				$data['email'],
				$newpassword,
				$data['landline'],
				$data['mobile'],
				
				DATE_TIME,
				DATE_TIME,
			)
		);
		if($user_id=$db->insert_id()){
			$app->log_message(3,'New User','Added '.$first_name.' '.$last_name.' to users');
			return array(
				'id'		=>$user_id,
				'password'	=>$pass
			);
		}
		return false;
	}
	# Create meta for user
	public function delete_users($users){
		global $app,$db;
		if(!is_array($users)){
			$users=array($users);
		}
		if($datas=$db->query("SELECT `first_name`,`last_name`,`email`,`address_id` FROM `users` WHERE `id` IN(".implode(',',$users).")")){
			foreach($datas as &$data){
				$data=$data['first_name'].' '.$data['last_name'].'<br>'.$data['email'];
				if($data['adddress_id']){
					$db->query("DELETE FROM `addresses` WHERE `id` =?",$data['address_id']);
				}
			}
			$db->query("DELETE FROM `users` WHERE `id` IN(".implode(',',$users).")");
			$db->query("DELETE FROM `notification_users` WHERE `id`IN(".implode(',',$users).")");
			$app->set_message('success','Successfully deleted '.$db->rows_updated().' users',$datas);
			$app->log_message(3,'Deleted users','Deleted '.$db->rows_updated().' users');
		}
	}
	public function get_avatar($user_id=NULL,$size=75){
		global $db;
		$sizes=array(1000,500,300,150,75);
		if(!in_array($size,$sizes)){
			foreach($sizes as $sz){
				if($size<$sz){
					$siz=$sz;
				}
			}
			$size=$siz;
		}
		if($user_id && $user_id!=-1){
			$email=$db->get_value("SELECT `email` FROM `users` WHERE `id`=?",$user_id);
			$url='/uploads/users/'.$user_id.'/avatars/'.$size.'.png';
		}else{
			$email=$this->email;
			$url='/uploads/users/'.$this->id.'/avatars/'.$size.'.png';
		}
		$icons=icon_sizes();
		if(!in_array($size,$icons)){
			foreach($icons as $icon){
				if($size<$icon){
					$isiz=$icon;
				}
			}
			$isize=$isiz;
		}
		if(!is_file(ROOT.$url)){
			$url='http://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?s='.$size.'&d='.(is_file(ROOT.'images/icons/'.$isize.'.png')?urlencode(SERVER_NAME.'/images/icons/'.$isize.'.png'):'mm').'&r=x';
		}
		return $url;
	}
	public function get_roles($formatted=0,$include_visitor=false){
		global $db,$user;
		if($user->role_id!=1){
			$exclude[]='Formation';
		}
		if($include_visitor==false){
			$exclude[]='Visitor';
		}
		if(!$exclude){
			$exclude[]=0;
		}
		$roles=$db->query(
			"SELECT
				`roles`.*
			FROM `roles`
			WHERE
				`role` NOT IN('".implode("','",$exclude)."')
			ORDER BY
				`role` ASC"
		);
		if($formatted){
			foreach($roles as $role){
				$return[$role['id']]=$role['role'];
			}
			return $return;
		}
		return array(
			'count'	=>sizeof($roles),
			'roles'	=>$roles
		);
	}
	public function get_user($user_id,$loaders=NULL){
		global $addresses,$app,$db;
		if($temp=$db->get_row(
			'SELECT
				`users`.*,CONCAT(`users`.`first_name`," ",`users`.`last_name`) as `name`,
				`roles`.`role`
			FROM `users`
			LEFT JOIN `roles`
			ON `users`.`role_id`=`roles`.`id`
			WHERE `users`.`id`=?',
			$user_id
		)){
			if(!$temp['role']){
				$this->update_user(array('role_id'=>4),$user_id);
			}
			$temp['roles'][$temp['role_id']]=$temp['role'];
			if(is_file(ROOT.'uploads/users/'.$user_id.'/cover.html')){
				$temp['cover']=file_get_contents(ROOT.'uploads/users/'.$user_id.'/cover.html');
			}
			if($loaders){
				if(!is_array($loaders)){
					$loaders=array($loaders);
				}
			}
			return $temp;
		}
	}
	public function get_user_by_email($user_email){
		if($user_email==$this->email){
			return (array) $this;
		}else{
			global $db;
			if($temp=$db->get_row(
				'SELECT `id` FROM `users` WHERE `users`.`email`=?',
				$user_email
			)){
				return $this->get_user($temp[0]['id']);
			}
			return false;
		}
	}
	public function get_users_email($userid=NULL){
		if($userid){
			global $db;
			if($temp=$db->get_value("SELECT `email` FROM `users` WHERE `id`=?",$userid)){
				return $temp;
			}else{
				return false;
			}
		}
		return $this->email;
	}
	public function get_users_name($userid=NULL){
		if($userid){
			global $db;
			if($temp=$db->get_value("SELECT CONCAT(`first_name`,' ',`last_name`) FROM `users` WHERE `id`=?",$userid)){
				return $temp;
			}else{
				return false;
			}
		}
		return $this->first_name.' '.$this->last_name;
	}
	public function get_user_count($roles=NULL){
		global $db;
		if($roles!=NULL){
			if(is_array($roles)){
				$roles=implode(',',$roles);
			}
			$where[]="`users`.`role_id` IN(".$roles.")";
		}
		return $db->result_count("FROM `users`
			INNER JOIN `roles`
			ON `users`.`role_id`=`roles`.`id`
			".$where,
			$options
		);
	}
	# 1: Formation, 2: Site Admin, 3: Site Manager, 4: User
	public function get_users($limit=NULL,$roles=NULL,$formatted=0){
		global $db;
		if($roles!=NULL){
			$roles=implode(',',(array) $roles);
			$where[]="`users`.`role_id` IN(".$roles.")";
		}else{
			$where[]="`users`.`id` <> 5";
		}
		if($limit==-1){
			$limit='';
		}elseif(!$limit || !is_numeric($limit)){
			$limit="LIMIT ".($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0).','.ITEMS_PER_PAGE;
		}else{
			$limit='LIMIT '.$limit;
		}
		if($where){
			$where=" WHERE ".implode(' AND ',$where);
		}
		if($user_list=$db->query(
			"SELECT
				`users`.*,CONCAT(`users`.`first_name`,' ',`users`.`last_name`) as `name`,
				`roles`.`role`
			FROM `users`
			LEFT JOIN `roles`
			ON `users`.`role_id`=`roles`.`id`
			$where
			GROUP BY `users`.`id`
			ORDER BY
				`first_name` ASC,
				`last_name` ASC
			".$limit,
			$options
		)){
			unset($user_item);
			if(!$formatted){
				return array(
					'count'	=>$db->get_value(
						"SELECT COUNT(DISTINCT(`users`.`email`)) as `count` FROM `users`
						LEFT JOIN `roles`
						ON `users`.`role_id`=`roles`.`id`
						".$where,
						$options
				),
					'users'	=>$user_list
				);
			}else{
				foreach($user_list as $user_item){
					if($roles==NULL){
						$users[$user_item['role']][$user_item['id']]=$user_item['first_name'].' '.$user_item['last_name'];
					}else{
						$users[$user_item['id']]=$user_item['name'];
					}
				}
				return $users;
			}
		}
	}
	public function reset_password($users){
		global $app,$db,$user;
		if(!is_array($users)){
			$users=array($users);
		}
		foreach($users as $usr){
			$pass=random_text(PASSWORD_STRENGTH);
			$newpassword=password_hash($pass,PASSWORD_BCRYPT);
			$db->query(
				"UPDATE `users`
				SET
					`password`=?,
					`updated`=?
				WHERE `id`=?",
				array(
					$newpassword,
					DATE_TIME,
					$usr
				)
			);
			$email=$user->get_users_email($usr);
			email(
				$email,
				'Password Reset',
				'Your password has been reset',
				"<p>The password for your account on <strong>".SITE_NAME."</strong> has been reset, you can now <a href='{{{LOGIN URL}}}' title='Login'>login</a> with the following details:</p>
				<table border='0' cellspacing='10' cellpadding='0' width='560'>
					<tr>
						<td width='20%'><strong>Username:</strong></td>
						<td width='80%'>".$email."</td>
					</tr>
					<tr>
						<td><strong>Password:</strong></td>
						<td>".$pass."</td>
					</tr>
				</table>"
			);
		}
		$app->set_message('success','Successfully reset '.sizeof($users).' users\' passwords');
		$app->log_message(3,'Reset password(s)','Reset '.sizeof($users).' users\' passwords');
	}
	public static function search($term){
		global $db;
		if($users=$db->query(
			"SELECT
				`users`.`id`, CONCAT(`users`.`title`,' ',`users`.`first_name`,' ',`users`.`initials`,' ',`users`.`last_name`) as `name`,`users`.`email`,
				`roles`.`role`
			FROM `users`
			INNER JOIN `roles`
			ON `users`.`role_id`=`roles`.`id`
			WHERE
				`users`.`first_name` LIKE ? OR
				`users`.`last_name` LIKE ? OR
				`users`.`email` LIKE ?
			ORDER BY
				`role_id` ASC,
				`first_name` ASC,
				`last_name` ASC",
			array(
				"%{$term}%",
				"%{$term}%",
				"%{$term}%"
			)
		)){
			foreach($users as $user){
				$data[]=array(
					'ID'		=>$user['id'],
					'Name'		=>$user['name'],
					'Role'		=>$user['role'],
					'Actions'	=>'<a class="btn btn-primary btn-sm" href="user?id='.$user['id'].'" title="View '.$user['name'].'">View</a> <a class="btn btn-secondary btn-sm" href="mailto:'.$user['email'].'" title="Email '.$user['name'].'">Email</a>'
				);
			}
		}
		return array(
			'name'	=>'Users',
			'slug'	=>'users',
			'count'	=>sizeof($data),
			'data'	=>$data
		);
	}
	public function statistics(){
		global $db;
		$active		=$db->result_count("FROM `users` WHERE `last_login`>?",date('Y-m-d H:i:s',strtotime('-'.MONTH_LENGTH.' days')));
		$live		=$db->result_count("FROM `users` WHERE `last_login`>?",array(date('Y-m-d H:i:s',strtotime('today'))));
		$no_access	=$db->result_count("FROM `users` WHERE `can_access`=0");
		$total		=$db->result_count("FROM `users`");
		$roles=$this->get_roles();
		foreach($roles['roles'] as $role){
			$rls[slug($role['role'])]=$db->result_count("FROM `users` WHERE `role_id`=?",$role['id']);
		}
		return array(
			'active'		=>$active,
			'live'			=>$live,
			'has-access'	=>$total-$no_access,
			'no-access'		=>$no_access,
			'total'			=>$total,
			'roles'			=>$rls
		);
	}
	public function update_avatar($file,$user_id=NULL){
		if($file['size']>0){
			if($user_id===NULL){
				$user_id=$this->id;
			}
			if(!is_dir(ROOT.'uploads/users/'.$user_id.'/avatars/')){
				mkdir(ROOT.'uploads/users/'.$user_id.'/avatars/',0777,1);
				copy(ROOT.'uploads/users/index.php',ROOT.'uploads/users/'.$user_id.'/index.php');
				copy(ROOT.'uploads/users/index.php',ROOT.'uploads/users/'.$user_id.'/avatars/index.php');
			}
			list($width,$height)=getimagesize($file['tmp_name']);
			smart_resize_image($file['tmp_name'],NULL,$width>=75?75:$width,		$width>=75?75:$width,	0,ROOT.'uploads/users/'.$user_id.'/avatars/75.png',		0,'png');
			smart_resize_image($file['tmp_name'],NULL,$width>=150?150:$width,	$width>=150?150:$width,	0,ROOT.'uploads/users/'.$user_id.'/avatars/150.png',	0,'png');
			smart_resize_image($file['tmp_name'],NULL,$width>=300?300:$width,	$width>=300?300:$width,	0,ROOT.'uploads/users/'.$user_id.'/avatars/300.png',	0,'png');
			smart_resize_image($file['tmp_name'],NULL,$width>=500?500:$width,	$width>=500?500:$width,	0,ROOT.'uploads/users/'.$user_id.'/avatars/500.png',	0,'png');
			smart_resize_image($file['tmp_name'],NULL,$width>=1000?1000:$width,	$width>=1000?1000:$width,0,ROOT.'uploads/users/'.$user_id.'/avatars/1000.png',	0,'png');
		}
	}
	public function update_user(array $columns_values,$user_id=NULL){
		global $addresses,$app,$db,$page;
		if($user_id){
			$user=$user_id;
		}else{
			$user=$this->id;
		}
		if($columns_values['can_access']){
			$columns_values['can_access']=1;
		}else{
			$columns_values['can_access']=0;
		}
		if($columns_values['cover']){
			file_put_contents(ROOT.'uploads/users/'.$user.'/cover.html',$columns_values['cover']);
		}elseif(array_key_exists('cover',$columns_values) && is_file(ROOT.'uploads/users/'.$user.'/cover.html')){
			unlink(ROOT.'uploads/users/'.$user.'/cover.html');
		}
		if($columns_values['reset_password'] && $columns_values['can_access']){
			$this->reset_password($user);
		}
		if($addresses && $columns_values['address']){
			if($columns_values['address']['id']){
				$addresses->update_address(
					$columns_values['address']['id'],
					$columns_values['address']['location_id'],
					$columns_values['address']['line_1'],
					$columns_values['address']['building'],
					$columns_values['address']['line_2'],
					$columns_values['address']['line_3'],
					$columns_values['address']['postcode']
				);
			}else{
				$columns_values['address_id']=$addresses->add_address($columns_values);
			}
		}
		$cols=array_keys($db->get_columns('users'));
		# Construct update SQL
		foreach($columns_values as $column=>$value){
			if(in_array($column,$cols)){
				$sets[]="`{$column}`=?";
				$options[]=$value;
			}
		}
		$options[]=$user;
		$result=$db->query(
			"UPDATE `users` SET ".implode(',',$sets)." WHERE `id`=?",
			$options
		);
		if(!$user_id){
			$this->__construct($this->id);
		}
		if($page->title!='Login'){
			$app->log_message(3,'Updated User','Updated '.$first_name.' '.$last_name);
		}
		return $result;
	}
	# Helper methods
		# Fields for new password
		public function new_password_fields($form_object){
			$form_object->add_field('type=password&name=existing_password&placeholder=Existing Password&label=Existing Password');
			$form_object->add_field('type=password&name=new_password_1&placeholder=New Password&label=New Password');
			$form_object->add_field('type=password&name=new_password_2&placeholder=Repeat New Password&label=Repeat New Password');
		}
		# Fields for user form
		public function user_form_fields($form,$user=NULL){
			$form->add_select(
				array(
					'label'		=>'Title',
					'name'		=>'title',
					'required'	=>1,
					'value'		=>$user['title']
				),
				json_decode(TITLES,1),
				'Select&hellip;'
			);
			$form->add_fields(array(
				array(
					'label'			=>'First Name',
					'name'			=>'first_name',
					'placeholder'	=>'First Name',
					'required'		=>1,
					'type'			=>'text',
					'value'			=>$user['first_name']
				),
				array(
					'label'			=>'Initials',
					'name'			=>'initials',
					'placeholder'	=>'Initials',
					'type'			=>'text',
					'value'			=>$user['initials']
				),
				array(
					'label'			=>'Last Name',
					'name'			=>'last_name',
					'placeholder'	=>'Last Name',
					'required'		=>1,
					'type'			=>'text',
					'value'			=>$user['last_name']
				),
				array(
					'label'			=>'Email',
					'name'			=>'email',
					'placeholder'	=>'Email Address',
					'required'		=>1,
					'type'			=>'email',
					'value'			=>$user['email']
				),
				array(
					'label'			=>'Landline',
					'name'			=>'landline',
					'placeholder'	=>'Landline',
					'type'			=>'tel',
					'value'			=>$user['landline']
				),
				array(
					'label'			=>'Mobile',
					'name'			=>'mobile',
					'placeholder'	=>'Mobile',
					'type'			=>'tel',
					'value'			=>$user['mobile']
				)
			));
			$form->add_field("type=hidden&name=user_id&value=".$user['id']);
		}
}