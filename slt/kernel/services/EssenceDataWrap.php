<?php

namespace Kernel\Services;
use Kernel\ExceptionHandler;

class EssenceDataWrap{
	private $entry;
	private $fields;
	private $model;

	public function __construct($entry, $model){
		$this -> entry = $entry;
		$this -> fields = array_keys($entry);
		$this -> model = $model;
	}

	public function to_array(){
		return $this -> entry;
	}

	public function __get($field){
		return $this -> entry[$field];
	}

	public function __set($field, $value){
		if($field == 'id'){
			throw new \Exception("Field id is protected", 1);
		}
		$this -> entry[$field] = $value;
	}

	public function __toString(){
		if(isset($this -> entry['id'])){
			return $this -> entry['id'];
		}

		if(is_array($this -> entry)){
			return $this -> entry[$this -> fields[0]];
		}

		return 'Empty object';
	}

	public function __call($meth, $params){
		$_meth = 'edw_'.$meth;
		if(method_exists($this -> model, $_meth)){
			return $this -> model -> $_meth($this, $params);
		}else{
			throw new \Exception("{$meth}() no exists");
		}
	}

	public function update(){
		return $this -> model -> update($this -> entry, ['id', '=', $this -> entry['id']]);
	}

	public function set(){
		if(isset($this -> entry['id'])){
			unset($this -> entry['id']);
		}
		return $this -> model -> set($this -> entry);
	}

	public function remove(){
		if(!isset($this -> entry['id'])){
			return false;
		}
		return $this -> model -> remove(['id', '=', $this -> entry['id']]);
	}

	public function simplify(){
		$new_entry = [];
		foreach($this -> entry as $entry_item){
			foreach($entry_item as $i => $value){
				$new_entry[] = $value;
			}
		}
		$this -> entry = $new_entry;
		return $this;
	}
}