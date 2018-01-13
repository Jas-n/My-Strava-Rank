<?php class clubs{
	public function get_club($id){
		global $db;
		if(!$club=$db->get_row("SELECT * FROM `clubs` WHERE `hex_id`=?",$id)){
			if(!$club=$db->get_row("SELECT * FROM `clubs` WHERE `id`=?",$id)){
				return false;
			}
		}
		foreach(array('ride','run','swim') as $activity){
			$club['activities'][$activity]['count']=$db->get_value(
				"SELECT SUM(`count`)
				FROM `activities`
				WHERE 
					`athlete_id` IN(SELECT `athlete_id` FROM `club_athletes` WHERE `club_id`=?) AND
					`activity`=?",
				array(
					$club['id'],
					$activity
				)
			);
			$club['activities'][$activity]['miles']=$db->get_value(
				"SELECT SUM(`miles`)
				FROM `activities`
				WHERE 
					`athlete_id` IN(SELECT `athlete_id` FROM `club_athletes` WHERE `club_id`=?) AND
					`activity`=?",
				array(
					$club['id'],
					$activity
				)
			);
			$club['activities'][$activity]['moving_hours']=$db->get_value(
				"SELECT SUM(`moving_hours`)
				FROM `activities`
				WHERE
					`athlete_id` IN(SELECT `athlete_id` FROM `club_athletes` WHERE `club_id`=?) AND
					`activity`=?",
				array(
					$club['id'],
					$activity
				)
			);
			$club['activities'][$activity]['total_hours']=$db->get_value(
				"SELECT SUM(`total_hours`)
				FROM `activities`
				WHERE
					`athlete_id` IN(SELECT `athlete_id` FROM `club_athletes` WHERE `club_id`=?) AND
					`activity`=?",
				array(
					$club['id'],
					$activity
				)
			);
			$club['activities'][$activity]['elevation_gain']=$db->get_value(
				"SELECT SUM(`elevation_gain`)
				FROM `activities`
				WHERE
					`athlete_id` IN(SELECT `athlete_id` FROM `club_athletes` WHERE `club_id`=?) AND
					`activity`=?",
				array(
					$club['id'],
					$activity
				)
			);
		}
		$club['athletes']=$this->get_club_athletes($club['id']);
		$club['points']=$db->get_value("SELECT SUM(`points`) FROM `athletes` WHERE `id` IN(SELECT `athlete_id` FROM `club_athletes` WHERE `club_id`=?)",$club['id']);
		return $club;
	}
	public function get_club_athletes($club_id){
		global $athletes,$db;
		$athlete_ids=$db->query(
			"SELECT `athletes`.`id`
			FROM `club_athletes`
			INNER JOIN `athletes`
			ON `club_athletes`.`athlete_id`=`athletes`.`id`
			WHERE `club_id`=?",$club_id
		);
		return $athletes->get_athletes('points:asc',NULL,array_column($athlete_ids,'id'));
	}
	public function get_clubs(){
		global $db;
		return array(
			'count'	=>$db->result_count("FROM `clubs`"),
			'data'	=>$db->query(
				"SELECT *,
				(SELECT COUNT(DISTINCT(`athlete_id`)) FROM `club_athletes` WHERE `club_id`=`clubs`.`id`) as `athletes`
				FROM `clubs`
				ORDER BY `points` DESC, `name` ASC"
			)
		);
	}
}