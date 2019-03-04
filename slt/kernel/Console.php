<?php

namespace Kernel;

class Console{
	private static $routes = [];
	private static $args_list = [];
	private static $not_found_action;

	private static function make_arguments_list($source_arguments){
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

	private static function get_command_name($source_arguments){
		return $source_arguments[1];
	}

	private static function get_args_names_string($arguments_list){
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

	public static function routing(){
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

	public static function route($command, $action){
		self::$routes[$command] = $action;
		return true;
	}

	public static function not_found($action){
		self::$not_found_action = $action;
		return true;
	}

	private static function call($action){
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

	public static function action($action){
		if(!is_string($action) or strpos($action, '@') === false){
			throw new \Exception("Action must be string in format 'class@method'");
		}
		list($classname, $methname) = explode('@', $action);
		if(!class_exists($classname)){
			IncludeControll::load_one_controller($classname);
		}
		$reflection_meth = new \ReflectionMethod($classname, $methname);
		$params = $reflection_meth -> getParameters();
		$meth_arguments = [];
		foreach($params as $param){
			$meth_arguments[] = '$' . $param -> name;
		}
		$meth_arguments_string = implode(';', $meth_arguments);

		$action_name = self::controller_name_formating($classname) . '.' . self::action_name_formating($methname);
		self::route($action_name . '::' . $meth_arguments_string, $action);
		return true;
	}

	private static function controller_name_formating($controller_name){
		if(strpos($controller_name, '\\') !== false){
			$arr = explode('\\', $controller_name);
			$controller_name = $arr[count($arr) - 1];
		}
		$controller_name = str_replace('Controller', '', $controller_name);
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $controller_name));
	}

	private static function action_name_formating($action_name){
		$action_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $action_name)));
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $action_name));
	}

	public static function get_routes_map(){
		return self::$routes;
	}
}
