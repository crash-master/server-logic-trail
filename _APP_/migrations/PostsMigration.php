<?php

/*  /migrations/ */
use Kernel\DBW;

class PostsMigration extends \Extensions\Migration{

    public function up(){
        // Create tables in db
        DBW::create('Posts',function($t){
            $t -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });

        return true;
    }

    public function down(){
        // Drop tables from db
        DBW::drop('Posts');

        return true;
    }

}

