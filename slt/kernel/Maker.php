<?php
namespace Kernel;

class Maker{
	public static $params;

	public static function setMigration($params = NULL){
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
		@include_once('app/migrations/'.$params[1].'Migration.php');
		@call_user_func(array($params[1].'Migration','up'));
		
		CodeTemplate::create('set', ['tablename' => $params[1], 'setname' => $params[1], 'filename' => $params[1].'Set']);
		CodeTemplate::create('model', ['modelname' => $params[1], 'setname' => $params[1], 'filename' => $params[1]]);
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

	public static function unsetMigration($params = NULL){
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

		$migrationPath = self::getPathToMigrationFileOnName($params[1]);

		@include_once($migrationPath);
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
		$list = IncludeControll::scan('./app/migrations/');
		
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
