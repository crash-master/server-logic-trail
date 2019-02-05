<?php
namespace Kernel\Services;

class ArrayWrap{

    private $data;
    private $config;
    public $lang;
    
    public function __construct($path, $json = true){
        $this -> init($path, $json);
        $this -> lang = $json ? 'json' : 'php';
    }

    public function init($path, $json){
        $this -> data = $json ? json_decode(file_get_contents($path),true) : $path;
        return false;
    }

    private function getPart($arr,$partName){
        return $arr[$partName];
    }

    public function get($name){
        if(strstr($name,'->')){
            $part = explode('->',$name);
            $count = count($part);
            $res = $this -> data;
            for($i=0;$i<$count;$i++){
                $res = $this -> getPart($res,trim($part[$i]));
            }

            return $res;
        }

        return $this -> data[$name];
    }
    
    public function getDataArray(){
        return $this -> data;
    }
    
    
    private function toPath($arr,$name,$val){
        $part = explode('->',$name);
        $count = count($part);

        $result = $val;

        for($i = $count-1;$i > 0;$i--){
            $result = $this -> setPart(trim($part[$i]),$result);
        }

        $part[0] = trim($part[0]);

        if(isset($arr[$part[0]]))
            $arr[$part[0]] = @array_merge($arr[$part[0]],$result);
        else
            $arr[$part[0]] = $result;
        
        return $arr;
    }

}