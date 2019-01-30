<?php
namespace Kernel;

class Module{
    
    public static $modules;
    
    public static function register($name){
        try{
            if(isset(self::$modules[$name])){
                throw new Exception("Module with the name '{$name}' already exists");
                return false;
            }
        }catch(Exception $e){
            exception($e);
        }

        Events::register('during_register_module', [
            'module_name' => $name
        ]);
        
        $classname = $name;
        $name = '\\Modules\\' . $name;
        self::$modules[$classname] = new $name();
    }
    
    public static function includesAllModules(){
        $dirs = Config::get('system -> modules');
        $count = count($dirs); 
        for($i=0;$i<$count;$i++){
            $pathToIndex = 'fw/modules/'.$dirs[$i].'/index.php';
            try{
                if(!file_exists($pathToIndex)){
                    throw new Exception("In module '{$dirs[$i]}' not exists file index.php");
                    continue;
                }
                
                include_once($pathToIndex);
            }catch(Exception $e){
                exception($e);
            }

        }
        
        return true;  
    }
    
    public static function pathToModulesDir(){
        return 'fw/modules/';
    }
    
    public static function pathToModule($name){
        return self::pathToModulesDir() . $name . '/';
    }
    
    public static function get($name){
        Events::register('before_direct_call_module', [
            'module_name' => $name
        ]);
        return self::$modules[$name];
    }
    
}