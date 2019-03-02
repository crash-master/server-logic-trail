<?php
namespace Kernel;

class Maker_{
	public static $params;

	public static function setMigration($params = NULL, $custom_path = false, $create_model_flag = false){
		global $SLT_APP_NAME;
		try{
			if(Config::get('system -> migration') != "on"){
				throw new Exception('Migration set to off in slt/config/main.config.php');
				return false;
			}
		}catch(Exception $e){
			exception($e);
		}

		if(is_null($params)){
			$params = self::$params;
		}

		$path_to_migration_file = !$custom_path ? $SLT_APP_NAME . '/migrations/'.$params[1].'Migration.php' : $custom_path.$params[1].'Migration.php';

		if(!file_exists($path_to_migration_file)){
			return false;
		}
		@include_once($path_to_migration_file);
		@call_user_func(array($params[1].'Migration','up'));
		
		if($create_model_flag){
			CodeTemplate::create('model', ['modelname' => $params[1], 'tablename' => $params[1], 'filename' => $params[1]]);
		}
		return true;
	}

	public static function setAllMigration(){
		$arr = self::getMigrationList();
		$count = count($arr);
		for($i=0;$i<$count;$i++){
			self::setMigration(array(1=>$arr[$i]['name']));
		}

		return true;
	}

	public static function unsetMigration($params = NULL, $custom_path = false){
		global $SLT_APP_NAME;
		try{
			if(Config::get('system -> migration') != "on"){
				throw new Exception('Migration set to off in slt/config/main.config.php');
				return false;
			}
		}catch(Exception $e){
			exception($e);
		}

		if(is_null($params)){
			$params = self::$params;
		}

		$path_to_migration_file = !$custom_path ? $SLT_APP_NAME . '/migrations/'.$params[1].'Migration.php' : $custom_path.$params[1].'Migration.php';

		if(!file_exists($path_to_migration_file)){
			return false;
		}

		@include_once($path_to_migration_file);
		@call_user_func(array($params[1].'Migration','down'));

		return true;
	}

	public static function unsetAllMigration(){
		$arr = self::getMigrationList();
		$count = count($arr);
		for($i=0;$i<$count;$i++){
			if(self::issetTable($arr[$i]['name'])){
				self::unsetMigration(array(1=>$arr[$i]['name']));
			}
		}
		return true;
	}

	public static function refreshMigration(){
		if(self::unsetAllMigration() and self::setAllMigration()){
			return true;
		}
		return false;
	}

	private static function issetTable($tablename){
		$tables = DBIO::getTableList();
		$count = count($tables);
		for($i=0;$i<$count;$i++){
			if($tablename == $tables[$i]){
				return true;
			}
		}

		return false;
	}

	public static function getMigrationList(){
		global $SLT_APP_NAME;
		$list = IncludeControll::scan('./' . $SLT_APP_NAME . '/migrations/');
		
		$count = count($list);
		$res = [];
		for($i=0;$i<$count;$i++){
			list($name) = explode('Migration', basename($list[$i]));
			$res[] = ['name' => $name, 'path' => $list[$i]];
		}

		return $res;
	}

	private static function getPathToMigrationFileOnName($name){
		$migrations = self::getMigrationList();
		$count = count($migrations);
		for($i=0;$i<$count;$i++){
			if($migrations[$i]['name'] == $name){
				return $migrations[$i]['path'];
			}
		}

		return false;
	}   

}
