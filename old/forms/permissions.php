<?php class permissions extends form{
	public function __construct($module_id=NULL){
		global $app,$page,$user;
		$datas=$page->get_pages(-1);
		if($datas['count']){
			foreach($datas['pages'] as $data){
				$pages[$data['module']][]=$data;
			}
		}
		parent::__construct("name=permissions&class=form-inline");
		$temp=$user->get_roles(0,1);
		foreach($temp['roles'] as $i=>$role){
			if($role['extends']!=0){
				unset($temp['roles'][$i]);
			}
		}
		$temp['roles']=array_values($temp['roles']);
		parent::add_html('<ul class="nav nav-tabs" role="tablist">');
			foreach($temp['roles'] as $i=>$role){
				parent::add_html('<li class="nav-item">
					<a class="nav-link'.($i==0?' active':'').'"  data-toggle="tab" href="#role_'.$role['id'].'" role="tab">'.$role['role'].'</a>
				</li>');
			}
		parent::add_html('</ul>
		<div class="tab-content">');
			foreach($temp['roles'] as $i=>$role){
				parent::add_html('<div role="tabpanel" class="tab-pane '.($i==0?' active':'').'" id="role_'.$role['id'].'">');
					if($pages){
						foreach($pages as $module_id=>$mod_pages){
							if($module_id==0){
								parent::add_html('<h2>Core Pages</h2>');
							}else{
								parent::add_html('<h2>'.$app->all_modules[$module_id]['name'].' Pages</h2>');
							}
							parent::add_field(array(
								'name'		=>'roles[]',
								'type'		=>'hidden',
								'value'		=>$role['id']
							));
							parent::add_html('<table class="table table-hover table-sm table-striped">
								<thead>
									<tr>
										<th rowspan="2">Page</th>
										<th rowspan="2">Directory</th>
										<th colspan="'.($user->role_id==1?5:4).'">Access</th>
									</tr>
									<tr>');
										if($user->role_id==1){
											parent::add_html('<th width="5%">Fixed</th>');
										}
										parent::add_html('<th width="5%">View</th>
										<th width="5%">Add</th>
										<th width="5%">Edit</th>
										<th width="5%">Delete</th>
									</tr>
								</thead>
								<tbody>');
									foreach($mod_pages as $pg){
										parent::add_html('<tr>
											<th>'.$pg['title'].'</th>
											<td>'.$pg['path'].'</td>');
											if($user->role_id==1){
												parent::add_html('<td>');
													if($i==0){
														parent::add_field(array(
															'checked'	=>$pg['fixed_access'],
															'name'		=>'fixed['.$pg['id'].']',
															'type'		=>'checkbox',
															'value'		=>1
														));
													}else{
														if($pg['fixed_access']){
															parent::add_html('<span class="fa fa-check"></span>');
														}
													}
												parent::add_html('</td>');
											}
											parent::add_html('<td>');
												if($pg['fixed_access'] && $user->role_id!=1){
													if($pg['permissions'][$role['id']]['view']){
														parent::add_html('<span class="fa fa-check"></span>');
													}
													parent::add_field(array(
														'name'		=>'page['.$pg['id'].']['.$role['id'].'][view]',
														'type'		=>'hidden',
														'value'		=>$pg['permissions'][$role['id']]['view']
													));
												}else{
													parent::add_field(array(
														'checked'	=>$pg['permissions'][$role['id']]['view'],
														'name'		=>'page['.$pg['id'].']['.$role['id'].'][view]',
														'type'		=>'checkbox',
														'value'		=>1
													));
												}
											parent::add_html('</td>
											<td>');
												if($pg['fixed_access'] && $user->role_id!=1){
													if($pg['permissions'][$role['id']]['add']){
														parent::add_html('<span class="fa fa-check"></span>');
													}
													parent::add_field(array(
														'name'		=>'page['.$pg['id'].']['.$role['id'].'][add]',
														'type'		=>'hidden',
														'value'		=>$pg['permissions'][$role['id']]['add']
													));
												}else{
													parent::add_field(array(
														'checked'	=>$pg['permissions'][$role['id']]['add'],
														'name'		=>'page['.$pg['id'].']['.$role['id'].'][add]',
														'type'		=>'checkbox',
														'value'		=>1
													));
												}
											parent::add_html('</td>
											<td>');
												if($pg['fixed_access'] && $user->role_id!=1){
													if($pg['permissions'][$role['id']]['edit']){
														parent::add_html('<span class="fa fa-check"></span>');
													}
													parent::add_field(array(
														'name'		=>'page['.$pg['id'].']['.$role['id'].'][edit]',
														'type'		=>'hidden',
														'value'		=>$pg['permissions'][$role['id']]['edit']
													));
												}else{
													parent::add_field(array(
														'checked'	=>$pg['permissions'][$role['id']]['edit'],
														'name'		=>'page['.$pg['id'].']['.$role['id'].'][edit]',
														'type'		=>'checkbox',
														'value'		=>1
													));
												}
											parent::add_html('</td>
											<td>');
												if($pg['fixed_access'] && $user->role_id!=1){
													if($pg['permissions'][$role['id']]['delete']){
														parent::add_html('<span class="fa fa-check"></span>');
													}
													parent::add_field(array(
														'name'		=>'page['.$pg['id'].']['.$role['id'].'][delete]',
														'type'		=>'hidden',
														'value'		=>$pg['permissions'][$role['id']]['delete']
													));
												}else{
													parent::add_field(array(
														'checked'	=>$pg['permissions'][$role['id']]['delete'],
														'name'		=>'page['.$pg['id'].']['.$role['id'].'][delete]',
														'type'		=>'checkbox',
														'value'		=>1
													));
												}
												// Hidden
												parent::add_field(array(
													'name'		=>'ids[]',
													'type'		=>'hidden',
													'value'		=>$pg['id']
												));
											parent::add_html('</td>
										</tr>');
									}
								parent::add_html('</tbody>
							</table>');
						}
					}
				parent::add_html('</div>');
			}
		parent::add_html('</div>
		<p class="text-xs-center">');
			parent::add_button(array(
				'class'	=>'btn-success',
				'name'	=>'update',
				'type'	=>'submit',
				'value'	=>'Update'
			));
		parent::add_html('</p>');
	}
	public function process(){
		global $app,$db,$page;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results=parent::unname($results['data']);
				if($results['ids']){
					foreach($results['ids'] as $page_id){
						$results['page'][$page_id][1]=array(
							'view'	=>1,
							'add'	=>1,
							'edit'	=>1,
							'delete'=>1
						);
						if($results['page'][$page_id]){
							foreach($results['roles'] as $role){
								if(!$results['page'][$page_id][$role]){
									$results['page'][$page_id][$role]=array(
										'view'	=>0,
										'add'	=>0,
										'edit'	=>0,
										'delete'=>0
									);
								}else{
									if(empty($results['page'][$page_id][$role]['view'])){
										$results['page'][$page_id][$role]['view']=0;
									}
									if(empty($results['page'][$page_id][$role]['add'])){
										$results['page'][$page_id][$role]['add']=0;
									}
									if(empty($results['page'][$page_id][$role]['edit'])){
										$results['page'][$page_id][$role]['edit']=0;
									}
									if(empty($results['page'][$page_id][$role]['delete'])){
										$results['page'][$page_id][$role]['delete']=0;
									}
								}
							}
						}else{
							foreach($results['roles'] as $role){
								$results['page'][$page_id][$role]=array(
									'view'	=>0,
									'add'	=>0,
									'edit'	=>0,
									'delete'=>0
								);
							}
						}
						$page->update(
							$page_id,
							array(
								'fixed_access'	=>$results['fixed'][$page_id]
							),
							$results['page'][$page_id]
						);
					}
				}
				$app->log_message(3,'Updated Permissions','Updated page permissions');
				$app->set_message('success','Permissions successfully updated.');
				$this->reload();
			}
		}
	}
}