<?php

namespace Kernel;

class Cache{
	private static $cache_directory = 'tmp/cache';
	private static $cache_life = 3; // days

	public static function get_path_to_cache_file($file_name){
		return self::$cache_directory . '/' . $file_name . '.cache';
	}

	private static function clear_cache_if_not_relevant($path_to_cache){
		if(!file_exists($path_to_cache)){
			return false;
		}

		$cache_last_update = filemtime($path_to_cache);
		if(time() - $cache_last_update > self::$cache_life * 24 * 60 * 60){
			unlink($path_to_cache);
			return true;
		}

		return false;
	}

	public function autoclear_not_relevant_cache(){
		$rs = new Services\RecursiveScan();
		$cache_list = $rs -> get_files(self::$cache_directory);
		$clear_counter = 0;
		foreach($cache_list as $cache_file){
			if(self::clear_cache_if_not_relevant($cache_file)){
				$clear_counter++;
			}
		}

		return $clear_counter;
	}

	public static function set($name, $cache_data){
		Events::register('cache_data_create', ['cache_alias' => $name, 'cache_data' => $cache_data]);
		$file_path = self::get_path_to_cache_file($name);
		$data = serialize($cache_data);
		return file_put_contents($file_path, $data);
	}

	public static function exists($name){
		return file_exists(self::get_path_to_cache_file($name));
	}

	public static function get($name){
		$data = unserialize(file_get_contents(self::get_path_to_cache_file($name)));
		Events::register('cache_data_used', ['cache_alias' => $name, 'cache_data' => $data]);
		return $data;
	}

	public static function remove($name){
		return self::exists($name) ? unlink(self::get_path_to_cache_file($name)) : false;
	}

	public static function code($name, $code_in_func){
		global $SLT_DEBUG, $SLT_CACHE;
		if($SLT_DEBUG == 'off' and $SLT_CACHE == 'on'){
			if(self::exists($name)){
				return self::get($name);
			}
		}

		$code_result = $code_in_func();
		self::set($name, $code_result);
		return $code_result;
	}
}