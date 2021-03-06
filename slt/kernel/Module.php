<?php
namespace Kernel;

class Module{
	
	public static $modules;
	
	public static function register($name){
		try{
			if(isset(self::$modules[$name])){
				throw new Exception("Module with the name '{$name}' already exists");
				return false;
			}
		}catch(Exception $e){
			exception($e);
		}
		
		$classname = $name;
		$name = '\\Modules\\' . $name . '\\' . $name;
		self::$modules[$classname] = new $name();
	}
	
	public static function includesAllModules(){
		$dirs = Config::get('system -> modules');
		$count = count($dirs); 
		for($i=0;$i<$count;$i++){
			$pathToIndex = 'slt/modules/'.$dirs[$i].'/index.php';
			if(!file_exists($pathToIndex)){
				throw new \Exception("In module '{$dirs[$i]}' not exists file index.php");
				continue;
			}
			
			include_once($pathToIndex);
		}
		
		return true;  
	}
	
	public static function pathToModulesDir(){
		return 'slt/modules/';
	}
	
	public static function pathToModule($name){
		return self::pathToModulesDir() . $name . '/';
	}
	
	public static function get($name){
		return self::$modules[$name];
	}
	
}