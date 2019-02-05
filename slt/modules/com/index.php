<?php
use Kernel\Module;

Module::register('com');

spl_autoload_register(function($class){
    $class = explode('\\', $class);
    $class = $class[count($class) - 1];
    $class = Module::pathToModule('com') . $class . '.php';
    if(file_exists($class)){
        include_once($class);
        return true;
    }
    return false;
});