<?php

namespace Kernel;

/**
 * Класс для вызова нужного обработчика маршрута.
 */

class Door{
	/**
	 * Вызов метода класса с передачей нужных параметров.
	 *
	 * @method knock_to_class
	 *
	 * @param  string $class_name Название класса
	 * @param  string $method_name Название метода
	 * @param  array $args Список аргументов в виде ассоциативного массива, которые будут переданы обработчику.
	 * @param  function $callback Не обязательный параметр, функция обратного вызова, будет вызвана перед вызовом обработчика.
	 * Перимущественно используется для получения данных о вызываемом обработчике.
	 *
	 * @return string Возвращает результат выполнения обработчика, тип данных зависит от разработчика метода - обработчика.
	 */
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

	/**
	 *  Вызов функции, обработчика с передачей нужных параметров.
	 *
	 * @method knock_to_func
	 *
	 * @param  string $func название функции или сама анонимная функция
	 * @param  string $args Список аргументов в виде ассоциативного массива, которые будут переданы обработчику.
	 * @param  function $callback Не обязательный параметр, функция обратного вызова, будет вызвана перед вызовом обработчика.
	 * Перимущественно используется для получения данных о вызываемом обработчике.
	 *
	 * @return string Возвращает результат выполнения обработчика, тип данных зависит от разработчика метода - обработчика.
	 */
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