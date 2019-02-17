<?php

/*  /migrations/ */
use Kernel\DBW;

class Test2Migration extends \Extend\Migration{

    public static function up(){
        // Create tables in db
        DBW::create('Test2',function($t){
            $t -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });
    }

    public static function down(){
        // Drop tables from db
        DBW::drop('Test2');
    }

}

