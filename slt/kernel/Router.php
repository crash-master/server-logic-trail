<?php
namespace Kernel;

class Router{
	private static $data;
	private static $viewfunc;
	private static $action404;
	private static $url;
	private static $post;
	private static $request_args;
	private static $post_flag;
	public static $cache_flag;
	public static $init_flag = false;
	public static $do_cache_flag = false;

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

	public static function set_data_from_cache(){
		$cache = Cache::get('routes.map');
		self::$data = $cache['data'];
		self::$action404 = $cache['action404'];
		self::$post = $cache['post'];
		return true;
	}

	public static function caching(){
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

	private static function compareUrl($url,$route){
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

	private static function compareAll(){
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

	private static function routing($view){
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
	
	public static function call($classname, $methname){   
		$object = \call_user_func([$classname, 'ins']);
		$reflectionMethod = new \ReflectionMethod($classname, $methname);
		$methParams = $reflectionMethod -> getParameters();
		$params = [];
		$count = count($methParams);
		$data = self::$request_args;

		for($i=0;$i<$count;$i++){
			if(isset($data[$methParams[$i] -> name])){
				$params[] = $data[$methParams[$i] -> name];
			}
		}
		
		Events::register('call_action', [
			'controller' => $classname,
			'action' => $methname,
			'params' => is_array($params) ? $params : NULL,
			'method' => 'get'
		]);


		return \call_user_func_array([$object, $methname], $params);
	}
	
	public static function callFunc($funcname){
		$reflectionFunction = new \ReflectionFunction($funcname);
		$funcParams = $reflectionFunction -> getParameters();
		$params = [];
		$count = count($funcParams);
		$data = self::$request_args;

		for($i=0;$i<$count;$i++){
			if(isset($data[$funcParams[$i] -> name])){
				$params[] = $data[$funcParams[$i] -> name];
			}
		}
		
		Events::register('call_action', [
			'action' => $funcname,
			'params' => count($params) ? $params : NULL,
			'method' => 'get'
		]);
		return $reflectionFunction -> invokeArgs($params);

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
						
						$view($func['action']());
						self::$post_flag = true;
					}else{
						$arr = explode('@',$func['action']);
						
						Events::register('call_action', [
							'controller' => $arr[0],
							'action' => $arr[1],
							'params' => $data,
							'method' => 'post'
						]);

						$res = self::call($arr[0],$arr[1]);
						Events::register('worked_action', [
							'controller' => $arr[0],
							'action' => $arr[1],
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

	public static function post($post, $action, $get = false){
		self::init();
		if(self::$cache_flag){
			return false;
		}
		if(!$post or !$action) return false;

		if($get){
			$get = ltrim($get, '/');
			self::$post[$post.':'.$get]['action'] = $action;
		}
		else{
			self::$post[$post]['action'] = $action;
		}

		return true;
	}
	
	public static function linkTo($actionName, $params = NULL){
		$data = [];
		foreach(self::$data as $key => $val){
			if(!is_object($val))
				$data[$val] = $key;
		}
		
		if(!isset($data[$actionName])){
			$post = is_null(self::$post) ? [] : self::$post;
			foreach($post as $var_and_route => $action){
				if($action['action'] == $actionName){
					list(,$route) = explode(':', $var_and_route);
					return '/' . $route;
				}
			}
		}

		if(is_array($params)){
			$route = $data[$actionName];
			foreach($params as $key => $val){     
				$route = str_replace('{' . $key . '}', $val, $route);     
			}
			return '/' . $route;
		}
		
		return '/' . $data[$actionName];
	}

	private static function controller_name_to_route_link($controller){
		if(strpos($controller, '\\') !== false){
			$arr = explode('\\', $controller);
			$controller = $arr[count($arr) - 1];
		}
		$controller = str_replace('Controller', '', $controller);
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $controller));
	}

	private static function action_name_to_route_link($action){
		$action = str_replace(' ', '', ucwords(str_replace('_', ' ', $action)));
		return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "-$1", $action));
	}

	public static function generate_route_from_action($action_string){
		// get controller and action names from string
		list($controller, $action) = explode('@', $action_string, 2);
		$controller_for_route = self::controller_name_to_route_link($controller);
		$action_for_route = self::action_name_to_route_link($action);
		// make route
		$route = '/' . $controller_for_route . '/' . $action_for_route;
		if(!class_exists($controller)){
			IncludeControll::load_one_controller($controller);
		}
		$reflectionMethod = new \ReflectionMethod($controller, $action);
		$methParams = $reflectionMethod -> getParameters();

		foreach($methParams as $name){
			$name = trim(ltrim(rtrim(strstr($name, '$'), ']'), '$'));
			$route .= '/' . $name . '/{' . $name . '}';
		}

		return $route;
	}
	
	public static function actions($c){
		self::init();
		if(self::$cache_flag){
			return false;
		}
		if(!is_array($c))
			$c = [$c];
		$count = count($c);
		for($i=0;$i<$count;$i++){
			$route = self::generate_route_from_action($c[$i]);
			
			// set route
			self::get($route, $c[$i]);
		}
		
		return true;
	}

	public static function action($c){
		return self::actions($c);
	}
	
	public static function controller($classname, $without = false){
		self::init();
		if(self::$cache_flag){
			return false;
		}
		$without = !$without ? [] : $without;
		$without = !is_array($without) ? [$without] : $without;
		if(!class_exists($classname)){
			IncludeControll::load_one_controller($classname);
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

