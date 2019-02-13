<?php

use Kernel\Module;

spl_autoload_register(function($class){
    if($class == 'Modules\Auth'){
        include_once(Module::pathToModule('Auth') . 'Auth.php');
        return true;
    }

    $arr = explode('\\', $class);
    $path = Module::pathToModule('Auth');
    $count = count($arr);
    for($i=2; $i<$count; $i++){
        $path .= ($i < $count - 1) ? $arr[$i] . '/' : $arr[$i];
    }

    $path .= '.php';
    if(!file_exists($path)){
        return false;
    }

    include_once($path);

    return true;
});


Module::register('Auth');