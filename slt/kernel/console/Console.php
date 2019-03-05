<?php

namespace Kernel\Console;

class Console extends ConsoleBack implements ConsoleInterface{
	public static function routing(){
		parent::routing();
	}

	public static function route($command, $action){
		self::$routes[$command] = $action;
		return true;
	}

	public static function not_found($action){
		self::$not_found_action = $action;
		return true;
	}

	public static function action($action){
		if(!is_string($action) or strpos($action, '@') === false){
			throw new \Exception("Action must be string in format 'class@method'");
		}
		list($classname, $methname) = explode('@', $action);
		if(!class_exists($classname)){
			throw new \Exception("Class {$classname} not exists");
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
		return;
	}

	public static function get_routes_map(){
		return self::$routes;
	}
}