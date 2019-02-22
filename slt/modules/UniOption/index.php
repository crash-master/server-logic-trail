<?php

\Kernel\Module::register('UniOption');

spl_autoload_register(function($class){
	if(strpos($class, 'UniOption\\') !== false){
		list(, $class) = explode('Modules\\UniOption\\', $class);
		$class = str_replace('\\', '/', $class);
		$path = \Kernel\Module::pathToModule('UniOption') . $class . '.php';
		if(file_exists($path)){
			include_once($path);
			return true;
		}
	}
});