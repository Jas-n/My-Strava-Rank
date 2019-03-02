<?php class athletes{
	public function get_activities($athlete_id){
		global $db;
		if($data=$db->query("SELECT * FROM `activities` WHERE `athlete_id`=?",$athlete_id)){
			foreach($data as $activity){
				$activies[$activity['activity']]=$activity;
			}
		}
		return $activies;
	}
	public function get_athlete($id){
		global $db;
		if($athlete=$db->get_row("SELECT * FROM `athletes` WHERE `hex_id`=? OR `id`=?",array($id,$id))){
			$athlete['ranks']=array(
				'all'	=>$db->get_value("SELECT COUNT(1) FROM `athletes` WHERE `points` >=?",$athlete['points']),
				'gender'=>$db->get_value("SELECT COUNT(1) FROM `athletes` WHERE `points` >=? AND `sex`=?",array($athlete['points'],$athlete['sex'])),
				'town'	=>$db->get_value("SELECT COUNT(1) FROM `athletes` WHERE `points` >=? AND `town_id`=?",array($athlete['points'],$athlete['town_id']))
			);
			asort($athlete['ranks']);
			$athlete['activities']=$this->get_activities($athlete['id']);
			$athlete['clubs']=$this->get_athlete_clubs($athlete['id']);
			#$athlete['location']=$db->get_location($athlete['town_id']);
			return $athlete;
		}else{
			return false;
		}
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
	public function get_athletes($order='points:desc',$wheres=array(),$ids=NULL){
		global $db;
		$athlete_cols=array_keys($db->get_columns('athletes'));
		if($wheres){
			foreach($wheres as $field=>$value){
				if(in_array($field,$athlete_cols)){
					$where[]='`'.$field.'`=?';
					$options[]=$value;
				}
			}
		}
		if($ids){
			if(!is_array($ids)){
				$ids=array($ids);
			}
			$where[]='`id` IN('.implode(',',$ids).')';
		}
		$orders=explode(',',$order);
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
			"SELECT *
			FROM `athletes`
			".$where."
			".$order_s."
			LIMIT ".($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0).','.ITEMS_PER_PAGE,
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