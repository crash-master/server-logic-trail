<?php
namespace Kernel;

class View{
    private static $vars;
    private static $currentPage;
    private static $layoutMap;
    private static $layoutMapCreate_flag = false;

    public static function make($name, $arr = NULL, $varname = NULL){
        if(!is_array(self::$vars))
            self::$vars = [];

        if(!is_null($arr) and !is_null($varname))
            self::$vars[$varname] = $arr;
        elseif(!is_null($arr) and is_null($varname)){
            $names = @array_keys($arr);
            $count = count($names);
            if($count){
                self::$vars = array_merge(self::$vars,$arr);
            }
        }

        self::$currentPage = $name;
        return self::makeAndParse($name);
    }

    public static function getCurrentPage(){
        return self::$currentPage;
    }

    private static function searchFile($name){
        $file = Components::getOnComponentsName($name);
        if($file) list($name) = array_keys($file);
        
        list($rootDir, $secondDir) = explode('/', $name);
        if($rootDir == 'slt' && $secondDir == 'modules'){
            if(file_exists($name)){
                return $name;
            }else{
                if(file_exists($name.'.php')){
                    return $name.'.php';
                }
            }
        }

        $dirList = ['./resources/view/'];

        $count = count($dirList);
        for($i=0;$i<$count;$i++){
            $file = $dirList[$i].$name;
            if(file_exists($file)){
                return $file;
            }else{
                if(file_exists($file.'.php')){
                    return $file.'.php';
                }
            }
        }

        return false;
    }

    private static function makeAndParse($name){
        $file = self::searchFile($name);
        if(!$file)
            return false;
        self::addLayoutMapItem($name, $file);
        ob_start();
        if(!is_null(self::$vars))
            extract(self::$vars);

        require($file);

        $res = ob_get_clean();

        Events::register('ready_template_for_view', $res);
        return $res;
    }

    public static function join($name, $arr_arguments = []){
        $file = self::searchFile($name);
        if(!$file) return false;
        self::addLayoutMapItem($name, $file);
        Components::callToAction($name, $arr_arguments);
        if(!is_null(self::$vars))
            extract(self::$vars);
        require($file);
        return true;
    }

    public static function json($arr){
        return json_encode($arr);
    }

    /**
     * [addVars for adding new vars]
     * @param [array] $arr [array like [$varname => value]]
     */
    public static function addVars($arr){
        self::$vars = array_merge(self::$vars, $arr);
    }

    public static function addLayoutMapItem($name, $file){
        if(self::$layoutMapCreate_flag){
            self::$layoutMap[$name] = $file;
        }
    }

    public static function getLayoutMap($name, $vars = NULL){
        self::$layoutMapCreate_flag = true;
        self::$layoutMap = [];
        self::make($name, $vars);
        self::$layoutMapCreate_flag = false;
        return self::$layoutMap;
    }

}

