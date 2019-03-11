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
		var map={
			athlete_points:	php.number_format(data.gamification.points),
			athlete_rank:	data.gamification.rank,
			first_name:		data.first_name,
			msr_join:		php.formatted_date(data.added.raw),
			strava_join:	php.formatted_date(data.strava_join.raw),
			username:		athlete.name
		};
		console.log(data);
		$('[data-athlete]').each(function(){
			if(map[$(this).data('athlete')]){
				$(this).text(map[$(this).data('athlete')]);
			}else{
				console.log($(this).data('athlete'));
			}
		});
	}
};