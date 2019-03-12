<?php

namespace Kernel\Router;

use Kernel\Cache\Cache;
use Kernel\Request;
use Kernel\Events;
use Kernel\Door;

class Router extends RouterBack implements RouterInterface{
	public static function init(){
		if(self::$init_flag){
			return false;
		}
		self::$init_flag = true;
		global $SLT_DEBUG, $SLT_CACHE;
		self::$cache_flag = false;
		if($SLT_DEBUG == 'off'){
			if($SLT_CACHE == 'on'){
				if(Cache::exists('routes.map')){
					self::$cache_flag = true;
					self::set_data_from_cache();
					return true;
				}else{
					self::$do_cache_flag = true;
				}
			}
		}

		self::$do_cache_flag = true;

		return true;
	}

	public static function routing($view){
		parent::routing($view);
	}

	public static function run($view = false){
		self::init();
		self::caching();
		self::$url = Request::getUrl();
		if(!$view and self::$viewfunc){
			$view = self::$viewfunc;
		}elseif($view and !self::$viewfunc){
			self::$viewfunc = $view;
		}

		self::eventsPost($view);
		self::routing($view);

		return true;
	}

	public static function urlto($action_name, $params = NULL){
		$data = [];
		foreach(self::$data as $key => $val){
			if(!is_object($val))
				$data[$val] = $key;
		}
		
		if(!isset($data[$action_name])){
			$post = is_null(self::$post) ? [] : self::$post;
			foreach($post as $var_and_route => $action){
				if($action['action'] == $action_name){
					list(,$route) = explode(':', $var_and_route);
					return '/' . $route;
				}
			}
		}

		if(is_array($params)){
			$route = $data[$action_name];
			foreach($params as $key => $val){     
				$route = str_replace('{' . $key . '}', $val, $route);     
			}
			return '/' . $route;
		}
		
		return '/' . $data[$action_name];
	}

	public static function addRoute($arr){
		if(empty($arr['route']) or empty($arr['action'])){
			return false;
		}
		$arr['route'] = trim($arr['route'], '/');
		self::$data[$arr['route']] = $arr['action'];
		return true;
	}

	public static function _404($action){
		self::init();
		self::$action404 = $action;
		return false;
	}

	public static function getUrl(){
		return self::$url;
	}

	public static function eventsPost($view){
		$data = Request::post();
		$keys = array_keys($data);
		$count = count($data);
		for($i=0;$i<$count;$i++){
			if(isset(self::$post[$keys[$i]]) or isset(self::$post[$keys[$i].':'.self::$url])){
				$func = (isset(self::$post[$keys[$i]])) ? self::$post[$keys[$i]] : self::$post[$keys[$i].':'.self::$url];

				if(!isset($func['get']) or self::$url == $func['get']){
					if(is_object($func['action']) or strpos($func['action'],'@') === false){

						Events::register('call_action', [
							'action' => $func['action'],
							'params' => $data,
							'method' => 'post'
						]);
						
						$view(Door::knock_to_func($func['action'], []));
						self::$post_flag = true;
					}else{
						list($controller, $action) = explode('@', $func['action']);
						
						Events::register('call_action', [
							'controller' => $controller,
							'action' => $action,
							'params' => $data,
							'method' => 'post'
						]);

						$res = Door::knock_to_class($controller, $action, $data);

						Events::register('worked_action', [
							'controller' => $controller,
							'action' => $action,
							'result' => $res
						]);

						$view($res);

						self::$post_flag = true;
					}
				}
			}
		}
		return true;
	}

	public static function delRoute($route){
		if(empty($route)){
			return false;
		}
		if(isset(self::$data[$route])){
			unset(self::$data[$route]);
			return true;
		}
		return false;
	}

	public static function count_routes(){
		return count(self::$data);
	}

	public static function get($route, $action){
		self::init();
		if(self::$cache_flag and !is_object($action)){
			return false;
		}
		self::addRoute(array('route'=>$route,'action'=>$action));
	}

	public static function post($post, $action, $uri = false){
		self::init();
		if(self::$cache_flag){
			return;
		}
		if(!$post or !$action) return;

		if(is_string($uri)){
			$uri = ltrim($uri, '/');
			self::$post[$post.':'.$uri]['action'] = $action;
		}
		else{
			self::$post[$post]['action'] = $action;
		}
	}
	
	public static function actions($actions_list){
		self::init();
		if(self::$cache_flag){
			return;
		}
		foreach($actions_list as $i => $action){
			$route = self::generate_route_from_action($action);
			self::get($route, $action);
		}
	}

	public static function action($action){
		self::actions([$action]);
	}
	
	public static function controller($classname, $without = []){
		self::init();
		if(self::$cache_flag){
			return;
		}
		$without = !is_array($without) ? [$without] : $without;
		if(!class_exists($classname)){
			throw new \Exception("Class {$classname} not exists");
		}
		$methods = get_class_methods($classname);
		$count = count($methods);
		$arr = [];
		$without = array_flip($without);
		
		for($i=0;$i<$count;$i++){
			if(isset($without[$methods[$i]]))
				continue;
			$arr[] = $classname . '@' . $methods[$i];
		}

		return self::actions($arr);
	}
	
	public static function getRouteList(){      
		if(count(self::$data))
			$data['get'] = array_keys(self::$data);
		
		if(count(self::$post))
			$data['post'] = array_keys(self::$post);
		
		return $data;    
	}
	
	public static function getControllerList(){     
		return [
			'get' => self::$data,
			'post' => self::$post
		];  
	}

	public static function route_universe($param1, $param2 = false, $param3 = false){
		self::init();
		if(self::$cache_flag and !is_object($param2)){
			return false;
		}

		if($param1 and !$param2 and !$param3){
			if(!is_array($param1) and strpos($param1, '@') !== false){
				// this is action
				self::action($param1);
			}elseif(!is_array($param1) and strpos($param1, 'Controller') !== false){
				// this is controller
				self::controller($param1);
			}elseif(is_array($param1)){
				// this is actionS
				self::actions($param1);
			}
		}elseif($param1 and $param2 and !$param3){
			if(is_string($param1) and is_object($param2)){
				self::get($param1, $param2);
			}elseif(is_string($param2) and strpos($param2, '@') !== false and strpos($param1, '/') !== false){
				// this is GET classic
				self::get($param1, $param2);
			}elseif(strpos($param1, 'Controller') !== false){
				// this is controller with two params
				self::controller($param1, $param2);
			}elseif(strpos($param2, '@') !== false and strpos($param1, '/') === false){
				// this is POST classic
				self::post($param1, $param2);
			}
		}elseif($param1 and $param2 and $param3){
			// this is only POST classic
			self::post($param1, $param2, $param3);
		}
	}
}