<?php
namespace Kernel\Services;
use Kernel\{
    DBW
};

class DBWService{
    public $table = '';
    public $rowname = '';
    
    public function __construct($table = false){
        if($table)
            $this->table = $table;
    }
    
    public function table($name){
        DBW::$params[$name] = array();
        DBW::$tablename = $name;
        return new DBWService($name);
    }
    
    public function rows($rows = '*'){
        if(is_string($rows) and $rows != '*'){
            $rows = array($rows);
        }
        
        if(DBW::$meth == 's' or DBW::$meth == 'u')
            DBW::$params[$this->table]['rows'] = $rows;
        else
            DBW::$params[$this->table] = $rows;
        return new DBWService($this->table);
    }
    
    public function where($arr = false){
        DBW::$params[$this->table]['where'] = $arr;
        return new DBWService($this->table);
    }
    
    public function limit($arr = false){
        DBW::$params[$this->table]['limit'] = $arr;
        return new DBWService($this->table);
    }
    
    public function row($rowname = false){ // for create
        DBW::$createParams[$rowname] = array();
        $o = new DBWService();
        $o->rowname = $rowname;
        return $o;
    }
    
    public function order($arr = false){
        DBW::$params[$this->table]['order'] = $arr;
        return new DBWService($this->table);
    }
    
    public function int($rowname,$count = 11){
        $o = $this->row($rowname);
        DBW::$createParams[$o->rowname]['int('.$count.')'] = array();
        return $o;
    }
    
    public function varchar($rowname,$count = 255){
        $o = $this->row($rowname);
        DBW::$createParams[$o->rowname]['varchar('.$count.')'] = array();
        return $o;
    }
    
    public function text($rowname,$count = false){
        $o = $this->row($rowname);
        if($count)
            DBW::$createParams[$o->rowname]['text('.$count.')'] = array();
        else
            DBW::$createParams[$o->rowname]['text'] = array();
        return $o;
    }
    
    public function tinyint($rowname,$count = false){
        $o = $this->row($rowname);
        if($count)
            DBW::$createParams[$o->rowname]['tinyint('.$count.')'] = array();
        else
            DBW::$createParams[$o->rowname]['tinyint'] = array();
        return $o;
    }
    
    public function longtext($rowname,$count = false){
        $o = $this->row($rowname);
        if($count)
            DBW::$createParams[$o->rowname]['longtext('.$count.')'] = array();
        else
            DBW::$createParams[$o->rowname]['longtext'] = array();
        return $o;
    }
    
    public function float($rowname,$count = false){
        $o = $this->row($rowname);
        if($count)
            DBW::$createParams[$o->rowname]['float('.$count.')'] = array();
        else
            DBW::$createParams[$o->rowname]['float'] = array();
        return $o;
    }
    
    public function datetime($rowname){
        $o = $this->row($rowname);
        DBW::$createParams[$o->rowname]['datetime'] = array();
        return $o;
    }
    
    // default
    
    public function null($default = ''){
        $type = array_keys(DBW::$createParams[$this->rowname]);
        DBW::$createParams[$this->rowname][$type[0]]['0'] = $default;
        $o = new DBWService();
        $o->rowname = $this->rowname;
        return $o;
    }
    
    
    
    public function run(){
        return DBW::run();
    }
}
?>