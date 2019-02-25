<?php

namespace Extend;
use Kernel\{
	DBIO,
	Essence,
	Events
};

use Kernel\Services\EssenceDataWrap;

class Model{

	private static $instance;
	private static $config;
	private static $tmp_one_flag = false;

	public static function ins(){
		if(!self::$instance){
			$classname = get_called_class();
			self::$config = \Kernel\Config::get() -> system -> model;
			self::$instance = new $classname();
		}

		return self::$instance;
	}

	public function q($sql){
		return DBIO::fq($sql);
	}

	private function returned_data($data, $one = true, $please_give_another = false){
		$obj_ret_flag = !$please_give_another ? self::$config -> object_returning : !self::$config -> object_returning;
		if(!$obj_ret_flag){
			return $data;
		}

		if($one or self::$tmp_one_flag){
			return new EssenceDataWrap($data, $this);
		}

		$exacly_array = isset($data[0]) ? $data : [$data];
		$ret = [];
		$count = count($exacly_array);
		for($i=0; $i<$count; $i++){
			$ret[] = new EssenceDataWrap($exacly_array[$i], $this);
		}

		return $ret;
	}

	public function get($params = NULL, $please_give_another = false){
		if(count($params) > 2 and !isset($params['where']) and !isset($params['order']) and !isset($params['limit'])){
			$params['where'] = $params;
		}
		$where = isset($params['where']) ? $params['where'] : NULL;
		$data = (new Essence($this)) -> get($where, $params);
		Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
		return $this -> returned_data($data, false, $please_give_another);
	}

	public function all($type = "ASC", $please_give_another = false){
		$data = (new Essence($this)) -> get(NULL, [
			'order' => ['id', $type]
		]);

		Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
		return $this -> returned_data($data, false, $please_give_another);
	}

	public function first($please_give_another = false){
		$data = (new Essence($this)) -> get(NULL, [
			'order' => ['id', 'ASC'],
			'limit' => [0, 1]
		]);
		Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
		return $this -> returned_data($data, true, $please_give_another);
	}

	public function last($please_give_another = false){
		$data = (new Essence($this)) -> get(NULL, [
			'order' => ['id', 'DESC'],
			'limit' => [0, 1]
		]);
		Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
		return $this -> returned_data($data, true, $please_give_another);
	}

	public function set($data){
		if(is_object($data)){
			$data = $data -> to_array();
		}
		$data['date_of_create'] = !isset($data['date_of_create']) ? 'NOW()' : $data['date_of_create'];
		$data['date_of_update'] = !isset($data['date_of_update']) ? 'NOW()' : $data['date_of_update'];
		Events::register('set_to_table', ['tablename' => $this -> table, 'data' => $data]);
		return (new Essence($this)) -> set($data);
	}

	public function remove($where = false){
		Events::register('remove_from_table', ['tablename' => $this -> table, 'where' => $where]);
		return (new Essence($this)) -> del($this -> whereExistAndConvert($where));
	}

	public function update($data, $where = false){
		if(is_object($data)){
			$data = $data -> to_array();
		}
		$data['date_of_update'] = !isset($data['date_of_update']) ? 'NOW()' : $data['date_of_update'];
		Events::register('update_table', ['tablename' => $this -> table, 'data' => $data, 'where' => $where]);
		return (new Essence($this)) -> edit($data, $this -> whereExistAndConvert($where));
	}

	public function length($where = false){
		return (new Essence($this)) -> length($this -> whereExistAndConvert($where));
	}

	public function truncate(){
		return (new Essence($this)) -> truncate();
	}

	private function whereExistAndConvert($where){
		if(isset($where['where'])){
			return $where['where'];
		}
		return $where;
	}

	public function __call($methname, $params){
		return $this -> get([$methname, '=', $params[0]], $params[1]);
	}

	public function one(){
		self::$tmp_one_flag = true;
		return $this;
	}

	public function get_data_wrapper($data = []){
		return new EssenceDataWrap($data, $this);
	}

}
