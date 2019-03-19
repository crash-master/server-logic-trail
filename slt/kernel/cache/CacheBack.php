<?php
namespace Kernel\Cache;

class CacheBack{
	protected static $cache_directory = 'tmp/cache';
	protected static $cache_life = 3; // days

	protected static function clear_cache_if_not_relevant($path_to_cache){
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
}