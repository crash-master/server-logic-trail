<?php

namespace Extend;
use Kernel\{
    DBIO,
    Essence,
    Events
};

class Model{

    public function q($sql){
        return DBIO::fq($sql);
    }

    public function get($params = NULL){
    	if(count($params) > 2 and !isset($params['where']) and !isset($params['order']) and !isset($params['limit'])){
    		$params['where'] = $params;
    	}
        $where = isset($params['where']) ? $params['where'] : NULL;
        return (new Essence($this -> sets)) -> get($where, $params);
    }

    public function all($type = "ASC"){
        return (new Essence($this -> sets)) -> get(NULL, [
            'order' => ['id', $type]
        ]);
    }

    public function first(){
        return (new Essence($this -> sets)) -> get(NULL, [
            'order' => ['id', 'ASC'],
            'limit' => [0, 1]
        ]);
    }

    public function last(){
        return (new Essence($this -> sets)) -> get(NULL, [
            'order' => ['id', 'DESC'],
            'limit' => [0, 1]
        ]);
    }

    public function set($data){
        return (new Essence($this -> sets)) -> set($data);
    }

    public function remove($where = false){
        return (new Essence($this -> sets)) -> del($this -> whereExistAndConvert($where));
    }

    public function update($data, $where = false){
        return (new Essence($this -> sets)) -> edit($data, $this -> whereExistAndConvert($where));
    }

    public function length($where = false){
        return (new Essence($this -> sets)) -> length($this -> whereExistAndConvert($where));
    }

    public function truncate(){
        return (new Essence($this -> sets)) -> truncate();
    }

    private function whereExistAndConvert($where){
    	if(isset($where['where'])){
    		return $where['where'];
    	}
    	return $where;
    }

}
