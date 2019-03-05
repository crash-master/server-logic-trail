<?php

namespace Kernel\Console;

use Kernel\Door;

class ConsoleBack{
	protected static $routes = [];
	protected static $args_list = [];
	protected static $not_found_action;

	protected static function make_arguments_list($source_arguments){
		$args_list = [];
		$flag = false;
		$count = count($source_arguments);
		for($i=2; $i<$count; $i++){
			if(strpos($source_arguments[$i], '--') === 0){
				$arg_name = str_replace('--', '', $source_arguments[$i]);
				$args_list[$arg_name] = isset($source_arguments[$i + 1]) ? $source_arguments[$i + 1] : null;
			}
		}
		self::$args_list = $args_list;
		return $args_list;
	}

	protected static function get_command_name($source_arguments){
		return $source_arguments[1];
	}

	protected static function get_args_names_string($arguments_list){
		$count = count($arguments_list);
		if($count == 0){
			return '';
		}

		$tmp = [];
		foreach($arguments_list as $arg_name => $arg_val){
			$tmp[] = '$' . $arg_name;
		}

		return implode(';', $tmp);
	}

	protected static function routing(){
		global $argv;
		$arguments_list = self::make_arguments_list($argv);
		$command = self::get_command_name($argv);
		$args_names_string = self::get_args_names_string($arguments_list);
		$current_route = ($args_names_string == '') ? $command : $command . '::' . $args_names_string;
		if(isset(self::$routes[$current_route])){
			echo self::call(self::$routes[$current_route]);
		}else{
			echo self::call(self::$not_found_action);
		}
	}

	protected static function call($action){
		if(is_object($action)){
			return Door::knock_to_func($action, self::$args_list);
		}elseif(is_string($action) and strpos($action, '@') !== false){
			list($classname, $methname) = explode('@', $action);
			return Door::knock_to_class($classname, $methname, self::$args_list);
		}else{
			throw new \Exception("Incorrect route handler '{$action}'");
		}

		return null;
	}

	protected static function controller_name_formating($controller_name){
		if(strpos($controller_name, '\\') !== false){
			$arr = explode('\\', $controller_name);
			$controller_name = $arr[count($arr) - 1];
		}
		$controller_name = str_replace('Controller', '', $controller_name);
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $controller_name));
	}

	protected static function action_name_formating($action_name){
		$action_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $action_name)));
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $action_name));
	}
}