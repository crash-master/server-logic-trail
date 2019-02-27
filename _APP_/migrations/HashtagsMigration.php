<?php

/*  /migrations/ */
use Kernel\DBW;

class HashtagsMigration extends \Extend\Migration{

    public static function up(){
        // Create tables in db
        DBW::create('Hashtags',function($t){
            $t -> varchar('name')
            -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });
    }

    public static function down(){
        // Drop tables from db
        DBW::drop('Hashtags');
    }

}

