<?php

/*  /migrations/ */
use Kernel\DBW;

class Test1Migration extends \Extensions\Migration{

    public function up(){
        // Create tables in db
        DBW::create('Test1',function($t){
            $t -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });

        return true;
    }

    public function down(){
        // Drop tables from db
        DBW::drop('Test1');

        return true;
    }

}

