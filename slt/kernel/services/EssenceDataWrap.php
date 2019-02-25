<?php

namespace Kernel\Services;
use Kernel\ExceptionHandler;

class EssenceDataWrap{
	private $data;
	private $fields;
	private $model;

	public function __construct($data, $model){
		$this -> data = $data;
		$this -> fields = array_keys($data);
		$this -> model = $model;
	}

	public function to_array(){
		return $this -> data;
	}

	public function __get($field){
		return $this -> data[$field];
	}

	public function __set($field, $value){
		try{
			if($field == 'id'){
				throw new \Exception("Field id is protected", 1);
			}
		}catch(Exception $e){
			exception($e);
		}
		$this -> data[$field] = $value;
	}

	public function __toString(){
		if(isset($this -> data['id'])){
			return $this -> data['id'];
		}

		if(is_array($this -> data)){
			return $this -> data[$this -> fields[0]];
		}

		return 'Empty object';
	}

	public function update(){
		return $this -> model -> update($this -> data, ['id', '=', $this -> data['id']]);
	}

	public function set(){
		if(isset($this -> data['id'])){
			unset($this -> data['id']);
		}
		return $this -> model -> set($this -> data);
	}

	public function remove(){
		if(!isset($this -> data['id'])){
			return false;
		}
		return $this -> model -> remove(['id', '=', $this -> data['id']]);
	}
}