<?php
namespace Kernel\Services;

class SingletonPattern{
	private static $instance = [];

	public static function ins(){
		$classname = get_called_class();
		if(!isset(self::$instance[$classname])){
			self::$instance[$classname] = new $classname();
		}

		return self::$instance[$classname];
	}
}