<?php

/* PATH: /migrations/ */
use Kernel\DBW;

class /*$name*/Migration extends \Extensions\Migration{

    public static function up(){
        // Create tables in db
        DBW::create('/*$name*/',function($t){
            $t -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });

        return true;
    }

    public static function down(){
        // Drop tables from db
        DBW::drop('/*$name*/');

        return true;
    }

}

