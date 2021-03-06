<?php

namespace Extensions;
use Kernel\{
	DBIO,
	Essence,
	Events
};

use Kernel\Services\EssenceDataWrap;

class Model extends \Kernel\Services\SingletonPattern{

	private static $config;
	private static $tmp_one_flag = false;
	/**
	 * $relations_map массив описывающий как текущая модель должна связывать две других модели
	 *
	 * @var array
	 * @example $relations_map = ['Articles' => 'article_id', 'Comments' => 'comment_id'];
	 */
	public $relations_map;

	public function __construct(){
		self::$config = \Kernel\Config::get() -> system -> model;
	}


	public function q($sql){
		return DBIO::fq($sql);
	}

	private function returned_data($data, $one = true, $returning_entity = null){
		$returning_entity = !is_null($returning_entity) ? $returning_entity : self::$config -> returning;
		if($returning_entity == SLT_INARR){
			return $data;
		}
		if($one or self::$tmp_one_flag){
			self::$tmp_one_flag = false;
			return new EssenceDataWrap($data[0], $this);
		}

		$ret = [];
		$count = count($data);
		for($i=0; $i<$count; $i++){
			$ret[] = new EssenceDataWrap($data[$i], $this);
		}

		return $ret;
	}

	public function get($params = NULL, $please_give_another = null){
		if(count($params) > 2 and !isset($params['where']) and !isset($params['order']) and !isset($params['limit'])){
			$params['where'] = $params;
		}
		$where = isset($params['where']) ? $params['where'] : NULL;
		$data = (new Essence($this)) -> get($where, $params);
		Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
		return $this -> returned_data($data, false, $please_give_another);
	}

	public function all($type = "ASC", $please_give_another = null){
		$data = (new Essence($this)) -> get(NULL, [
			'order' => ['id', $type]
		]);

		Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
		return $this -> returned_data($data, false, $please_give_another);
	}

	public function first($please_give_another = null){
		$data = (new Essence($this)) -> get(NULL, [
			'order' => ['id', 'ASC'],
			'limit' => [0, 1]
		]);
		Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
		return $this -> returned_data($data, true, $please_give_another);
	}

	public function last($please_give_another = null){
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
		if(is_array($params[0])){
			$s = 'IN';
		}else{
			$s = '=';
		}

		return $this -> get([$methname, $s, $params[0]], $params[1]);
	}

	public function one(){
		self::$tmp_one_flag = true;
		return $this;
	}

	public function wrap_up($data = []){
		return new EssenceDataWrap($data, $this);
	}

	private function relations_map_data(int $id, string $table){
		if(!property_exists($this, 'relations_map') and is_array($this -> relations_map)){
			throw new \Exception("Relations map not found in {$this -> table}");
		}

		$by_field = $this -> relations_map[$table];
		$tables_list = array_keys($this -> relations_map);
		list($res_table) = array_keys(array_flip(array_filter($tables_list, function($item) use ($table){
			return $item != $table;
		})));

		$res_field = $this -> relations_map[$res_table];

		return compact('by_field', 'res_field', 'res_table');
	}

	/**
	 * Получить массив id записей привязаных к данной записи
	 *
	 * @method relations
	 *
	 * @param  int $id идентификатор записи
	 * @param  string $table название текущей таблицы
	 *
	 * @return array Возвращает массив id записей привязаных к той записи чей id был передан
	 */
	public function relations(int $id, string $table){
		extract($this -> relations_map_data($id, $table));

		$query_links = $this -> get(['rows' => [$res_field], 'where' => [$by_field, '=', $id]]);
		$relations_arr = [];
		foreach ($query_links as $i => $item) {
			$item = $item -> to_array();
			$relations_arr[] = $item[$res_field];
		}

		return $relations_arr;
	}

}
