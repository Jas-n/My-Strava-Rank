<?php namespace Jasn\MSR;
use Jasn\MSR\athletes;
class athlete extends athletes{
	public function __construct($athlete){
		if($athlete=parent::get_athlete($athlete)){
			foreach($athlete as $key=>$value){
				$this->$key=$value;
			}
			$this->name			=$this->first_name.' '.$this->last_name;
			$this->initial_name	=$this->first_name.' '.substr($this->last_name,0,1);
			if(!$this->username){
				$this->username=$this->initial_name;
			}
			$this->favourite_activity=array(
				'count'=>0,
				'activity'=>''
			);
			foreach($this->activities as $activity=>$data){
				if($data['count']>$this->favourite_activity['count']){
					$this->favourite_activity['count']=$data['count'];
					$this->favourite_activity['activity']=to_noun($data['activity']);
				}
			}
			$this->ranks=$this->ranks();
		}
	}
	public function distances(){
		return array(
			'like'=>distance_like($this->total_miles)
		);
	}
	public function elevation(){
		return array(
			'like'=>elevation_like($this->total_elevation)
		);
	}
	public function ranks(){
		global $db;
		# All Athletes
		$select="SELECT
			`hex_id`,IF(`username`<>'',`username`,CONCAT(`first_name`,' ',LEFT(`last_name`,1))) as `name`,`points`,`points` as `count`,
			FIND_IN_SET(
				`points`,
				(
					SELECT GROUP_CONCAT(`points` ORDER BY `points` DESC)
					FROM `athletes`
				)
			) as `rank` ";
		$ranks['My Strava Rank']=$db->query(
			$select."from `athletes` where id = ?
			union all (".
				$select."from `athletes`
				where `athletes`.`points` <  (select `athletes`.`points` from `athletes` where `id` = ?) 
				order by `athletes`.`points` ASC limit 5
			)
			union all (".
				$select."from `athletes`
			  where `athletes`.`points` > (select `athletes`.`points` from athletes where `id` = ?) 
			  order by `athletes`.`points` DESC limit 5
			)
			ORDER BY `points` ASC",
			array(
				$this->id,
				$this->id,
				$this->id
			)
		);
		return $ranks;
	}
}