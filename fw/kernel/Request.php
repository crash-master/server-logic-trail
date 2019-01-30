<?php
namespace Kernel;

class Request{
    private static $urlTemp;
    private static $args;
    private static $url;

    public static function getArgs($urlTemp = false){
        if($urlTemp){
            self::$urlTemp = $urlTemp; 
        }
        $url = explode('/',self::getUrl());
        $uri = explode('/',self::$urlTemp); 
        
        $count = count($url);
        for($i=0;$i<$count;$i++){
            if(strpos($uri[$i], '{') === false)
               continue;
               
               $name = explode('{',$uri[$i]);
               list($name) = explode('}',$name[1]);
               $vars[$name] = $url[$i];
        }
            
        self::$args = $vars;
        return $vars;
    }
    
    public static function getAll(){
        return $_GET;
    }
    
    public static function postAll(){
        return $_POST;
    }

    public static function ParseURLModRewrite(){
        $url = $_SERVER['REQUEST_URI'];
        if(strpos($url, '?') !== false){
            list($url) = explode('?', $url);
        }
        $url = trim($url, '/');

        self::$url = $url;
        return $url;
    }

    public static function getUrl(){
        return empty(self::$url) ? self::ParseURLModRewrite() : self::$url;
    }
    
    public static function get($params = false){
        if(!$params) return self::getAll();
        return is_array($params) ? self::array_items_from_array($params, $_GET) : $_GET[$params];
    }
    
    public static function post($params = false){
        if(!$params) return self::postAll();
        return is_array($params) ? self::array_items_from_array($params, $_POST) : $_POST[$params];
    }

    private static function array_items_from_array($items, $arr){
        $res_arr = [];
        $count = count($items);
        for($i=0; $i<$count; $i++){
            if(isset($arr[$items[$i]])){
                $res_arr[$items[$i]] = $arr[$items[$i]];
            }
        }

        return $res_items;
    }

    private static function _clear($arr){
        $keys = array_keys($arr);
        $count = count($arr);
        for($i=0;$i<$count;$i++){
            $arr[$keys[$i]] = trim(htmlspecialchars($arr[$keys[$i]]));
        }
        return $arr;
    }
    
    public static function clearGET(){
        $_GET = self::_clear($_GET);
        return true;
    }

    public static function clear_get(){
        $_GET = self::_clear($_GET);
        return true;
    }
    
    public static function clearPOST(){
        $_post = self::_clear($_post);
        return true;
    }

    public static function clear_post(){
        $_post = self::_clear($_post);
        return true;
    }
    
    public static function clear(){
        return self::clear_get() and self::clear_post();
    }
}