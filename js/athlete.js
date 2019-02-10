var athlete={
	initiated:	false,
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
		$('h1').text(data.username?data.username:data.first_name+' '+data.last_name[0]);
	}
};