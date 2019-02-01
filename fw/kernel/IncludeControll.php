<?php
namespace Kernel;
use Kernel\Services\RecursiveScan;
use Kernel\Events;

class IncludeControll{
    public static $dirs;
    public static $listTheInclude;
    public static $app_files;
    public static $app_files_name;
    private static $cache_controllers_list;

    public static function init(){
		include_once('./fw/kernel/services/RecursiveScan.php');
		self::loadKernel();
        self::appRoutesInit();
		self::appRootInit();
		self::appAutoLoadInit();
        Events::register('load_kernel');
		self::loadModules();
    }

    public function load_one_controller($controller_name){
        self::$cache_controllers_list = self::$cache_controllers_list ? self::$cache_controllers_list : (new RecursiveScan) -> get_files('app/controllers');
        $controllers = self::$cache_controllers_list;
        foreach($controllers as $controller){
            if(@strpos($controller, $controller_name) !== false){
                include_once($controller);
                return true;
            }
        }

        return false;
    }

    public static function scan($path){
        return (new RecursiveScan) -> get_files($path, false);
    }

    public static function loadKernel(){
        $rs = new RecursiveScan;
        $files = array_merge(
            $rs -> get_files('./fw/kernel'),
            $rs -> get_files('./fw/extend')
        );

        return self::inc($files);
    }

    public static function loadModules(){
        spl_autoload_register(function($class){
            $class = explode("\\", $class);
            $class = $class[count($class) - 1];
            $class = Module::pathToModulesDir() . $class . '/' . $class . '.php';

            if(file_exists($class)){
                include_once($class);
                return true;
            }

            return false;
        });
    }

    public static function inc($files){
        $count = count($files);
        for($i=0;$i<$count;$i++){
            include_once($files[$i]);
        }
        return true;
    }

    public static function appAutoLoadInit(){
        if(!is_array(self::$app_files)){
            self::$app_files = [];
        }

        $dirs = array(
            './app/models',
            './app/controllers',
            './app/migrations',
            './app/middleware',
            './app/middleware/kernelevents',
            './app/sets'
        );

        $rs = new RecursiveScan;
        foreach($dirs as $dir){
            $files = $rs -> get_files($dir);
            foreach($files as $file){
                $filename = explode('/', $file);
                $filename = strstr($filename[count($filename) - 1], '.', true);
                self::$app_files[$filename] = $file;
            }
        }

        spl_autoload_register(function ($classname){
            if(strpos($classname, '\\') !== false){
                $classname = explode('\\', $classname);
                $classname = $classname[count($classname) - 1];
            }
            $app_files = IncludeControll::$app_files;
            $count = count($app_files);
            for($i=0;$i<$count;$i++){
            	if(!isset($app_files[$classname]))
            		continue;
                $path = $app_files[$classname];
                include_once($path);
                return true;
            }
            return false;
        });
    }

    private static function appRootInit(){
    	return self::inc(self::scan('./app'));
    }

    private static function appRoutesInit(){
        return self::inc(self::scan('./app/routes'));
    }

    public static function fileList($arr){
        $page = View::getCurrentPage();
        $list = array();
        if(is_array($arr['*']))
            $list = $arr['*'];
        else
            $list[] = $arr['*'];

        if(!is_null($page) and !empty($page) and isset($arr[$page])){
            if(is_array($arr[$page]))
                $list = array_merge($list,$arr[$page]);
            else
                $list[] = $arr[$page];
        }

        return $list;
    }


}