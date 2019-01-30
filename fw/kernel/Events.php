<?php
namespace Kernel;

class Events{
    private static $events;
    private static $waste;
    
    public static function add($eventname, $callback){
        if(!isset(self::$events[$eventname])){     
            self::$events[$eventname] = [$callback];
            return true;
        }
        
        self::$events[$eventname][] = $callback;
        return true;
    }
    
    public static function register($eventname, $args){
        self::$waste = !self::$waste ? [$eventname => $args] : array_merge(self::$waste, [$eventname => $args]);
        if(!isset(self::$events[$eventname]))
            return false;
        
        foreach(self::$events[$eventname] as $item => $callback){
            $callback($args);
        }
        
        return true;
    }
    
    public static function getList(){
        $events = [];
        foreach(self::$events as $name => $callback){
            $events[$name] = count(self::$events[$name]);
        }
        
        return $events;
    }
    
    public static function getWaste(){
        return self::$waste;
    }
    
}