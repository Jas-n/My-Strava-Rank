<?php if($athlete=$strava->tokenExchange($_GET['code'])){
	$athlete_hex_id=str_pad(base_convert($athlete->athlete->id,10,16),6,0,STR_PAD_LEFT);
	if(!$athlete_id=$db->get_value("SELECT `id` FROM `athletes` WHERE `strava_id`=?",$athlete->athlete->id)){
		if($location=$db->get_town_by_name($athlete->athlete->city,$athlete->athlete->country)){
			$location_id=$location['id'];
		}else{
			$location_id=$db->add_location($athlet->athlete->city,$athlete->athlete->state,$athlete->athlete->country);
		}
		$msr_join=DATE_TIME;
		$strava_join=str_replace('T',' ',$athlete->athlete->created_at);
		$db->query(
			"INSERT INTO `athletes` (
				`hex_id`,	`strava_id`,`town_id`,	`access_token`,	`first_name`,
				`last_name`,`username`,	`email`,	`sex`,			`strava_join`,
				`added`,	`updated`,	`updates`,	`subscribed`
			) VALUES (?,?,?,?,?,	?,?,?,?,?,	?,?,?,?)",
			array(
				$athlete_hex_id,
				$athlete->athlete->id,
				$location_id,
				$athlete->access_token,
				$athlete->athlete->firstname,
				
				$athlete->athlete->lastname,
				$athlete->athlete->username,
				$athlete->athlete->email,
				$athlete->athlete->sex,
				$strava_join,
				
				DATE_TIME,
				DATE_TIME,
				1,
				0
			)
		);
		$athlete_id=$db->insert_id();
		$added=true;
		$strava->setAccessToken($athlete->access_token);
		$data=$strava->get('athletes/'.$athlete->athlete->id.'/stats');
		foreach(array('ride','run','swim') as $activity){
			if($activity_id=$db->get_value(
				"SELECT `id`
				FROM `activities`
				WHERE
					`athlete_id`=? AND
					`activity`=?",
				array(
					$athlete_id,
					$activity
				)
			)){
				$db->query(
					"UPDATE `activities`
					SET
						`count`=?,
						`miles`=?,
						`moving_hours`=?,
						`total_hours`=?,
						`elevation_gain`=?
					WHERE
						`id`=? AND
						`activity`=?",
					array(
						$data->{'all_'.$activity.'_totals'}->count,
						$data->{'all_'.$activity.'_totals'}->distance*0.0006213712,
						$data->{'all_'.$activity.'_totals'}->moving_time/60/60,
						$data->{'all_'.$activity.'_totals'}->elapsed_time/60/60,
						$data->{'all_'.$activity.'_totals'}->elevation_gain*0.0006213712,
						$activity_id,
						$activity
					)
				);
			}else{
				$db->query(
					"INSERT INTO `activities` (
						`athlete_id`,	`activity`,		`count`,`miles`,`moving_hours`,
						`total_hours`,	`elevation_gain`
					) VALUES (?,?,?,?,?,	?,?)",
					array(
						$athlete->athlete->id,
						$activity,
						$data->{'all_'.$activity.'_totals'}->count,
						$data->{'all_'.$activity.'_totals'}->distance*0.0006213712,
						$data->{'all_'.$activity.'_totals'}->moving_time/60/60,
						
						$data->{'all_'.$activity.'_totals'}->elapsed_time/60/60,
						$data->{'all_'.$activity.'_totals'}->elevation_gain*0.0006213712,
					)
				);
			}
		}
		$athlete_activities=$athletes->get_activities($athlete_id);
		$points=0;
		# Days since strava join
		$strava_join=(strtotime(DATE_TIME)-strtotime($strava_join))/60/60/24;
		$points+=$strava_join;
		# Days since MSR join
		$msr_join=(strtotime(DATE_TIME)-strtotime($msr_join))/60/60/24;
		$points+=$msr_join;
		# Total activities
		$activities=array_sum(array_column($athlete_activities,'count'))*10;
		$points+=$activities;
		# Total moving hours
		$tmh=array_sum(array_column($athlete_activities,'moving_hours'))*10;
		$points+=$tmh;
		# Total distance
			$td=$athlete_activities['ride']['miles'];
			$td+=$athlete_activities['run']['miles']*5;
			$td+=$athlete_activities['swim']['miles']*10;
		$points+=$td;
		# Speed * 10
		if($tmh){
			$speed=($td/$tmh)*100;
		}
		$points+=$speed;
		# Store
		$db->query(
			"UPDATE `athletes`
			SET
				`updates`=`updates`+1,
				`points`=?,
				`updated`=?
			WHERE `id`=?",
			array(
				$points,
				DATE_TIME,
				$athlete_id
			)
		);
	}
	if($athlete->athlete->clubs){
		$db->query('DELETE FROM `club_athletes` WHERE `athlete_id`=?',$athlete_id);
		foreach($athlete->athlete->clubs as $club){
			$club_id=$db->get_value("SELECT `id` FROM `clubs` WHERE `strava_id`=?",$club->id);
			if(!$club_id){
				$db->query(
					"INSERT INTO `clubs` (
						`hex_id`,`strava_id`,`name`,`added`,`updated`
					) VALUES (?,?,?,?,?)",
					array(
						str_pad(base_convert($club->id,10,16),6,0,STR_PAD_LEFT),
						$club->id,
						$club->name,
						DATE_TIME,
						DATE_TIME
					)
				);
				$club_id=$db->insert_id();
			}else{
				$db->query(
					"UPDATE `clubs`
					SET `updated`=?
					WHERE `id`=?",
					array(
						DATE_TIME,
						$club_id
					)
				);
			}
			if(!$db->get_value(
				"SELECT *
				FROM `club_athletes`
				WHERE
					`club_id`=? AND
					`athlete_id`=?",
				array(
					$club_id,
					$athlete_id
				)
			)){
				$db->query(
					"INSERT INTO `club_athletes` (
						`club_id`,`athlete_id`
					) VALUES (?,?)",
					array(
						$club_id,
						$athlete_id
					)
				);
			}
		}
	}
	$core->set_message('success','Your Stava account has now been '.($added?' added to My Strava Rank.':'updated.'));
	header('Location: /athlete/'.$athlete_hex_id);
	exit;
}
header('Location: /?error');