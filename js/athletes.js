var athletes={
	initiated:	false,
	init:function(){
		this.get();
	},
	get:function(id){
		core.ajax('athletes','get',this.got);
	},
	got:function(json){
		var athlete={};
		for(i=0;i<json.data.length;i++){
			athlete=json.data[i];
			console.log(athlete);
			$('.ranks').append('<div class="card rank" data-load="athlete" data-id="'+athlete.id+'">'+
				'<div class="rank-number">'+(i+1)+'</div>'+
				'<h3>'+(athlete.username?athlete.username:athlete.first_name+' '+athlete.last_name[0])+'</h3>'+
				'<div class="row">'+
					'<div class="col-6">'+php.number_format(athlete.points)+'</div>'+
					'<div class="col-6">'+athlete.rank+'</div>'+
				'</div>'+
				'<div class="progress">'+
					'<div class="progress-bar" style="width:'+athlete.rank_percent+'%" role="progressbar" aria-valuenow="'+athlete.points+'" aria-valuemin="0" aria-valuemax="'+athlete.next_rank_points+'"></div>'+
				'</div>'+
			'</div>');
		}
	}
};