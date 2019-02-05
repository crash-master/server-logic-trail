<?php
namespace Kernel;
use Kernel\Services\DBWService;

class DBW{
    public static $params;
    public static $meth;
    public static $createParams;
    public static $tablename;
    private static $columns;
    
    public static function s(){
        self::$meth = 's';
        return new DBWService();
    }
    
    public static function u(){
        self::$meth = 'u';
        return new DBWService();
    }
    
    public static function i(){
        self::$meth = 'i';
        return new DBWService();
    }
    
    public static function d(){
        self::$meth = 'd';
        return new DBWService();
    }
    
    public static function getCR(){
        self::$meth = 'cr';
        return new DBWService();
    }
    
    public static function run(){
        switch(self::$meth){
            case 's': $res = self::select(self::$params); break;
            case 'u': $res = self::update(self::$params); break;
            case 'i': $res = self::insert(self::$params); break;
            case 'd': $res = self::delete(self::$params); break;
            case 'cr': $res = self::getCountRows(self::$params); break;
        }
        
        self::$params = NULL;
        self::$meth = NULL;
        self::$createParams = NULL;
        self::$tablename = NULL;
        
        Events::register('after_db_query', ['result' => $res, 'sql' => DBIO::get_last_sql_query_string()]);
        
        return $res;
    }
    
    public static function select($data){
        $table = array_keys($data);
        $table = $table[0];
        $params = array(
            'table'=>$table,
            'rows'=>$data[$table]['rows'],
        );
        if(isset($data[$table]['where']) and is_array($data[$table]['where'])){
            $params['where'] = $data[$table]['where'];
        }
        if(isset($data[$table]['limit']) and is_array($data[$table]['limit'])){
            $params['limit'] = $data[$table]['limit'];
        }
        if(isset($data[$table]['order']) and is_array($data[$table]['order'])){
            $params['order'] = $data[$table]['order'];
        }
        $res = DBIO::select($params);
        return $res;
    }
    
    public static function update($data){
        $table = array_keys($data);
        $table = $table[0];         
        $params = array(
            'table'=>$table,
            'data'=>$data[$table]['rows']
        );
        if(isset($data[$table]['where']) and is_array($data[$table]['where'])){
            $params['where'] = $data[$table]['where'];
        }

        $res = DBIO::update($params);
        return $res;
    }
    
    public static function insert($data){
        $table = array_keys($data);
        $table = $table[0];
        $params = array(
            'table'=>$table,
            'data'=>$data[$table]
        );
        $res = DBIO::insert($params);
        return $res;
    }
    
    public static function delete($data){
        $table = array_keys($data);
        $table = $table[0];
        $params = array(
            'table'=>$table
        );
        if(isset($data[$table]['where']) and is_array($data[$table]['where'])){
            $params['where'] = $data[$table]['where'];
        }
        $res = DBIO::delete($params);
        return $res;
    }
    
    public static function create($tablename,$func){
        self::$tablename = $tablename;
        $t = new DBWService();
        $func($t);
        $createParams = self::$createParams;
        self::$createParams = NULL;
        return DBIO::create(array($tablename => $createParams));
    }
    
    public static function drop($table){
        return DBIO::drop($table);
    }
    
    public static function columns($table){
        if(!isset(self::$columns[$table]) or empty(self::$columns[$table])){
            self::$columns[$table] = DBIO::columns($table);
        }
        return self::$columns[$table];
    }
    
    public static function getFields($table){
        $data = self::columns($table);
        return $data;
    }
    
    public static function createTime($table){
        return DBIO::getTimeOfCreate($table);
    }
    
    public static function status(){
        return DBIO::getStatusOfTables();
    }
    
    public static function getCountRows($params){
        $res = DBIO::getCountResults(self::$tablename, $params[self::$tablename]['where']);
        return $res['COUNT(*)'];
    }
}
?>