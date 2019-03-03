<?php

namespace Kernel;

class Door{
	public static function knock_to_class($class_name, $method_name, $args, $callback = null){
		$class_controller = \call_user_func([$class_name, 'ins']);
		$reflectionMethod = new \ReflectionMethod($class_name, $method_name);
		$methParams = $reflectionMethod -> getParameters();
		$params = [];
		$count = count($methParams);

		for($i=0;$i<$count;$i++){
			if(isset($args[$methParams[$i] -> name])){
				$params[] = $args[$methParams[$i] -> name];
			}
		}
		if(!is_null($callback)){
			$callback($class_name, $method_name, $params);
		}

		return \call_user_func_array([$class_controller, $method_name], $params);
	}

	public static function knock_to_func($func, $args, $callback = null){
		$reflectionFunction = new \ReflectionFunction($func);
		$funcParams = $reflectionFunction -> getParameters();
		$params = [];
		$count = count($funcParams);

		for($i=0;$i<$count;$i++){
			if(isset($args[$funcParams[$i] -> name])){
				$params[] = $args[$funcParams[$i] -> name];
			}
		}
		if(!is_null($callback)){
			$callback($func, $params);
		}

		return $reflectionFunction -> invokeArgs($params);
	}
}