<?php
namespace Kernel;

class Sess{
    
    public static function get($name){      
        if(!isset($_SESSION)){
            return false;
        }
        
        if(strstr($name,'->'))
            return self::fromPath($name);
        
        return isset($_SESSION[$name]) ? $_SESSION[$name] : NULL;
    }
    
    private static function fromPath($name){
        $part = explode('->',$name);
        $count = count($part);
        $res = $_SESSION;
        for($i=0;$i<$count;$i++){
            $res = self::getPart($res,trim($part[$i]));
        }
        
        return $res;
    }
    
    private static function toPath($name,$val){
        $part = explode('->',$name);
        $count = count($part);
        
        $result = $val;
        
        for($i = $count-1;$i > 0;$i--){
            $result = self::setPart(trim($part[$i]),$result);
        }
        
        $part[0] = trim($part[0]);
        
        if(isset($_SESSION[$part[0]]))
            $_SESSION[$part[0]] = @array_merge($_SESSION[$part[0]],$result);
        else
            $_SESSION[$part[0]] = $result;
    }
    
    private static function setPart($partName,$val){
        $arr = array();
        $arr[$partName] = $val;
        return $arr;
    }
    
    private static function getPart($arr,$partName){
        return @$arr[$partName];
    }
    
    public static function set($name,$val){
        if(!isset($_SESSION)){
            return false;
        }
        
        if(strstr($name,'->'))
            return self::toPath($name,$val);
        
        $_SESSION[$name] = $val;
        
        return true;
    }
    
    public static function kill($name){
        if(!isset($_SESSION)){
            return false;
        }
        
        if(strstr($name,'->')){
            return self::set($name,NULL);
        }
        
        unset($_SESSION[$name]);
        
        return true;
    }
}

?>