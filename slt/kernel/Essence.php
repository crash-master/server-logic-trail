<?php
namespace Kernel;

class Essence{

//    Variables
	
	private $rows;
	private $tableName;
	private $defaultRows;
	
	private $model_class;

//    Methods
	
	public function __construct($model_class){
		$this -> rows = DBW::getFields($model_class -> table);
		if(method_exists($model_class, 'default_cols')){
			$this -> defaultRows = $model_class -> default_cols();
		}else{
			$this -> defaultRows = [];
		}

		$this -> tableName = $model_class -> table;
		$this -> model_class = $model_class;
		return false;
	}

	public function tableName(){
		return $this -> tableName;
	}

	public function get($where = NULL,$params = NULL){
		if(!isset($params['rows']))
			$params['rows'] = NULL;
		
		if(!isset($params['order']))
			$params['order'] = NULL;
		
		if(!isset($params['limit']))
			$params['limit'] = NULL;
		
		$res = DBW::s() -> table( $this -> tableName ) -> rows($params['rows']) -> where($where) -> order($params['order']) -> limit($params['limit']) -> run();
		if(is_array($res))
			return $res;
		
		return false;
	}


	public function set($data){

		$count = count($this -> rows);
		$result = array();

		for($i=0;$i<$count;$i++){
			if(isset($data[$this -> rows[$i]]) and $this -> rows[$i] != 'id'){
				$result[$this -> rows[$i]] = $data[$this -> rows[$i]];
			}
		}

		$count = count($this -> defaultRows);
		$keys = @array_keys($this -> defaultRows);
		$rows_list = array_flip($this -> rows);
		
		for($i=0;$i<$count;$i++){
			if( ( !isset($result[$keys[$i]]) or empty($result[$keys[$i]]) ) and isset($rows_list[$keys[$i]]) ){
				$result[$keys[$i]] = $this -> defaultRows[$keys[$i]];
			}
		}

		return DBW::i() -> table( $this -> tableName ) -> rows($result) -> run();
	}
	
	public function edit($data, $where = NULL){
		$count = count($this -> rows);
		$result = array();

		for($i=0;$i<$count;$i++){
			if(isset($data[$this -> rows[$i]]) and $this -> rows[$i] != 'id'){
				$result[$this -> rows[$i]] = $data[$this -> rows[$i]];
			}
		}

		$count = count($this -> defaultRows);
		$keys = @array_keys($this -> defaultRows);

		for($i=0;$i<$count;$i++){
			if(!isset($result[$keys[$i]]) or (empty($result[$keys[$i]]) and $result[$keys[$i]] != '0')){
				$result[$keys[$i]] = $this -> defaultRows[$keys[$i]];
			}
		}

		DBW::u() -> table( $this -> tableName ) -> rows($result) -> where($where) -> run();
		return false;
	}



	public function del($where = false){
		if(DBW::d() -> table( $this -> tableName ) -> where($where) -> run()){
			return true;
		}
		
		return false;
	}
	
	public function length($where = false){
		return DBW::getCR() -> table($this -> tableName) -> where($where) -> run();
	}
	
	public function truncate(){
		return DBIO::truncate($this -> tableName);
	}

	public function get_columns(){
		return $this -> $rows;
	}

}
