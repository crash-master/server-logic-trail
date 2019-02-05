<?php
namespace Kernel;

class Essence{

//    Variables
	
	private $rows;
	private $rules;
	private $errs;
	private $tableName;
	private $defaultRows;
	
	private $sets;

//    Methods
	
	public function __construct($sets){
		$this -> rows = DBW::getFields($sets -> tableName());
		$this -> defaultRows = $sets -> defaultRows();
		$this -> rules = $sets -> rules();
		$this -> errs = $sets -> errs();
		$this -> tableName = $sets -> tableName();
		$this -> sets = $sets;
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
		
		$this -> sets -> ifNotFound();
		
		return false;
	}


	public function set($data){
		if(is_array($this->rules)){
			if(is_array($this->errs)){
				$res = Validator::rules($data,$this->rules,$this->errs);
			}else{
				$res = Validator::rules($data,$this->rules);
			}
			
			try{
				if($res){
					throw new Exception($this -> tableName . ': ' . $res);
					return $res;
				}
			}catch(Exception $e){
				exception($e);
			}
		}


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

		DBW::i() -> table( $this -> tableName ) -> rows($result) -> run();
		$this -> sets -> afterAdding();
		return false;
	}
	
	public function edit($data, $where = NULL){
		if(is_array($this->rules)){
			if(is_array($this->errs)){
				$res = Validator::rules($data,$this->rules,$this->errs);
			}else{
				$res = Validator::rules($data,$this->rules);
			}
			
			if($res)
				return $res;
		}

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
		$this -> sets -> afterUpdating();
		return false;
	}



	public function del($where = false){
		if(DBW::d() -> table( $this -> tableName ) -> where($where) -> run()){
			$this -> sets -> afterRemoving();
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

}
