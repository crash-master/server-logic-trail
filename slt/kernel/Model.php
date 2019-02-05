<?php
namespace Kernel;

class Model{
    private static $data;

    public static function register($name){
        if(!isset(self::$data[$name])){
            self::$data[$name] = new $name();
        }
        return self::$data[$name];
    }

    public static function getRegisteredList(){
        return array_keys(self::$data);
    }

    public static function get($name){
      Events::register('before_call_model', [
          'model_name' => $name
      ]);
        return self::$data[$name];
    }

}
