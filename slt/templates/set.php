<?php

/* PATH: app/sets/ */

namespace Sets;

class /*$setname*/Set extends \Extend\Set{

    public function tableName(){ 
        return '/*$tablename*/'; 
    }

    public function defaultRows(){
        return [
            'date_of_update' => 'NOW()'
        ];
    }
}