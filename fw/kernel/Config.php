<?php
namespace Kernel;

class Config{
    protected static $db;
    protected static $conf;

    protected static function init(){
        $config_file = require_once('./fw/config/main.config.php');
        self::$db = new Services\ArrayWrap($config_file, false);
        self::$conf = new Services\Conf($config_file);
    }

    public static function conf(){
        return self::$conf;
    }

    public static function get($path = false){
        if(!self::$db)
            self::init();

        if(!$path)
            return self::conf();
        return self::$db -> get($path);
    }

    public static function set($path, $val){
        if(!self::$db)
            self::init();

        return self::$db -> set($path,$val);
    }

    public static function del($path){
        return self::$db -> del($path);
    }

    public static function dump(){
        self::$db -> dump();
    }

    
}
