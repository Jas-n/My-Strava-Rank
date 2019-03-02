<?php class athletes{
	public function get($id=null){
		global $db;
		if($id){
			return $this->get_athletes(array('ids'=>$id));
		}
		return $this->get_athletes();
	}
	public function get_activities($athlete_id){
		global $db;
		if($data=$db->query("SELECT * FROM `activities` WHERE `athlete_id`=?",$athlete_id)){
			foreach($data as $activity){
				$activies[$activity['activity']]=$activity;
			}
		}
		return $activies;
	}
	public function get_athlete_clubs($athlete_id,$limit=true){
		global $db;
		if($clubs=$db->query(
			"SELECT `clubs`.*,
				(SELECT COUNT(DISTINCT(`athlete_id`)) FROM `club_athletes` WHERE `club_id`=`clubs`.`id`) as `athletes`
			FROM `club_athletes`
			INNER JOIN `clubs`
			ON `club_athletes`.`club_id`=`clubs`.`id`
			WHERE `athlete_id`=?
			".($limit?'LIMIT '.ITEMS_PER_PAGE:''),
			$athlete_id
		)){
			return $clubs;
		}
	}
	/*private function get_athlete($id){
		global $db;
		if($athlete=$db->get_row(
			"SELECT
				*,
				(SELECT `name` FROM `ranks` WHERE `points`<`athletes`.`points` ORDER BY `id` DESC LIMIT 1) as `rank`,
				(
					SELECT `points` FROM `ranks` WHERE `id`=(
						IF(
							(SELECT `id` FROM `ranks` WHERE `points`>`athletes`.`points` ORDER BY `id` DESC LIMIT 1),
							(SELECT `id` FROM `ranks` WHERE `points`>`athletes`.`points` ORDER BY `id` DESC LIMIT 1),
							(SELECT `id` FROM `ranks` ORDER BY `id` DESC LIMIT 1)
						)
					)
				) as `next_rank_points`
			FROM `athletes`
			WHERE
				? IN(`hex_id`,`id`)",
			$id
		)){
			$this->normalise_athlete($athlete);
			$athlete['ranks']=array(
				'all'	=>$db->get_value("SELECT COUNT(1) FROM `athletes` WHERE `points` >=?",$athlete['points']),
				'gender'=>$db->get_value("SELECT COUNT(1) FROM `athletes` WHERE `points` >=? AND `sex`=?",array($athlete['points'],$athlete['sex'])),
				'town'	=>$db->get_value("SELECT COUNT(1) FROM `athletes` WHERE `points` >=? AND `town_id`=?",array($athlete['points'],$athlete['town_id']))
			);
			asort($athlete['ranks']);
			$athlete['activities']=$this->get_activities($athlete['id']);
			$athlete['clubs']=$this->get_athlete_clubs($athlete['id']);
			#$athlete['location']=$db->get_location($athlete['town_id']);
			$athlete['total_miles']=array_sum(array_column($athlete['activities'],'miles'));
			$athlete['total_elevation']=array_sum(array_column($athlete['activities'],'elevation_gain'));
			return $athlete;
		}else{
			return false;
		}
	}*/
	private function get_athletes($args=array()){
		global $db;
		$defaults=array(
			'ids'	=>NULL,
			'order'	=>'points:desc',
			'wheres'=>array()
		);
		$args=array_merge($defaults,$args);
		$athlete_cols=array_keys($db->get_columns('athletes'));
		if($args['wheres']){
			foreach($args['wheres'] as $field=>$value){
				if(in_array($field,$athlete_cols)){
					$where[]='`'.$field.'`=?';
					$options[]=$value;
				}
			}
		}
		if($args['ids']){
			if(!is_array($args['ids'])){
				$args['ids']=array($args['ids']);
			}
			$where[]='`id` IN('.implode(',',$args['ids']).')';
		}
		$orders=explode(',',$args['order']);
		foreach($orders as $order){
			$order=explode(':',$order);
			$order[0]=strtolower($order[0]);
			if(in_array($order[0],$athlete_cols)){
				if(!$order[1] || !in_array(strtoupper($order[1]),array('ASC','DESC'))){
					$order[1]='ASC';
				}else{
					$order[1]=strtoupper($order[1]);
				}
				$order_s[]='`'.$order[0].'` '.$order[1];
			}
		}
		if($order_s){
			$order_s=' ORDER BY '.implode(', ',$order_s);
		}
		if($where){
			$where='WHERE ('.implode(') AND (',$where).')';
		}
		if($athletes=$db->query(
			"SELECT
				`athletes`.*,
				(SELECT `name` FROM `ranks` WHERE `points`<`athletes`.`points` ORDER BY `id` DESC LIMIT 1) as `rank`,
				(
					SELECT `points` FROM `ranks` WHERE `id`=(
						IF(
							(SELECT `id` FROM `ranks` WHERE `points`>`athletes`.`points` ORDER BY `id` DESC LIMIT 1),
							(SELECT `id` FROM `ranks` WHERE `points`>`athletes`.`points` ORDER BY `id` DESC LIMIT 1),
							(SELECT `id` FROM `ranks` ORDER BY `id` DESC LIMIT 1)
						)
					)
				) as `next_rank_points`
			FROM `athletes`
			".$where."
			".$order_s.
			SQL_LIMIT,
			$options
		)){
			foreach($athletes as &$athlete){
				$athlete['clubs']=$this->get_athlete_clubs($athlete['id']);
				if($athlete['sex']=='M'){
					$athlete['gender']='Male';
				}elseif($athlete['sex']=='F'){
					$athlete['gender']='Female';
				}else{
					$athlete['gender']='';
				}
			}
		}
		return array(
			'count'	=>$db->result_count(
				"FROM `athletes` ".$where,
				$options
			),
			'data'	=>$athletes
		);
	}
}