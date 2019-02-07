<?php

namespace Kernel;

class Cache{
	private static $cache_directory = 'tmp/cache';

	public static function set($name, $cache_data){
		$file_name = $name . '.cache';
		$data = serialize($cache_data);
		return file_put_contents(self::$cache_directory . '/' . $file_name, $data);
	}

	public static function exists($name){
		$file_name = $name . '.cache';
		return file_exists(self::$cache_directory . '/' . $file_name);
	}

	public static function get($name){
		$file_name = $name . '.cache';
		return unserialize(file_get_contents(self::$cache_directory . '/' . $file_name));
	}
}