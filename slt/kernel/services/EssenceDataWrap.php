<?php

namespace Kernel\Services;

class EssenceDataWrap extends ArrayLikeObject{
	private $model;

	public function __construct($entry, $model){
		parent::__construct($entry);
		$this -> model = $model;
	}

	public function __set($field, $value){
		if($field == 'id'){
			throw new \Exception("Field id is protected", 1);
		}
		parent::__set($field, $value);
	}

	public function __toString(){
		if(isset($this -> arr['id'])){
			return $this -> arr['id'];
		}

		if(is_array($this -> arr)){
			return $this -> arr[$this -> fields[0]];
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
		return $this -> model -> update($this -> arr, ['id', '=', $this -> arr['id']]);
	}

	public function set(){
		if(isset($this -> arr['id'])){
			unset($this -> arr['id']);
		}
		$result = $this -> model -> set($this -> arr);
		if(!$result){
			$last_added = $this -> model -> one() -> get([
				'rows' => ['id'],
				'order' => ['id', 'DESC'],
				'limit' => [0, 1]
			]);
			$this -> arr['id'] = $last_added -> id;
		}

		return $result;
	}

	public function remove(){
		if(!isset($this -> arr['id'])){
			return false;
		}
		return $this -> model -> remove(['id', '=', $this -> arr['id']]);
	}

}