<?php # 2.5 - get_row auto append LIMIT 1
class database{
	private $user = "";
	private $pass = "";
	private $name = "";
	private $last_id;
	private $rows_updated;
	private $db;
	public function __construct(){
		if(is_file(ROOT.'classes/connect.php')){
			include(ROOT.'classes/connect.php');
			$this->host=$host;
			$this->name=$name;
			$this->user=$user;
			$this->pass=$pass;
		}else{
			echo 'No file: '.ROOT.'classes/connect.php';
		}
		$this->db=$this->con();
	}
	# Backup
	public function backup($table,$location=NULL){
		if(strtolower($table)!='all'){
			$tables=' --tables '.$table;
		}
		if($location==NULL){
			$location=ROOT.'backups';
		}else{
			$location=ROOT.$location;
		}
		if(!is_dir($location)){
			mkdir($location,0777,1);
		}
		$command='mysqldump -u '.$this->user.' -p'.$this->pass.' --databases '.$this->name.$tables.' --single-transaction > '.$location.'/'.$table.'__`date +%Y-%m-%d_%H-%M-%S`.sql';
		system($command);
		return $location.'/'.$table.'__'.date('Y-m-d_H-i-s').'.sql';
	}
	# Cleans out JavaScript and additional HTML
	private function cleanInput(&$input){
		if(!is_array($input)){
			$input=array($input);
		}
		foreach($input as $key=>$data){
			$search = array(
				'@<script[^>]*?>.*?</script>@si',   // Strip out javascript
				'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
				'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
				'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
			);
			$input[$key]=preg_replace($search,'',$data);
		}
		return $input;
	}
	# DB Connect
	private function con(){
		try{
			$db=new PDO('mysql:dbname='.$this->name.';host='.$this->host,$this->user,$this->pass);
			$db->query("SET NAMES UTF8");
			return $db;
		}catch(PDOException $e){
			echo '<h3>Error Connecting to Database!</h3>'.$e->getMessage();
			exit;
		}
	}
	# Stores Error
	public function error($type='PHP',$message,$file,$line,$severity=NULL,$data=NULL){
		global $app;
		$app->log_message(
			1,
			$type.' Error',
			'<strong>Error: </strong>'.$message,
			array(
				'trace'=>debug(),
				'user_history'=>$_SESSION['history']
			)
		);
	}
	# Get table columns
	public function get_columns($table){
		if($columns=$this->query("SHOW COLUMNS FROM `".$table."`")){
			foreach($columns as $column){
				$column=array_change_key_case($column);
				$return[$column['field']]=$column;
			}
			return $return;
		}
		return false;
	}
	# Get location from $location_id
	public function add_location($town,$county,$country){
		if(!$country_id=$this->get_value("SELECT `id` FROM `countries` WHERE `country` LIKE ?",$country)){
			$this->query("INSERT INTO `countries` (`country`) VALUES (?)",$country);
			$country_id=$this->insert_id();
		}
		if(!$county_id=$this->get_value("SELECT `id` FROM `counties` WHERE `county` LIKE ?",$county)){
			$this->query("INSERT INTO `counties` (`country_id`,`county`) VALUES (?,?)",array($country_id,$county));
			$county_id=$this->insert_id();
		}
		$this->query("INSERT INTO `towns` (`county_id`,`town`) VALUES (?,?)",array($county_id,$town));
		return $this->insert_id();
	}
	public function get_location($location_id){
		return $this->get_row(
			"SELECT
				`towns`.*,
				`counties`.`county`,
				`countries`.`country`
			FROM `towns`
			INNER JOIN `counties`
			ON `towns`.`county_id`=`counties`.`id`
			INNER JOIN `countries`
			ON `counties`.`country_id`=`countries`.`id`
			WHERE `towns`.`id`=?",
			$location_id
		);
	}
	public function get_town_by_name($town,$country){
		return $this->get_row(
			"SELECT
				`towns`.*,
				`counties`.`county`,
				`countries`.`country`
			FROM `towns`
			INNER JOIN `counties`
			ON `towns`.`county_id`=`counties`.`id`
			INNER JOIN `countries`
			ON `counties`.`country_id`=`countries`.`id`
			WHERE `town` LIKE ? AND `country` LIKE ?",
			array(
				$town,
				$country
			)
		);
	}
	# Get Logs
	public function get_logs($level=NULL){
		if($level!=NULL){
			$where='WHERE `level`=?';
			$options[]=$level;
		}
		return array(
			'count'	=>$this->result_count("FROM `logs` $where"),
			'logs'	=>$this->query('
				SELECT
					`logs`.*,
					`users`.`first_name`,`users`.`last_name`
				FROM `logs`
				LEFT JOIN `users`
				ON `logs`.`user_id`=`users`.`id`
				'.$where.'
				ORDER BY `id` DESC
				LIMIT '.($_GET['page']?(($_GET['page']-1)*ITEMS_PER_PAGE):0).','.ITEMS_PER_PAGE,
				$options
			)
		);
	}
	# Get row (LIMIT is automatically appended to $sql)
	public function get_row($sql,$values=NULL,$clense=true,$echo=NULL){
		if($result=$this->query($sql.' LIMIT 1',$values,$clense,$echo)){
			return $result[0];
		}
	}
	# Get Value
	public function get_value($sql,$values=NULL,$clense=true,$echo=NULL){
		if($result=$this->get_row($sql,$values,$clense,$echo)){
			return $result[key($result)];
		}
	}
	# Get last insert id
	public function insert_id(){
		return $this->last_id;
	}
	# Next ID
	public function next_id($table){
		return $this->get_value(
			"SELECT AUTO_INCREMENT
			FROM `information_schema`.`tables`
			WHERE
				`table_name`=? AND
				`table_schema`=?",
			array(
				$table,
				$this->name
			)
		);
	}
	# Perform Query
	public function query($sql,$values=NULL,$clense=true,$echo=NULL){
		$this->last_id=0;
		$this->rows_updated=0;
		$stmt=$this->db->prepare($sql);
		if(!$stmt){
			$e=$this->db->errorInfo();
			$this->error('SQL',$e[2],__FILE__,__LINE__);
			echo "<strong>SQL ERROR</strong>: ".$e[2]."<br>";
			$echo=1;
		}else{
			if($clense){
				$this->cleanInput($values);
			}
			if(!is_array($values)){
				$values=array($values);
			}
			if($values){
				$values=array_values($values);
			}
			if(!$stmt->execute($values)){
				echo '<strong>PDO Excecute ERROR:</strong><br>';
				$error=1;
			}
			$status=$stmt->errorInfo();
			if($status[0]>0){
				$this->error('PDO',$status[2],__FILE__,__LINE__);
				echo "<strong>SQL ERROR: <em>{$status[2]}</em></strong><br>";
				$error=1;
			}
			$this->last_id=$this->db->lastInsertId();
			$this->rows_updated=$stmt->rowCount();
			if($echo || $error){
				$sqle=$sql;
				if(strpos($sqle,'?')){
					for($i=0;$i<sizeof($values);$i++){
						$qm=strpos($sqle,'?',$i);
						$sqle=substr($sqle,0,$qm).'\''.$values[$i].'\''.substr($sqle,$qm+1);
					}
				}else{
					$sqle=$sql;
				}
				echo '<strong>Original SQL:</strong> '.$sql.'<br>
				<strong>Formed SQL:</strong> '.$sqle.'<br>';
				if($error){
					print_pre(debug());
				}
			}
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	#Restore
	public function restore($files){
		foreach((array) $files as $file){
			$this->query(file_get_contents($file));
		}
	}
	# Result Count
	public function result_count($from_where_sql,$values=NULL,$echo=false){
		if(!is_array($values)){
			$values=array($values);
		}
		$out=$this->query("SELECT COUNT(*) as `size` $from_where_sql",$values,0,$echo);
		return $out[0]['size'];
	}
	# Get rows affected by last query
	public function rows_updated(){
		return $this->rows_updated;
	}
	# Contact Subjects
	public function get_contact_subject($subject_id){
		$temp=$this->get_row("SELECT `subject` FROM `contact_subjects` WHERE `id`=?",$subject_id);
		return $temp['subject'];
	}
	public function get_contact_subjects(){
		if($temps=$this->query("SELECT * FROM `contact_subjects` ORDER BY `subject`")){
			foreach($temps as &$temp){
				$temp['staff']=json_decode($temp['staff'],1);
			}
			return $temps;
		}
	}
	public function get_contact_staff($subject){
		$staff=$this->query("SELECT `staff` FROM `contact_subjects` WHERE `id`=?",$subject);
		$staff=json_decode($staff[0]['staff'],1);
		if(is_array($staff)){
			$staff=implode(',',json_decode($staff[0]['staff'],1));
		}
		if($staff=$this->query("SELECT `first_name`,`last_name`,`email` FROM `users` WHERE `id` IN(".$staff.")")){
			return $staff;
		}
		return false;
	}
	public function put_contact_subject($subject,$staff){
		$this->query(
			"INSERT INTO `contact_subjects` (`subject`,`staff`) VALUES (?,?)",
			array(
				$subject,
				json_encode($staff,JSON_NUMERIC_CHECK)
			)
		);
		$app->set_message('success',"Updated details for '".$results['data']['company_name']."'");
		$app->log_message(3,'Company updated',"Updated details for '".$results['data']['company_name']."'");
	}
	public function update_contact_subject($id,$subject,$staff){
		$this->query(
			"UPDATE `contact_subjects`
			SET
				`subject`=?,
				`staff`=?
			WHERE `id`=?",
			array(
				$subject,
				json_encode($staff,JSON_NUMERIC_CHECK),
				$id
			)
		);
	}
}