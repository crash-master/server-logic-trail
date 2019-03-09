<?php

/*  /migrations/ */
use Kernel\DBW;

class LikesMigration extends \Extensions\Migration{

    public function up(){
        // Create tables in db
        DBW::create('Likes',function($t){
            $t -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });

        return true;
    }

    public function down(){
        // Drop tables from db
        DBW::drop('Likes');

        return true;
    }

}

