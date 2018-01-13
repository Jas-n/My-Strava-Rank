<?php class list_users extends form{
	public $count=0;
	public function __construct($ids=NULL){
		global $user;
		$users=$user->get_users(0,0,$ids);
		$this->count=$users['count'];
		parent::__construct("name=users&class=form-inline");
		parent::add_html('<table class="table table-hover table-striped table-sm">
			<thead>
				<tr>
					<th>');
						parent::add_field(array(
							'class'	=>'check_all',
							'name'	=>'check_all',
							'type'	=>'checkbox',
							'value'	=>1
						));
					parent::add_html('</th>
					<th>Avatar</th>
					<th>Name</th>
					<th>Role</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>');
				if($users['count']){
					foreach($users['users'] as $i=>$usr){
						parent::add_html('<tr>
							<td>');
								if($usr['id']!=$user->id && $usr['id']!=0){
									parent::add_field(array(
										'class'	=>'check',
										'name'	=>'check[]',
										'type'	=>'checkbox',
										'value'	=>$usr['id']
									));
								}
							parent::add_html('</td>
							<td><img class="avatar" src="'.$user->get_avatar($usr['id'],50).'" height="50"></td>
							<td>'.$usr['title']." ".$usr['first_name']." ".$usr['initials']." ".$usr['last_name'].'</td>
							<td>'.$usr['role'].'</td>
							<td><a class="btn btn-success btn-sm" href="user?id='.$usr['id'].'">View</a> <a class="btn btn-secondary btn-sm" href="mailto:'.$usr['email'].'" title="Email '.$usr['first_name'].' '.$usr['last_name'].'">Email</a></td>
						</tr>');
					}
				}
			parent::add_html('</tbody>
		</table>'.
		pagination($users['count'],0).
		'<p class="text-xs-center">');
			parent::add_button(array(
				'class'	=>'btn-warning btn-sm',
				'name'	=>'reset',
				'type'	=>'submit',
				'value'	=>'Reset Password'
			));
			if($page->permissions['delete']==1){
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
		global $app,$db,$users;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results=parent::unname($results['data']);
				# If delete is clicked
				if($results['delete'] && is_array($results['check'])){
					$users->delete_users($results['check']);
				}
				# If reset is clicked
				elseif($results['reset'] && is_array($results['check'])){
					$users->reset_password($results['check']);
				}
			}
			$this->reload();
		}
	}
}