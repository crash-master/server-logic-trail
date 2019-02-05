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
        return (new Essence($this)) -> get($where, $params);
    }

    public function all($type = "ASC"){
        return (new Essence($this)) -> get(NULL, [
            'order' => ['id', $type]
        ]);
    }

    public function first(){
        return (new Essence($this)) -> get(NULL, [
            'order' => ['id', 'ASC'],
            'limit' => [0, 1]
        ]);
    }

    public function last(){
        return (new Essence($this)) -> get(NULL, [
            'order' => ['id', 'DESC'],
            'limit' => [0, 1]
        ]);
    }

    public function set($data){
        $data['date_of_create'] = !isset($data['date_of_create']) ? 'NOW()' : $data['date_of_create'];
        $data['date_of_update'] = !isset($data['date_of_update']) ? 'NOW()' : $data['date_of_update'];
        return (new Essence($this)) -> set($data);
    }

    public function remove($where = false){
        return (new Essence($this)) -> del($this -> whereExistAndConvert($where));
    }

    public function update($data, $where = false){
        $data['date_of_update'] = !isset($data['date_of_update']) ? 'NOW()' : $data['date_of_update'];
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

}
