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
        $data = (new Essence($this)) -> get($where, $params);
        Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
        return $data;
    }

    public function all($type = "ASC"){
        $data = (new Essence($this)) -> get(NULL, [
            'order' => ['id', $type]
        ]);

        Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
        return $data;
    }

    public function first(){
        $data = (new Essence($this)) -> get(NULL, [
            'order' => ['id', 'ASC'],
            'limit' => [0, 1]
        ]);
        Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
        return $data;
    }

    public function last(){
        $data = (new Essence($this)) -> get(NULL, [
            'order' => ['id', 'DESC'],
            'limit' => [0, 1]
        ]);
        Events::register('get_from_table', ['tablename' => $this -> table, 'data' => $data]);
        return $data;
    }

    public function set($data){
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

}
