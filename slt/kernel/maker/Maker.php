<?php

namespace Kernel\Maker;

use \Kernel\Services\RecursiveScan;
use \Kernel\Config;

class Maker extends MakerBack implements MakerInterface{
	public static function migration_exists($migration_name, $path_to_migration_dir = null){
		$migration_class = $migration_name . 'Migration';

		$migration_file = self::get_path_to_migration_file($migration_class, $path_to_migration_dir);

		if(!file_exists($migration_file)){
			throw new \Exception("File '{$migration_file}' does not exists");
		}
	}

	public static function migration_up($migration_name, $path_to_migration_dir = null, $with_model = false){
		return self::migration('up', $migration_name, $path_to_migration_dir, $with_model);
	}

	public static function migration_down($migration_name, $path_to_migration_dir = null, $with_model = false){
		return self::migration('down', $migration_name, $path_to_migration_dir, $with_model);
	}

	public static function migration_refresh($migration_name, $path_to_migration_dir = null, $with_model = false){
		return self::migration_down($migration_name, $path_to_migration_dir, $with_model) and 
			self::migration_up($migration_name, $path_to_migration_dir, $with_model);
	}

	public static function migration_up_all($path_to_migration_dir = null, $with_models = false){
		return self::migration_all('up', $path_to_migration_dir, $with_models);
	}

	public static function migration_down_all($path_to_migration_dir = null, $with_models = false){
		return self::migration_all('down', $path_to_migration_dir, $with_models);
	}

	public static function migration_refresh_all($path_to_migration_dir = null, $with_models = false){
		return self::migration_down_all($path_to_migration_dir, $with_models) and self::migration_up_all($path_to_migration_dir, $with_model);
	}

	public static function migrations_list($path_to_migration_dir = null){
		return parent::migrations_list($path_to_migration_dir);
	}
}