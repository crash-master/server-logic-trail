<?php

namespace Modules;
use Kernel\Router;

$COM_BACKUP_ROUTES;

class Com{
	
	public function __construct(){
		$this -> startedInit();
		require_once('com-routes.php');
		require_once('com-components.php');
	}

	public function startedInit(){
		global $COM_BACKUP_ROUTES;
		$cont = Router::getControllerList();

		if(isset($cont['post'])){
			foreach($cont['post'] as $variableAndRoute => $action){
				if(strstr($variableAndRoute, ':')){
					$res = explode(':', $variableAndRoute);
					$route = $res[1];
					$variable = $res[0];
					$cont['post'][$variable]['action'] = $action['action'];
					$cont['post'][$variable]['route'] = $route;
					unset($cont['post'][$variableAndRoute]);
				}
			}
		}

		$COM_BACKUP_ROUTES = $cont;
		return false;
	}
	
}