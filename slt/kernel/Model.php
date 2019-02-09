<?php
namespace Kernel;

class Model{
    private static $data;

    public static function register($class){
        if(!isset(self::$data[$class])){
            self::$data[$class] = new $class();
        }
        return self::$data[$class];
    }

    public static function getRegisteredList(){
        return array_keys(self::$data);
    }

    public static function get($class){
        return self::$data[$class];
    }

}
