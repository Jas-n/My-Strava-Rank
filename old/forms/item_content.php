<?php class content extends form{
	public function __construct($page){
		global $user;
		parent::__construct("name=content&class=form-horizontal");
		if($_GET['sid']){
			parent::add_field(array(
				'name'	=>'title[]',
				'type'	=>'hidden',
				'value'	=>$page['title']
			));
		}else{
			parent::add_html('<h2>Page Detailss</h2>');
			parent::add_fields(array(
				array(
					'label'			=>'Title',
					'name'			=>'title',
					'placeholder'	=>'Title',
					'type'			=>'text',
					'value'			=>$page['title']
				)
			));
		}
		parent::add_html('<ul class="nav nav-tabs text-xs-center" role="tablist">
			<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#new" role="tab">New</a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane" id="new">
				<h2>Add Content</h2>');
				parent::add_fields(array(
					array(
						'label'			=>'Section Title',
						'name'			=>'new_header',
						'placeholder'	=>'Header',
						'type'			=>'text'
					),
					array(
						'class'	=>'tinymce',
						'label'	=>'Section Content',
						'name'	=>'new_body',
						'rows'	=>20,
						'type'	=>'textarea'
					)
				));
			parent::add_html('</div>
		</div>');
		
		
		if($user->is_role('Formation')){
			
		}
		if($page['contents']){
			parent::add_html('<h2>Page Content</h2>
			<div class="row">
				<div class="col-md-12">');
					foreach($page['contents'] as $content){
						parent::add_html('<div class="row">
							<div class="col-md-6">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h3 class="panel-title">'.$content['title'].'</h3>
									</div>
									<div class="panel-body">');
										parent::add_fields(array(
											array(
												'label'	=>'ID',
												'type'	=>'static',
												'value'	=>$content['id']
											),
											array(
												'label'			=>'Title',
												'name'			=>'titles[]',
												'placeholder'	=>'Title',
												'type'			=>'text',
												'value'			=>$content['title']
											),
											array(
												'label'	=>'Slug',
												'type'	=>'static',
												'value'	=>$content['slug']
											),
											array(
												'class'	=>'tinymce',
												'name'	=>'content[]',
												'rows'	=>20,
												'type'	=>'textarea',
												'value'	=>$content['content']
											),
											array(
												'name'	=>'section_id[]',
												'type'	=>'hidden',
												'value'	=>$content['id']
											)
										));
									parent::add_html('</div>
								</div>
							</div>
						</div>');
					}
				parent::add_html('</div>
			</div>');
		}
		parent::add_html("<div class='col-sm-offset-3 col-sm-9'><p>");
		parent::add_button('name=update&showlabel=no&type=submit&value=Update&class=btn-primary');
		parent::add_html("</p></div>");
	}
	public function process(){
		global $app,$db,$page,$pg;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results=parent::unname($results['data']);
				$db->query(
					'UPDATE `pages`
					SET
						`title`=?,
						`updated`=?
					WHERE `id`=?',
					array(
						$results['title'],
						DATE_TIME,
						$pg['id']
					)
				);
				if($results['new_header'] && !$results['new_body']){
					$app->set_message('error','New content could not be saved without body content.');
				}elseif(!$results['new_header'] && $results['new_body']){
					$app->set_message('error','New content could not be saved without section header.');
				}elseif($results['new_header'] && $results['new_body']){
					$db->query(
						"INSERT INTO `page_content` (
							`page_id`,
							`title`,
							`slug`,
							`content`
						) VALUES (?,?,?,?)",
						array(
							$pg['id'],
							$results['new_header'],
							tidy_filename($results['new_header']),
							$results['new_body']
						),0
					);
				}
				if($results['titles']){
					foreach($results['titles'] as $key=>$title){
						$db->query(
							"UPDATE `page_content`
							SET
								`title`=?,
								`content`=?
							WHERE `id`=?",
							array(
								$title,
								$results['content'][$key],
								$results['section_id'][$key]
							),0
						);
					}
				}
				$app->set_message('success','Page has been updated');
				$app->log_message(3,'Page Updated','Updated the page \''.$results['title'].'\'');
				$this->reload($page->get_page($pg['id']));
			}
		}
	}
}