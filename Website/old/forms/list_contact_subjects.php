<?php class new_contact_subjects extends form{
	public function __construct(){
		global $db,$users;
		parent::__construct('name=new_contacts&class=form-horizontal');
		$formatted_usrs=$users->get_users(NULL,array(2,3));
		foreach($formatted_usrs['users'] as $usr){
			$formatted_users[$usr['id']]=$usr['first_name']." ".$usr['last_name'];
		}
		parent::add_field("type=text&name=new_title&label=Subject&placeholder=Subject&required=1");
		parent::add_select("name=new_staff&label=Staff&size=5&multiple=1&required=1",$formatted_users);
		parent::add_html("<div class='col-sm-offset-3 col-sm-9'><p>");
		parent::add_button('name=save&showlabel=no&type=submit&class=btn-primary&value=Add&wraptag=p class="alignc"');
		parent::add_html("</p></div>");
	}
	public function process(){
		global $app,$db,$user;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results=$this->unname($results['data']);
				unset(
					$results['form_name'],
					$results['save']
				);
				# Custom validation
				if(1!=1){
				}
				# Update DB
				else{
					# Page dependant processes (files, etc.)
					$db->put_contact_subject($results['new_title'],$results['new_staff']);
					$app->set_message('success','Contact subjects successfully updated');
					$app->log_message(3,'Contact Subjects Updated','Updated contact subjects');
				}
			}
		}
	}
}
class existing_contact_subjects extends form{
	public function __construct(){
		global $db,$users;
		parent::__construct('name=existing_contacts&class=form-inline');
		$formatted_usrs=$users->get_users(NULL,array(2,3));
		foreach($formatted_usrs['users'] as $usr){
			$formatted_users[$usr['id']]=$usr['first_name']." ".$usr['last_name'];
		}
		parent::add_html('<table class="table table-hover table-striped"><thead><tr><th>');
		parent::add_field(array(
			"name"	=>"check_all",
			"type"	=>"checkbox",
			"value"	=>1
		));
		parent::add_html("</th><th>ID</th><th>Title</th><th>Staff</th></tr></thead><tbody>");
		if($contact_subjects=$db->get_contact_subjects()){
			foreach($contact_subjects as $contact_subject){
				parent::add_html("<tr><td>");
				parent::add_field(array(
					'class'	=>'check',
					"name"	=>"check[]",
					"type"	=>"checkbox",
					"value"	=>$contact_subject['id']
				));
				parent::add_html("</td><td>".$contact_subject['id']."</td><td>");
				parent::add_field("type=text&name=title[]&showlabel=no&placeholder=title&required=1&value=".$contact_subject['subject']);
				parent::add_field("type=hidden&name=ids[]&value=".$contact_subject['id']);
				parent::add_html("</td><td>");
				parent::add_select(
					array(
						"multiple"	=>1,
						"name"		=>"staff_".$contact_subject['id']."[]",
						"required"	=>1,
						"value"		=>$contact_subject['staff'],
						"size"		=>5
					),
					$formatted_users
				);
				parent::add_html("</td></tr>");
			}
		}else{
			parent::add_html("<tr class='danger'><td colspan='4'>No contact subjects have been created</td></tr>");
		}
		parent::add_html('</tbody></table><p class="text-xs-center">');
		parent::add_button('name=save&type=submit&class=btn-primary&value=Save');
		parent::add_html("</p>");
	}
	public function process(){
		global $app,$db,$user;
		if($_POST['form_name']==$this->data['name']){
			$results=parent::process();
			if($results['status']!='error'){
				$results=$this->unname($results['data']);
				unset(
					$results['form_name'],
					$results['save']
				);
				# Custom validation
				if(1!=1){
				}
				# Update DB
				else{
					# Page dependant processes (files, etc.)
					foreach($results['ids'] as $key=>$id){
						$db->update_contact_subject($id,$results['title'][$key],$results["staff_".$id]);
					}
					$app->set_message('success','Contact subjects successfully updated');
					$app->log_message(3,'Contact Subjects Updated','Updated contact subjects');
				}
			}
		}
	}
}