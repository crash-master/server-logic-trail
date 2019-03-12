<?php

namespace Kernel;

use Kernel\Services\RecursiveScan;

class Boot{
	private static $all_files;

	public static function autoloader(){
		spl_autoload_register(function($class){
			if(self::search_and_load($class)) return true;
		});
	}

	public static function search_and_load($class){
		$class = strtolower(str_replace('\\', '/', $class));
		$files = self::get_all_files();
		foreach($files as $file){
			if(strpos(strtolower($file), $class) !== false){
				include_once($file);
				return true;
			}
		}
	}

	private static function scan_project(){
		global $SLT_APP_NAME;
		include_once('slt/kernel/services/Services.php');
		include_once('slt/kernel/services/RecursiveScan.php');
		$rs = new RecursiveScan();
		self::$all_files = $rs -> get_files('./slt');
		self::$all_files = array_merge(self::$all_files, $rs -> get_files('./' . $SLT_APP_NAME));
	}

	public static function get_all_files(){
		if(is_null(self::$all_files)){
			self::scan_project();
		}
		
		return self::$all_files;
	}

	public static function load_always($dirs_and_files){
		foreach($dirs_and_files as $item){
			if(is_file($item)){
				include_once($item);
			}else{
				$rs = new RecursiveScan();
				$files = $rs -> get_files($item, false);
				foreach($files as $file){
					include_once($file);
				}
			}
		}
	}

}