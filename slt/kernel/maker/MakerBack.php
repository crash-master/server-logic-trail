<?php

namespace Kernel\Maker;

use \Kernel\Services\RecursiveScan;

class MakerBack{
	private static function get_default_path_to_migration_dir(){
		global $SLT_APP_NAME;
		return $SLT_APP_NAME . '/migrations';
	}

	private static function get_path_to_migration_file($migration_class, $path_to_migration_dir){
		$path_to_migration_dir = is_null($path_to_migration_dir) ? self::get_default_path_to_migration_dir() : $path_to_migration_dir;
		$path_to_migration_dir = ($path_to_migration_dir[strlen($path_to_migration_dir) - 1] != '/') ? $path_to_migration_dir . '/' : $path_to_migration_dir;
		return $path_to_migration_dir . $migration_class . '.php';
	}

	private static function migration($migration_method, $migration_name, $path_to_migration_dir = null, $with_model = false){
		if(Config::get() -> system -> migration != "on"){
			throw new \Exception('Migration set to off in slt/config/main.config.php');
		}

		global $SLT_APP_NAME;

		$migration_class = $migration_name . 'Migration';

		$migration_file = self::get_path_to_migration_file($migration_class, $path_to_migration_dir);

		if(!file_exists($migration_file)){
			throw new \Exception("File '{$migration_file}' does not exists");
		}

		if(!class_exists($migration_class)){
			include_once($migration_file);
		}

		if($with_model and $migration_method == 'up'){
			CodeTemplate::create('model', ['filename' => $migration_name, 'modelname' => $migration_name, 'tablename' => $migration_name], false, $SLT_APP_NAME . '/models/');
		}elseif($with_model and $migration_method == 'down'){
			$path_to_model = $SLT_APP_NAME . '/models/' . $migration_name . '.php';
			$path_to_model_remove = $SLT_APP_NAME . '/models/.removed.' . $migration_name . '.php';
			if(file_exists($path_to_model)){
				rename($path_to_model, $path_to_model_remove);
			}
		}

		return (new $migration_class()) -> $migration_method();
	}

	private static function migration_all($migration_method, $path_to_migration_dir = null, $with_models = false){
		$migrations = self::migrations_list($path_to_migration_dir);
		foreach($migrations as $migration){
			self::migration($migration_method, $migration['name'], $path_to_migration_dir, $with_model);
		}

		return true;
	}
}