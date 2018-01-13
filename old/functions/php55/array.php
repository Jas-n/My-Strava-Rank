<?php if(!function_exists('array_column')){
	function array_column(array $input,$column_key,$index_key=NULL){
		foreach($input as $key=>$data){
			if(isset($data[$column_key])){
				if($index_key){
					$return[$data[$index_key]]=$data[$column_key];
				}else{
					$return[]=$data[$column_key];
				}
			}
		}
		return $return;
	}
}