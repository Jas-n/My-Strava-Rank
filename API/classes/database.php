<?php class database{
	protected $user	='Jas-n_strava';
	protected $pass	='4Xdei@17';
	protected $name	='Jas-n_strava';
	private $last_id;
	private $rows_updated;
	protected $db;
	public function __construct(){
		$this->db=$this->con();
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
	protected function con(){
		try{
			$db=new PDO('mysql:dbname='.$this->name.';host=localhost',$this->user,$this->pass);
			$db->query("SET NAMES UTF8");
			return $db;
		}catch(PDOException $e){
			echo '<h3>Error Connecting to Database!</h3>'.$e->getMessage();
			exit;
		}
	}
	public function database_name(){
		return $this->name;
	}
	# Stores Error
	public function error($type='PHP',$message,$file,$line,$severity=NULL,$data=NULL){
		if(!$data){
			$data=debug();
		}
		echo '<br><strong>Error: </strong>'.$message;
		print_pre($data);
	}
	# Get table columns
	# 30/01/2017 15:44
	public function get_columns($table){
		if($columns=$this->query("SHOW COLUMNS FROM `".$table."`")){
			foreach($columns as $column){
				$column=array_change_key_case($column);
				$column['type']=strtoupper($column['type']);
				if($column['null']=='NO'){
					$column['null']=0;
				}else{
					$column['null']=1;
				}
				if($column['key']=='PRI'){
					$column['key']='primary';
				}
				if($column['extra']=='auto_increment'){
					$column['auto_increment']=1;
				}else{
					$column['auto_increment']=0;
				}
				$field=$column['field'];
				unset(
					$column['extra'],
					$column['field']
				);
				ksort($column);
				$return[$field]=$column;
			}
			return $return;
		}
		return false;
	}
	# Get Logs
	public function get_logs($level=NULL){
		if($level!==NULL){
			$where='WHERE `level`=?';
			$options[]=$level;
		}
		return array(
			'count'	=>$this->result_count("FROM `logs` $where"),
			'logs'	=>$this->query('
				SELECT
					`logs`.*,
					CONCAT(`users`.`first_name`," ",`users`.`last_name`) as `name`
				FROM `logs`
				LEFT JOIN `users`
				ON `logs`.`user_id`=`users`.`id`
				'.$where.'
				ORDER BY
					`date` DESC,
					`id` DESC
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
	# Generate placeholder for IN()
	public function in_placeholder($in_values){
		$in_values=(array) $in_values;
		return implode(',',array_pad([],sizeof($in_values),'?'));
	}
	# Get last insert id
	public function insert_id(){
		return $this->last_id;
	}
	public function login(){
		return false;
	}
	# Get settings for modules
	public function module_settings($module_id){
		if($temp=$this->query("SELECT `name`,`value` FROM `settings` WHERE `module`=?",$module_id)){
			foreach($temp as $setting){
				$out[$setting['name']]=$setting['value'];
			}
			return $out;
		}
		return false;
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
	# Generate random hex based on current database values
	# Updated 15/03/2017 15:08
	public function next_hex_id($table,$column='id',$length=6,$prefix=''){
		$cols=array_keys($this->get_columns($table));
		if(
			!$cols ||
			!in_array($column,$cols)
		){
			return false;
		}
		$hex_id=$prefix.strtoupper(str_pad(base_convert(mt_rand(1,(32**$length-1)),10,32),$length,0,STR_PAD_LEFT));
		while($this->get_value("SELECT `".$column."` FROM `".$table."` WHERE `".$column."`=?",$hex_id)){
			$hex_id=$prefix.strtoupper(str_pad(base_convert(mt_rand(1,(32**$length-1)),10,32),$length,0,STR_PAD_LEFT));
		}
		return $hex_id;
	}
	# Perform Query
	public function query($sql,$values=NULL,$clense=true,$echo=NULL){
		$this->last_id=0;
		$this->rows_updated=0;
		$stmt=$this->db->prepare($sql);
		if(!$stmt){
			$e=$this->db->errorInfo();
			$error_message=$e[2];
			$this->error('SQL',$error_message,__FILE__,__LINE__,NULL,array('sql'=>$sql,'values'=>$values,'debug'=>debug()));
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
				$error=1;
			}
			$status=$stmt->errorInfo();
			if($status[0]!=0 || $error){
				$error_message=$status[2];
				$this->error('PDO',$error_message,__FILE__,__LINE__,NULL,array('sql'=>$sql,'values'=>$values,'debug'=>debug()));
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
				$print_pre=array('from'=>$error?'error':'echo');
				if($error){
					$print_pre['error']=$error_message;
				}
				$print_pre['original']=$sql;
				$print_pre['formatted']=$sqle;
				$print_pre['debug']=debug();
				print_pre($print_pre);
			}
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	# Result Count
	public function result_count($from_where_sql,$values=NULL,$echo=false){
		if(!is_array($values)){
			$values=array($values);
		}
		$out=$this->query("SELECT COUNT(*) as `size` $from_where_sql",$values,0,$echo);
		if(sizeof($out)==1){
			return $out[0]['size'];
		}
		# For group by that can return multiple sizes
		return sizeof($out);
	}
	# Get rows affected by last query
	public function rows_updated(){
		return $this->rows_updated;
	}
	# Check database for existing slug
	public function slug($table,$column,$value,$not_equal_column=false,$not_equal_value=false){
		$cols=array_keys($this->get_columns($table));
		if(
			!$cols ||
			!in_array($column,$cols) ||
			($not_equal_column && !in_array($not_equal_column,$cols))
		){
			return false;
		}
		if($not_equal_column){
			
		}
		$base=trim(slug($value));
		$i=0;
		$values=array(
			'slug'=>$base
		);
		if($not_equal_column){
			$values[]=$not_equal_value;
		}
		while($this->get_value(
			"SELECT `".$column."`
			FROM `".$table."`
			WHERE
				`".$column."`=?".
				($not_equal_column?" AND `".$not_equal_column."` <> ?":""),
			$values
		)){
			$values['slug']=$base.'_'.++$i;
		}
		return $values['slug'];
	}
	public function column_exists($table,$column,$as_sql=false){
		$sql="SELECT 1
		FROM `information_schema`.`columns`
		WHERE
			`table_schema`	='".addslashes($this->name)."' AND
			`table_name`	='".addslashes($table)."' AND
			`column_name`	='".addslashes($column)."'";
		if($as_sql){
			return $sql;
		}
		return !!$this->get_value($sql);
	}
	public function table_exists($table,$as_sql=false){
		$sql="SELECT 1
		FROM `information_schema`.`tables`
		WHERE `table_schema`= '".addslashes($this->name)."' AND `table_name`='".\addslashes($table)."'";
		if($as_sql){
			return $sql;
		}
		return !!$this->get_value($sql);
	}
	# Validates/updates databases to match definitions in $table
	# 30/01/2017 17:31
	public function validate($tables){
		foreach($tables as $table=>$table_data){
			if(!$table_data['columns']){
				continue;
			}
			foreach($table_data['columns'] as $column=>&$column_data){
				if(!array_key_exists('auto_increment',$column_data)){
					$column_data['auto_increment']=0;
				}
				if(!array_key_exists('default',$column_data)){
					$column_data['default']='';
				}
				if(!array_key_exists('key',$column_data)){
					$column_data['key']='';
				}
				if(!array_key_exists('null',$column_data)){
					$column_data['null']=0;
				}
				$column_data['type']=strtoupper($column_data['type']);
				ksort($table_data['columns'][$column]);
			}
			unset(
				$column,
				$column_data
			);
			$table=slug($table);
			if(sizeof($this->query("SHOW TABLES LIKE ?",$table))==0){
				# Create
				foreach($table_data['columns'] as $column=>$column_data){
					$column=slug($column);
					if(!$this->validate_field($column_data)){
						$core->set_message('error','Field <strong>'.$column.'</strong> is not valid.');
						return false;
					}
				}
				foreach($table_data['columns'] as $column=>$column_data){
					$column=slug($column);
					$create_fields[]="\r\n\t`".$column."` ".$column_data['type'].($column_data['null']?" ":" NOT ")."NULL".($column_data['default']?" DEFAULT '".$column_data['default']."'":"").($column_data['auto_increment']?" AUTO_INCREMENT":"").($column_data['comment']?" COMMENT '".addslashes($column_data['comment'])."'":"");
					if($column_data['key']=='primary'){
						$primary_keys[]=$column;
					}
				}
				$sql="CREATE TABLE `".$table."` (".
					implode(',',$create_fields);
					if($primary_keys){
						$sql.=",\r\n\tPRIMARY KEY(`".implode("`,`",$primary_keys)."`)";
					}
				$sql.="\r\n)\r\nENGINE=InnoDB DEFAULT CHARSET=utf8".($auto_increment?' AUTO_INCREMENT='.$auto_increment[key($auto_increment)]:"").($table_data['comment']?" COMMENT '".addslashes($table_data['comment'])."'":"");
				$this->query($sql);
			}
			else{
				# Update
				$cols=$this->get_columns($table);
				foreach($table_data['columns'] as $column=>$column_data){
					$column=slug($column);
					if(!$this->validate_field($column_data)){
						return false;
					}
					# Update if it exists
					if($cols[$column]){
						
					}
					# Rename if if doesn't exist and 'renamed' does
					elseif($column_data['renamed'] && $cols[$column_data['renamed']]){
						$this->query("ALTER TABLE `".$table."` CHANGE `".$column_data['renamed']."` `".$column."` ".$column_data['type']);
						unset($cols[$column_data['renamed']]);
					}
					# Insert
					else{
						$this->query("ALTER TABLE `".$table."` ADD COLUMN `".$column."` ".$column_data['type'].($column_data['null']?" ":" NOT ")."NULL".($column_data['default']?" DEFAULT '".$column_data['default']."'":"").($column_data['auto_increment']?" AUTO_INCREMENT":"").($column_data['comment']?" COMMENT '".addslashes($column_data['comment'])."'":"").(!$prev?" FIRST":" AFTER `".$prev."`"));
					}
					# Remove from cols so we can delete the leftovers
					unset($cols[$column]);
					$prev=$column;
				}
				if($cols){
					# Drop columns
					foreach($cols as $column=>$null){
						$this->query("ALTER TABLE `".$table."` DROP COLUMN `".$column."`");
					}
				}
			}
		}
	}
	private function validate_field($field){
		if(!is_array($field) || !$field['type']){
			return false;
		}
		return true;
	}
}