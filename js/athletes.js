var athletes={
	initiated:	false,
	init:function(){
		this.get();
	},
	get:function(id){
		msr.ajax('athletes','get',this.got,{
			id:id
		});
	},
	got:function(json){
		console.log(json);
	}
};