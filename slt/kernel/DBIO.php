<?php
namespace Kernel;

// DBIO - v2.0 (Connect v4.0))


class Connect{
	public static $config;
	public static $countQuery;
	public static $connect;
	private static $lastSqlQueryString;
	
	public static function get_lastSqlQueryString(){
		return self::$lastSqlQueryString;
	}

	public static function start(){
		if(!is_null(self::$config)){
			return false;
		}
		self::$config = Config::get('system -> DB');
		self::open_connect();
		self::$countQuery = 0;
		return true;
	}
	
	private static function open_connect(){    
		$dsn = self::$config['dbtype'] . ':host=' . self::$config['host'] . ';dbname=' . self::$config['dbname'] .';charset=' . self::$config['charset'];
		
		$opt = array(
			\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
		);
		
		self::$connect = new \PDO($dsn, self::$config['user'], self::$config['password'], $opt);
		
		try{
			if(!self::$connect){
				throw new Exception('Error connect to DB');
				return false;
			}
		}catch(Exception $e){
			exception($e);
		}

		return true;
	}
	
	public static function close_connect(){
		return true;
	}
	
	public static function query($sql){
		self::$lastSqlQueryString = $sql;
		Events::register('ready_sql_query_string', $sql);
		$result = strpos($sql, 'SELECT') !== false ? self::$connect -> query($sql) -> fetchAll(\PDO::FETCH_ASSOC) : self::$connect -> query($sql);
		$result = strpos($sql, 'INSERT') !== false ? self::$connect -> lastInsertId() : $result;
		
		self::$countQuery++;   
		Events::register('response_from_db', [
			'sql_query_string' => $sql,
			'response' => $result
		]);  

		return $result;
	}

	public static function getTableList(){
		$sql = 'SHOW TABLES';
		return self::query($sql) -> fetchAll(\PDO::FETCH_ASSOC);
	}
}

class DBIO{
	
	public static function start(){
		Connect::start();
		return true;
	}
	
	public static function end(){
		Connect::close_connect();
		return true;
	}

	public static function get_last_sql_query_string(){
		return Connect::get_lastSqlQueryString();
	}
	
	
	public static function select($params){ // $table - string, $rows - array, $where - array, $limit - array(from,count), $sort - DESC || ASC, $many - true || false) || $sql - string
		if(!isset($params['table'])){
			return false;
		}
		if(!isset($params['rows']) or is_null($params['rows'])){
			$params['rows'] = '*';
		}else{
			$count = count($params['rows']);
			for($i=0;$i<$count;$i++){
				$params['rows'][$i] = addslashes($params['rows'][$i]);
			}
			$params['rows'] = implode('`,`',$params['rows']);
			$params['rows'] = '`'.$params['rows'].'`';
		}

		$sql = 'SELECT '.$params['rows'].' FROM `'.addslashes($params['table']).'`';
		$sql2 = '';
		if(isset($params['where']) and is_array($params['where'])){
			$sql2 .= self::arrToSqlWhere($params['where']);
		}
		
		if(isset($params['order']) and is_array($params['order']))
			$sql2 .= ' ORDER BY `'.addslashes($params['order'][0]).'` '.addslashes($params['order'][1]);
		if(isset($params['limit']) and is_array($params['limit']))
			$sql2 .= ' LIMIT '.addslashes($params['limit'][0]).','.addslashes($params['limit'][1]);
		$sql .= $sql2;
		
		return self::fq($sql);
	}
	
	public static function arrToSqlWhere($where){
		$sql = '';
		$count = count($where);
		$sql .= ' WHERE ';
		if($count > 3){
			for($i=0;$i<$count;$i += 3){
				if($where[$i+1] == 'IN'){
					if(is_array($where[$i+2])){
						if(!count($where[$i+2])){
							$where[$i+2][] = 0;
						}
						$where[$i+2] = '(' . implode(',', $where[$i+2]) . ')';
					}
					$sql .= '`'.addslashes($where[$i]).'`'.$where[$i+1].' '.$where[$i+2].' ';
				}else{
					$sql .= '`'.addslashes($where[$i]).'`'.$where[$i+1].'\''.addslashes($where[$i+2]).'\'';
				}

				if(isset($where[$i + 3])){
					$sql .= ' ' . strtoupper(addslashes($where[$i + 3])) . ' ';
					$i++;
				}
			}
		}else{
			if($where[1] == 'IN'){
				if(is_array($where[2])){
					if(!count($where[2])){
						$where[2][] = 0;
					}
					$where[2] = '(' . implode(',', $where[2]) . ')';
				}
				$sql .= '`'.addslashes($where[0]).'`'.$where[1].' '.$where[2].' ';
			}else{
				$sql .= '`'.addslashes($where[0]).'`'.$where[1].'\''.addslashes($where[2]).'\'';
			}
		}

		return $sql;
	}
	
	public static function update($params){
		if(!is_array($params)){
			$sql = $params;
		}else{
			if(!isset($params['table']) or ((!isset($params['rows']) or !isset($params['rowsdata'])) and !isset($params['data']))){
				return false;
			}
			if(isset($params['data'])){
				$params['rows'] = array_keys($params['data']);
				$params['rowsdata'] = array_values($params['data']);
			}
			$sql = 'UPDATE `'.$params['table'].'` SET ';
			$count = count($params['rows']);
			if($count != count($params['rowsdata'])){
				return false;
			}
			for($i=0;$i<$count;$i++){
				if($i) $sql .= ',';
				if($params['rowsdata'][$i] != 'NOW()'){
					$sql .= '`'.addslashes($params['rows'][$i]).'`=\''.addslashes($params['rowsdata'][$i]).'\'';
				}else{
					$sql .= '`'.addslashes($params['rows'][$i]).'`='.addslashes($params['rowsdata'][$i]);
				}
			}
			if(isset($params['where']) and is_array($params['where'])){
				$sql .= self::arrToSqlWhere($params['where']);
			}
		}
		return Connect::query($sql);
	}
	
	
	
	public static function insert($params){ // $rowsdata - array
		if(!is_array($params)){
			$sql = $params;
		}else{
			if(!isset($params['table']) or ((!isset($params['rows']) or !isset($params['rowsdata'])) and !isset($params['data']))){
				return false;
			}
			if(isset($params['data'])){
				$params['rows'] = array_keys($params['data']);
				$params['rowsdata'] = array_values($params['data']);
			}
			$sql = 'INSERT INTO `'.$params['table'].'`(';
			$count = count($params['rows']);
			if($count != count($params['rowsdata'])){
				return false;
			}
			for($i=0;$i<$count;$i++){
				if($i) $sql .= ',';
				$sql .= '`'.addslashes($params['rows'][$i]).'`';
			}
			$sql .= ') VALUES (';
			
			for($i=0;$i<$count;$i++){
				if($i) $sql .= ',';
				if($params['rowsdata'][$i] == 'NOW()')
					$sql .= 'NOW()';
				else
					$sql .= '\''.addslashes($params['rowsdata'][$i]).'\'';
			}
			
			$sql .= ')';
		}
		return Connect::query($sql);
	}
	
	
	
	public static function delete($params){
		if(!is_array($params)){
			$sql = $params;
		}else{
			if(!isset($params['table'])){
				return false;
			}
			$sql = 'DELETE FROM `'.$params['table'].'` ';

			if(isset($params['where']) and is_array($params['where'])){
				$sql .= self::arrToSqlWhere($params['where']);
			}
		}
		
		return Connect::query($sql);
	}
	
	public static function fq($sql){
		$res = Connect::query($sql);
		// if(is_array($res) and !isset($res[1])){
		// 	if(isset($res[0]))
		// 		return $res[0];
		// }
		return $res;
	}
	
	public static function create($params){ // доработать
		$table = array_keys($params);
		$table = $table[0];
		$rows = array_keys($params[$table]);
		$count = count($rows);
		$types = array();
		$default = array();
		$null = array();
		for($i=0;$i<$count;$i++){
			$tmp = array_keys($params[$table][$rows[$i]]);
			$types[$i] = $tmp[0];
			$tmp = array_keys($params[$table][$rows[$i]][$types[$i]]);
			$null[$i] = $tmp[0]; // $null = 1 or 0
			$default[$i] = $params[$table][$rows[$i]][$types[$i]][$null[$i]];
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,";
		for($i=0;$i<$count;$i++){
			$sql .= '`'.$rows[$i].'` '.$types[$i];
			if(empty($null[$i]))
				$sql .= ' NOT NULL';
			if(!empty($default[$i]) and $default[$i] != 'undef'){
				if($default[$i] == 'NULL')
					$sql .= ' DEFAULT '.$default[$i];
				else
					$sql .= ' DEFAULT \''.$default[$i].'\'';
			}
			$sql .= ',';
		}
		$sql .= 'PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		return DBIO::fq($sql);
	}
	
	
	
	public static function drop($table){
		if(empty($table))
			return false;

		return self::fq("DROP TABLE `{$table}`");
	}
	
	public static function columns($tablename){
		$q = Connect::$connect -> prepare("DESCRIBE `{$tablename}`");
		$q -> execute();
		return $q -> fetchAll(\PDO::FETCH_COLUMN);
	}
	
	public static function getTimeOfCreate($tablename){
		$res = self::getStatusOfTables();
		$count = count($res);
		for($i=0;$i<$count;$i++){
			if($res[$i]['Name'] == $tablename){
				return $res[$i]['Create_time'];
			}
		}
		return false;
	}
	
	public static function getStatusOfTables(){
		return self::fq('SHOW TABLE STATUS FROM `'.Connect::$config['dbname'].'`');
	}

	public static function getTableList(){
		return Connect::getTableList();
	}
	
	public static function getCountResults($tablename = false, $where = false){
		if(!$tablename)
			return false;
		
		$tablename = addslashes($tablename);
		$sql = "SELECT COUNT(*) FROM `{$tablename}`";
		
		if($where and is_array($where)){
			$sql .= self::arrToSqlWhere($where);
		}
		return self::fq($sql);
	}
	
	public static function truncate($tablename){
		return self::fq('TRUNCATE TABLE `'.$tablename.'`');
	}

	public static function table_exists($tablename){
		$table_list = Connect::getTableList();
		$key = 'Tables_in_' . Connect::$config['dbname'];
		foreach($table_list as $item){
			if($item[$key] == $tablename){
				return true;
			}
		}

		return false;
	}
}

