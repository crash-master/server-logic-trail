<?php

spl_autoload_register(function($class){
	if(strpos($class, 'ConsoleView\\') !== false){
		list(, $class) = explode('Modules\\ConsoleView\\', $class);
		$class = str_replace('\\', '/', $class);
		$path = \Kernel\Module::pathToModule('ConsoleView') . $class . '.php';
		if(file_exists($path)){
			include_once($path);
			return true;
		}
	}
});

\Kernel\Module::register('ConsoleView');

