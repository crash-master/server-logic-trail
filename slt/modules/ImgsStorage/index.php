<?php

spl_autoload_register(function($class){
	if(strpos($class, 'ImgsStorage\\') !== false){
		list(, $class) = explode('Modules\\ImgsStorage\\', $class);
		$class = str_replace('\\', '/', $class);
		$path = \Kernel\Module::pathToModule('ImgsStorage') . $class . '.php';
		if(file_exists($path)){
			include_once($path);
			return true;
		}
	}
});

\Kernel\Module::register('ImgsStorage');