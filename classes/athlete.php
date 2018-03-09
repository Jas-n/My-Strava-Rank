<?php namespace Jasn\MSR;
use Jasn\MSR\athletes;
class athlete extends athletes{
	public function __construct($athlete){
		if($athlete=parent::get_athlete($athlete)){
			foreach($athlete as $key=>$value){
				$this->$key=$value;
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
}