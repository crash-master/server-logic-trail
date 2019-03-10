<?php

namespace Modules\AjaxHelper;

use Kernel\Module;
use Kernel\CodeTemplate;

class AjaxHelper{
	public $state;
	public $requests;
	public $p2m;
	public $cmd_list = [
		'route_to',
		'to_controller'
	];
	public $controller_access_list = [];

	public $response = [];

	public function __construct(){
		global $SLT_APP_NAME;
		$ah_settings = $SLT_APP_NAME . '/ajaxhelper.settings.php';
		$this -> p2m = Module::pathToModule('AjaxHelper');

		if(!file_exists($ah_settings)){
			$this -> install();
		}

		$this -> state = isset($_POST['AjaxHelper']) ? true : false;
		$this -> requests = json_decode($_POST['requests'], true);
		if($this -> state){
			include_once($ah_settings);
			$this -> controller_access_list = ah_controller_access_list();
			$this -> ajax_helper_controller();
			$this -> send_response();
		}
	}

	public function install(){
		global $SLT_APP_NAME;
		CodeTemplate::create('ajaxhelper.settings', ['filename' => 'ajaxhelper.settings'], $this -> p2m . 'codetemplates/', $SLT_APP_NAME . '/');
	}

	public function ajax_helper_controller(){
		foreach($this -> requests as $request){
			$cmd = $request['cmd'];
			$params = $request['params'];
			if(array_search($cmd, $this -> cmd_list) === false){
				continue;
			}
			$result = $this -> $cmd($params);
			$this -> response[] = $result;
		}
	}

	public function send_response(){
		$response = json_encode($this -> response);
		echo $response;
	}

	public function route_to($controller){
		return urlto($controller);
	}

	public function to_controller($arg){
		$controller = $arg['controller'];
		if(array_search($controller, $this -> controller_access_list) === false){
			return 'no-access';
		}
		$data = $arg['data'];
		list($classname, $methname) = explode('@', $controller);

		\Kernel\Door::knock_to_class($classname, $methname, $data, function($class_name, $meth_name, $params){
			\Kernel\Events::register('call_action', [
				'controller' => $class_name,
				'action' => $meth_name,
				'params' => is_array($params) ? $params : NULL,
				'method' => 'ajax'
			]);
		});
	}
}