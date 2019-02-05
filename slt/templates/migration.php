<?php

/* PATH: app/migrations/ */
use Kernel\DBW;

class /*$name*/Migration extends \Extend\Migration{

    public static function up(){
        // Create tables in db
        DBW::create('/*$name*/',function($t){
            $t -> datetime('date_of_update')
            -> datetime('date_of_create');
        });
    }

    public static function down(){
        // Drop tables from db
        DBW::drop('/*$name*/');
    }

}

