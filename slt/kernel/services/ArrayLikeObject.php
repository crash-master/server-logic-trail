<?php

namespace Kernel\Services;

class ArrayLikeObject{
	protected $arr;
	protected $fields;	

	public function __construct($arr){
		$this -> arr = $arr;
		$this -> fields = array_keys($arr);
	}

	public function __get($property){
		if(!isset($this -> arr[$property])){
			throw new \Exception("Property like '{$property}' doesn't exists");
		}

		return $this -> arr[$property];
	}

	public function __set($property, $value){
		$this -> arr[$property] = $value;
	}

	public function to_array(){
		return $this -> arr;
	}

	public function simplify(){
		$new_arr = [];
		foreach($this -> arr as $item){
			foreach($item as $i => $value){
				$new_arr[] = $value;
			}
		}
		$this -> arr = $new_arr;
		return $this;
	}

	public function get_fields(){
		return $this -> fields;
	}

	public function exists($property_name){
		if(!isset($this -> arr[$property_name])){
			return false;
		}
		return true;
	}
}