<?php
namespace Kernel\Services;

class Conf{

    protected $config_data;

    public function __construct($config_arr){
        $this -> config_data = $config_arr;
    }

    public function __get($element){
        if(isset($this -> config_data[$element]) and is_array($this -> config_data[$element]) and !isset($this -> config_data[$element][0])){
            return new self($this -> config_data[$element]);
        }
        return $this -> config_data[$element];
    }

}