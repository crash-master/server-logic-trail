<?php

/*  /migrations/ */
use Kernel\DBW;

class ArticlesMigration extends \Extend\Migration{

    public static function up(){
        // Create tables in db
        DBW::create('Articles',function($t){
            $t -> varchar('title')
            -> text('content')
            -> text('excerpt')
            -> varchar('slug')
            -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });
    }

    public static function down(){
        // Drop tables from db
        DBW::drop('Articles');
    }

}

