var athlete={
	initiated:	false,
	name:		false,
	small:		false,
	init:function(){
		this.get();
	},
	get:function(id){
		core.ajax('athletes','get',this.got,{
			id:core.id
		});
	},
	got:function(json){
		var data=json.data;
		athlete.name=data.username?data.username:data.first_name+' '+data.last_name[0];
		athlete.small=data.last_activity.formatted?data.last_activity.formatted:data.updated.formatted;
		core.set_header(athlete);
	}
};