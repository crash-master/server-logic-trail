<?php

namespace Kernel\Router;

use Kernel\Cache;
use Kernel\Request;
use Kernel\Door;
use Kernel\Events;

class RouterBack{
	protected static $data;
	protected static $viewfunc;
	protected static $action404;
	protected static $url;
	protected static $post;
	protected static $request_args;
	protected static $post_flag;
	public static $cache_flag;
	public static $init_flag = false;
	public static $do_cache_flag = false;

	protected static function set_data_from_cache(){
		$cache = Cache::get('routes.map');
		self::$data = $cache['data'];
		self::$action404 = $cache['action404'];
		self::$post = $cache['post'];
		return true;
	}

	protected static function caching(){
		if(!self::$do_cache_flag){
			return false;
		}
		$data = [];
		foreach(self::$data as $key => $val){
			if(!is_string($val)){
				continue;
			}
			$data[$key] = $val;
		}

		Cache::set('routes.map', [
			'data' => $data,
			'action404' => self::$action404,
			'post' => self::$post
		]);
	}

	protected static function compareUrl($url,$route){
		$url = explode('/',$url);
		$route = explode('/',$route);
		$count = count($url);
		if($count != count($route))
			return false;
		for($i=0;$i<$count;$i++){
			if($url[$i] != $route[$i] and $route[$i] != '*' and strpos($route[$i],'{') === false){
				return false;
			}
		}
		return true;
	}

	protected static function compareAll(){
		$count = count(self::$data);
		$routes = array_keys(self::$data);
		for($i=0;$i<$count;$i++){
			if(self::compareUrl(self::$url, $routes[$i])){
				self::$url = $routes[$i];
				self::$request_args = Request::getArgs($routes[$i]);
				return true;
			}
		}
		return false;
	}

	protected static function routing($view){
		if(isset(self::$data[self::$url]) or self::compareAll()){
			$url = self::$url;
			$f_name = self::$data[$url];
			
			if(is_object($f_name) or strpos($f_name,'@') === false){
				$res = self::callFunc($f_name);
				Events::register('worked_action', [
					'action' => $f_name,
					'result' => $res
				]);
				$view($res);
				return true;
			}
			
			$arr = explode('@',$f_name);  
			$res = self::call($arr[0], $arr[1]);
			Events::register('worked_action', [
				'controller' => $arr[0],
				'action' => $arr[1],
				'result' => $res
			]);

			$view($res);

		}else{  // action404
			if(self::$post_flag){
				return true;
			}
			$f_name = self::$action404;
			
			$ev_params = [
				'uri' => $_SERVER['REQUEST_URI'],
				'method' => 'get'
			];
			
			if(strpos($f_name,'@') !== false){
				$action_404 = explode('@', $f_name);
				$ev_params = array_merge($ev_params, [
					'controller' => $action_404[0],
					'action' => $action_404[1]
				]);
			}else{
				$ev_params = array_merge($ev_params, [
					'action' => $f_name
				]);
			}
			
			Events::register('route_not_found', $_SERVER['REQUEST_URI']);

			if(strpos($f_name,'@') === false){
				$r = $f_name();
				Events::register('worked_action', [
					'action' => $f_name,
					'result' => $r
				]);

				$view($r);
				return true;
			}
			
			$res = self::call($action_404[0], $action_404[1]);

			Events::register('worked_action', [
				'controller' => $action_404[0],
				'action' => $action_404[1],
				'result' => $res
			]);

			$view($res);
		}

		return true;
	}

	protected static function call($classname, $methname){   
		return Door::knock_to_class($classname, $methname, self::$request_args, function($class_name, $meth_name, $params){
			Events::register('call_action', [
				'controller' => $class_name,
				'action' => $meth_name,
				'params' => is_array($params) ? $params : NULL,
				'method' => 'get'
			]);
		});
	}
	
	protected static function callFunc($funcname){
		return Door::knock_to_func($funcname, self::$request_args, function($func, $params){
			Events::register('call_action', [
				'action' => $funcname,
				'params' => count($params) ? $params : NULL,
				'method' => 'get'
			]);
		});
	}

	protected static function controller_name_to_route_link($controller){
		if(strpos($controller, '\\') !== false){
			$arr = explode('\\', $controller);
			$controller = $arr[count($arr) - 1];
		}
		$controller = str_replace('Controller', '', $controller);
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $controller));
	}

	protected static function action_name_to_route_link($action){
		$action = str_replace(' ', '', ucwords(str_replace('_', ' ', $action)));
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $action));
	}

	protected static function generate_route_from_action($action_string){
		// get controller and action names from string
		list($controller, $action) = explode('@', $action_string, 2);
		$controller_for_route = self::controller_name_to_route_link($controller);
		$action_for_route = self::action_name_to_route_link($action);
		// make route
		$route = '/' . $controller_for_route . '/' . $action_for_route;
		if(!class_exists($controller)){
			throw new \Exception("Class {$classname} not exists");
		}
		$reflectionMethod = new \ReflectionMethod($controller, $action);
		$methParams = $reflectionMethod -> getParameters();

		foreach($methParams as $name){
			$name = trim(ltrim(rtrim(strstr($name, '$'), ']'), '$'));
			$route .= '/' . $name . '/{' . $name . '}';
		}

		return $route;
	}
}