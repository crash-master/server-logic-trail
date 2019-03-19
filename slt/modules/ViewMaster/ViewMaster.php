<?php

namespace Modules\ViewMaster;

use \Kernel\Module;
use \Kernel\Services\RecursiveScan;
use \Kernel\CodeTemplate;

class ViewMaster{
	public $p2m;

	public $create_list = [];

	public function __construct(){
		$this -> p2m = Module::pathToModule('ViewMaster');
		$this -> make_create_list();
		$this -> create_controllers();
	}

	public function make_create_list(){
		$rs = new RecursiveScan();
		$files = $rs -> get_files('./resources/view');
		foreach($files as $i => $file){
			$file = basename($file);
			if($file[0] == '_' and (strpos($file, 'controller') !== false or strpos($file, 'component') !== false)){
				list($type, $controller, $action) = explode('.', $file);
				$action = str_replace(['!', '*', '@', '#', '$', '%', '^', ':', '/', '-', '&', '(', ')', '+', '='], '_', $action);
				$controller[0] = strtoupper($controller[0]);
				$controller = str_replace(['!', '*', '@', '#', '$', '%', '^', ':', '/', '-', '&', '(', ')', '+', '='], '', $controller);
				$this -> create_list[$files[$i]] = compact('controller', 'action', 'type');
			}
		}
	}

	public function create_controllers(){
		foreach($this -> create_list as $view => $item){
			if($item['type'] == '_controller'){
				$controller_file = SLT_APP_NAME . '/controllers/' . $item['controller'] . 'Controller.php';
			}elseif($item['type'] == '_component'){
				$controller_file = SLT_APP_NAME . '/controllers/components/' . $item['controller'] . 'Controller.php';
			}else{
				continue;
			}
			list(, $view_file) = explode('./resources/view/', $view);
			list($view_file) = explode('.php', $view_file);
			if(!file_exists($controller_file)){
				$res_path = SLT_APP_NAME . '/controllers/';
				$res_path = $item['type'] == '_component' ? $res_path . 'components/' : $res_path;
				CodeTemplate::create('controller', ['filename' => $item['controller'].'Controller', 'name' => $item['controller']], false, $res_path);
				include_once($controller_file);
			}

			if($this -> action_exists($item['controller'] . 'Controller', $item['action'])){
				continue;
			}
			$open_controller = file_get_contents($controller_file);
			$parse_controller = explode('}', $open_controller);
			unset($parse_controller[count($parse_controller) - 1]);
			$open_controller = implode('}', $parse_controller);
			$new_name = str_replace($item['type'].'.', '', $view);
			rename($view, $new_name);
			$view_file = str_replace($item['type'].'.', '', $view_file);
			$open_controller .= $this -> get_method_code($item['type'], $item['action'], $view_file);
			file_put_contents($controller_file, $open_controller);

			$this -> add_to_some_map($item);
		}
	}

	private function action_exists($controller_name, $action){
		$class = new \ReflectionClass($controller_name);
		$methods = $class -> getMethods();
		foreach($methods as $method){
			if($method -> name == $action){
				return true;
			}
		}

		return false;
	}

	public function get_method_code($type, $meth_name, $view){
		if($type == '_controller'){
return "	public function {$meth_name}(){
		return view('{$view}');
	}
}";
		}elseif($type == '_component'){
return "	public function {$meth_name}(\$var = null){
		return compact('var');
	}
}";
		}

		return '';
	}

	public function add_to_some_map($item){
		if($item['type'] == '_controller'){
			// add route
			$web = file_get_contents(SLT_APP_NAME . '/routes/web.php');
			if(strpos($web, '?>') !== false){
				$web = str_replace('?>', '', $web);
			}

			$web .= "\nroute('{$item['controller']}Controller@{$item['action']}');";
			file_put_contents(SLT_APP_NAME . '/routes/web.php', $web);
		}elseif($item['type'] == '_component'){
			// add component
			//$web = file_get_contents(SLT_APP_NAME . '/routes/web.php');

		}
	}
}