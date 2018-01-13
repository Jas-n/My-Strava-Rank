<?php class locations{
	public function get_locations(){
		global $db;
		$locations=$db->query(
			"SELECT
				`towns`.`id`,`towns`.`town`,
				`counties`.`county`,
				`countries`.`country`,
				(SELECT COUNT(`id`) FROM `athletes` WHERE `town_id`=`towns`.`id`) as `athletes`,
				(SELECT SUM(`points`) FROM `athletes` WHERE `town_id`=`towns`.`id`) as `points`
			FROM `towns`
			INNER JOIN `counties`
			ON `towns`.`county_id`=`counties`.`id`
			INNER JOIN `countries`
			ON `counties`.`country_id`=`countries`.`id`
			WHERE `towns`.`id` IN(
				SELECT DISTINCT (`town_id`)
				FROM `athletes`
			)
			LIMIT ".($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0).','.ITEMS_PER_PAGE
		);
		return array(
			'count'	=>$db->result_count(
				"FROM `towns`
				WHERE `id` IN(
					SELECT DISTINCT (`town_id`)
					FROM `athletes`
				)"
			),
			'data'	=>$locations
		);
	}
	public function get_location($id){
		global $db;
		$location=$db->get_row(
			"SELECT
				`towns`.*,
				`counties`.`county`,
				`countries`.`country`,
				(SELECT SUM(`points`) FROM `athletes` WHERE `town_id`=`towns`.`id`) as `points`
			FROM `towns`
			INNER JOIN `counties`
			ON `towns`.`county_id`=`counties`.`id`
			INNER JOIN `countries`
			ON `counties`.`country_id`=`countries`.`id`
			WHERE `towns`.`id` =?",
			$id
		);
		foreach(array('ride','run','swim') as $activity){
			$location['activities'][$activity]['count']=$db->get_value(
				"SELECT SUM(`count`)
				FROM `activities`
				WHERE 
					`athlete_id` IN(SELECT `athlete_id` FROM `athletes` WHERE `town_id`=?) AND
					`activity`=?",
				array(
					$location['id'],
					$activity
				)
			);
			$location['activities'][$activity]['miles']=$db->get_value(
				"SELECT SUM(`miles`)
				FROM `activities`
				WHERE 
					`athlete_id` IN(SELECT `athlete_id` FROM `athletes` WHERE `town_id`=?) AND
					`activity`=?",
				array(
					$location['id'],
					$activity
				)
			);
			$location['activities'][$activity]['moving_hours']=$db->get_value(
				"SELECT SUM(`moving_hours`)
				FROM `activities`
				WHERE
					`athlete_id` IN(SELECT `athlete_id` FROM `athletes` WHERE `town_id`=?) AND
					`activity`=?",
				array(
					$location['id'],
					$activity
				)
			);
			$location['activities'][$activity]['total_hours']=$db->get_value(
				"SELECT SUM(`total_hours`)
				FROM `activities`
				WHERE
					`athlete_id` IN(SELECT `athlete_id` FROM `athletes` WHERE `town_id`=?) AND
					`activity`=?",
				array(
					$location['id'],
					$activity
				)
			);
			$location['activities'][$activity]['elevation_gain']=$db->get_value(
				"SELECT SUM(`elevation_gain`)
				FROM `activities`
				WHERE
					`athlete_id` IN(SELECT `athlete_id` FROM `athletes` WHERE `town_id`=?) AND
					`activity`=?",
				array(
					$location['id'],
					$activity
				)
			);
		}
		$location['athletes']=$this->get_location_athletes($location['id']);
		return $location;
	}
	public function get_location_athletes($location_id){
		global $athletes,$db;
		$athlete_ids=$db->query(
			"SELECT `athletes`.`id`
			FROM `athletes`
			WHERE `town_id`=?",
			$location_id
		);
		return $athletes->get_athletes('points:asc',NULL,array_column($athlete_ids,'id'));
	}
}