<?php define('CRON',true);
set_time_limit(0);
ini_set('memory_limit',-1);
$app_require=array(
	'php.Jasn\MSR\athletes',
	'php.Jasn\MSR\clubs'
);
require(__DIR__.'/../init.php');
$month	=date('m');
$day	=date('d');
$hour	=date('G');
if(in_array($hour,array(0,6,12,18))){
	# Collate data for 50 lowest `updatees`
	if($athletes_=$db->query("SELECT * FROM `athletes` ORDER BY `updates` ASC LIMIT 1")){ # Limit 50
		foreach($athletes_ as $athlete){
			$strava->setAccessToken($athlete['access_token']);
			$data=$strava->get('athletes/'.$athlete['strava_id'].'/stats');
			foreach(array('ride','run','swim') as $activity){
				if($activity_id=$db->get_value(
					"SELECT `id`
					FROM `activities`
					WHERE
						`athlete_id`=? AND
						`activity`=?",
					array(
						$athlete['id'],
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
							$athlete['id'],
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
			$athlete['activities']=$athletes->get_activities($athlete['id']);
			$points=0;
			# Days since strava join
			$strava_join=(strtotime(DATE_TIME)-strtotime($athlete['strava_join']))/60/60/24;
			$points+=$strava_join;
			# Days since MSR join
			$msr_join=(strtotime(DATE_TIME)-strtotime($athlete['added']))/60/60/24;
			$points+=$msr_join;
			# Total activities
			$activities=array_sum(array_column($athlete['activities'],'count'))*10;
			$points+=$activities;
			# Total moving hours
			$tmh=array_sum(array_column($athlete['activities'],'moving_hours'))*10;
			$points+=$tmh;
			# Total distance
				$td=$athlete['activities']['ride']['miles'];
				$td+=$athlete['activities']['run']['miles']*5;
				$td+=$athlete['activities']['swim']['miles']*10;
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
					$athlete['id']
				)
			);
		}
		$cron_messages[]='Updated '.sizeof($athletes_).' athletes\' activities.';
	}
}
# Hour specific
switch($hour){
	case 0:
		# Clear logs
		$db->query("DELETE FROM `logs` WHERE `date` < ?",date('Y-m-d H:i:s',strtotime("-".LOGS_AGE." days")));
		if($db->rows_updated()){
			$cron_messages[]="Deleted ".$db->rows_updated()." logs that are older than ".LOGS_AGE." days.";
		}
		# Recalculate ranks
		$top_points=$db->get_value("SELECT MAX(`points`)*.9 FROM `athletes`");
		$top_rank=$db->get_row("SELECT `id`,`points` FROM `ranks` ORDER BY `id` DESC");
		if($top_rank['points']<$top_points){
			$base_ponts=POINTS_BASE+1;
			$db->query("UPDATE `settings` SET `value`=? WHERE `name`=?",array($base_ponts,'points_base'));
			for($i=1;$i<=$top_rank['id'];$i++){
				if($i==1){
					$points=1;
				}else{
					$h=$points;
					$points=$h*2+ceil($h*$base_ponts/100);
				}
				$db->query(
					"UPDATE `ranks`
					SET
						`points`=?
					WHERE `id`=?",
					array(
						$points,
						$i
					)
				);
			}
		}
		# Once a month
		if($day==1){
			# Keep log IDs low
			if($logs=$db->query("SELECT `id` FROM `logs`")){
				foreach($logs as $i=>$log){
					$db->query("UPDATE `logs` SET `id`=? WHERE `id`=?",array($i+1,$log['id']));
				}
			}
			# Keep log IDs low
			if($logs=$db->query("SELECT `id` FROM `notification_users`")){
				foreach($logs as $i=>$log){
					$db->query("UPDATE `notification_users` SET `id`=? WHERE `id`=?",array($i+1,$log['id']));
				}
			}
		}

		# Update Club points
		$clubs=$db->query("SELECT `id`,`name` FROM `clubs`");
		foreach($clubs as &$club){
			$club_points=$db->query(
				"SELECT SUM(`points`) as `points`
				FROM `athletes`
				INNER JOIN `club_athletes`
				ON `athletes`.`id`=`club_athletes`.`athlete_id`
				WHERE `club_athletes`.`club_id`=?
				GROUP BY `athletes`.`id`",
				$club['id']
			);
			$db->query(
				"UPDATE `clubs`
				SET
					`points`=?,
					`updated`=?
				WHERE `id`=?",
				array(
					array_sum(array_column($club_points,'points')),
					DATE_TIME,
					$club['id']
				)
			);
		}
		$cron_messages[]='Updated points for '.sizeof($clubs).' clubs.';
		break;
	case 16:
		# Daily emails
		break;
}
# Once a month
if($hour==0 && $day==1){
	# Keep log IDs low
	if($logs=$db->query("SELECT `id` FROM `logs`")){
		foreach($logs as $i=>$log){
			$db->query("UPDATE `logs` SET `id`=? WHERE `id`=?",array($i+1,$log['id']));
		}
	}
	$cron_messages[]="Tidied up disposable database items.";
}
# Log the cron run
if($cron_messages){
	$core->log_message(3,sprintf('%02d',$hour).':00 Update',implode("<br>",$cron_messages));
}