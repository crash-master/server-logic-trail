<?php
namespace Kernel;

class Components{
	private static $components;

	/**
	 * [create component]
	 * @param  [string] $name   [component name]
	 * @param  [array] $component [array like pathToView => controller@action or pathToView => [controller@action, ...] ]
	 */
	public static function create($name, $component){
		if(!is_array(self::$components)){
			self::$components = [];
		}
		try{
			if(isset(self::$components[$name])){
				throw new Exception("Component with name '{$name}' already exist");
				return false;
			}
		}catch(Exception $e){
			exception($e);
		}

		self::$components[$name] = $component;
	}

	/**
	 * [getAll Return list with all components]
	 * @return [array] [all components]
	 */
	public static function getAll(){
		return self::$components;
	}

	/**
	 * [get searching components]
	 * @param  [string] $val [path to view or action name or component name]
	 * @param  [string] $row [searching param. Have value 'action' or 'view' or nothing]
	 * @return [array]      [components]
	 */
	public static function get($val, $row = NULL){
		switch($row){
			case 'view': 
				return self::getOnViewPath($val);
			case 'action': 
				return self::getOnAction($val);
		}

		return self::getOnComponentsName($val); // default variant
	}

	/**
	 * [getOnComponentsName searching one components with some name]
	 * @param  [string] $name [name of component]
	 * @return [array]       [one component]
	 */
	public static function getOnComponentsName($name){
		if(isset(self::$components[$name])){
			return self::$components[$name];
		}
		return [];
	}

	/**
	 * [getFullOnComponentsName get full component an example [component_name => [view_path => [controller1@action, controller2@action]]]]
	 *
	 * @param  [string] $name [name of component]
	 *
	 * @return [array] [component in assoc array]
	 */
	public static function getFullOnComponentsName($name){
		if(isset(self::$components[$name])){
			return [$name => self::$components[$name]];
		}
		return [];
	}

	/**
	 * [getOnViewPath searching and returning components]
	 * @param  [string] $viewPath [path to view]
	 * @return [array]           [components in array]
	 */
	public static function getOnViewPath($viewPath){
		$ret = [];
		if(!count(self::$components)){
			return [];
		}
		foreach(self::$components as $name => $component){
			foreach($component as $view => $actions){
				if($view == $viewPath){
					$ret[$name] = $component;
				}
			}
		}

		return $ret;
	}

	/**
	 * [getOnAction searching and returning components]
	 * @param  [string] $action [example 'controller@action']
	 * @return [array]         [components]
	 */
	public static function getOnAction($action){
		$ret = [];
		foreach(self::$components as $name => $component){
			foreach($component as $view => $actions){
				foreach($actions as $item => $ac){
					if($ac == $action){
						$ret[$name] = $component;
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * [callToAction for calling action]
	 * @param  [string] $view [path to view]
	 */
	public static function callToAction($view, $arguments = false){
		$component = self::getOnViewPath($view);
		if(!count($component) or !$component){
			$component = self::getFullOnComponentsName($view);
			if(!isset($component[$view])){
				return false;
			}
			list($view) = array_keys($component[$view]);
			if(!count($component) or !$component){
				return false;
			}
		}

		Events::register('before_rendered_component', [
            'view' => $view,
            'component' => $component
        ]);


		foreach($component as $name => $item){
			if(!is_array($item[$view])){
				$item[$view] = [$item[$view]];
			}
			$count = count($item[$view]);
			for($i=0; $i<$count; $i++){
				list($controller, $action) = explode('@', $item[$view][$i]);
				if($controller == ''){
					dd([$component[$name]]);
				}
				View::addVars(self::call($controller, $action, $arguments));
			}
		}
	}

	/**
	 * [call action]
	 *
	 * @param  [string] $controller [controller name]
	 * @param  [string] $action [action name]
	 * @param  [array] $args [array with arguments]
	 *
	 * @return [string] [layout html code]
	 */
	public static function call($controller, $action, $args){   
        $reflectionMethod = new \ReflectionMethod($controller, $action);
        $methParams = $reflectionMethod -> getParameters();
        $params = [];
        $count = count($methParams);

        for($i=0;$i<$count;$i++){
            if(isset($args[$methParams[$i] -> name])){
                $params[] = $args[$methParams[$i] -> name];
            }
        }

        return $reflectionMethod -> invokeArgs(new $controller(), $params);
    }

}