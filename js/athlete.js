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
			//activity_altitude:	php.number_format(data.activity_altitude),
			activity_count:		php.number_format(data.activity_count),
			activity_height:	php.number_format(data.activity_height),
			activity_hours:		php.number_format(data.activity_hours),
			activity_miles:		php.number_format(data.activity_miles),
			//altitude_complete:	Math.round(data.altitude.complete),
			//altitude_equivalent:data.altitude.to_text,
			athlete_club_count:	php.number_format(data.clubs.length),
			athlete_favourite_activity:data.favourite_activity.activity,
			athlete_favourite_count:php.number_format(data.favourite_activity.count),
			athlete_points:		php.number_format(data.gamification.points),
			athlete_rank:		data.gamification.rank,
			distance_complete:	Math.round(data.distance.complete),
			distance_equivalent:data.distance.to_text,
			first_name:			data.first_name,
			msr_join:			php.formatted_date(data.added.raw),
			msr_join_later:		data.msr_join_later,
			strava_join:		php.formatted_date(data.strava_join.raw),
			username:			athlete.name
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