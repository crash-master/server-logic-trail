<?php
namespace Kernel;

class Log{

//    Variables

    private static $logs;

//    Methods

    public static function add($section, $text){
        if(!isset(self::$logs[$section]) or !is_array(self::$logs[$section]))
            self::$logs[$section] = [];
        $date = ' AT '.date('Y.m.d G:i:s');
        if(is_array($text)){
            foreach($text as $i => $val){
                $text[$i] = $val.$date;
            }
        }else
            $text .= $date;

        self::$logs[$section] = (is_array($text)) ? array_merge(self::$logs[$section], $text) : array_merge(self::$logs[$section], [$text]);
        return true;
    }
    
    public static function get(){
        return self::$logs;
    }

    public static function dump(){
        self::clear();

        if(Config::get('system -> log -> on') != true)
            return false;

        if(empty(self::$logs))
            return false;

        $count = count(self::$logs);
        $sections = array_keys(self::$logs);
        $res = [];
        for($i=0;$i<$count;$i++){
            if($sections[$i])
                $res[] = '('.$sections[$i].') '.implode(PHP_EOL.'('.$sections[$i].') ', self::$logs[$sections[$i]]);
        }

        $res = PHP_EOL.PHP_EOL.implode(PHP_EOL, $res);
        $to = Config::get('system -> log -> to');
        $date = date('Y.m.d_z');
        $path = $to.$date.'.log';

        if(!file_exists($path))
            file_put_contents($path, NULL);

        $file = fopen($path, 'r+');
        fseek($file, 0, SEEK_END);
        fwrite($file, $res);
        fclose($file);
        return true;
    }
    
    public static function clear(){
        $life = Config::get('system -> log -> storageLife');
        if($life > 360 or $life <= 0)
            $life = 360;
        
        $files = IncludeControll::scan(Config::get('system -> log -> to'));
        $count = count($files);
        for($i=0;$i<$count;$i++){
            if(strstr(basename($files[$i]),'.gitignore'))
                continue;
            list(,$z) = explode('_',basename($files[$i]));
            list($z) = explode('.',$z);
            if(abs($z - date('z')) > $life){
                @unlink($files[$i]);
                self::add('Log clear', $files[$i]);
            }
        }
        
        return false;
    }

}
