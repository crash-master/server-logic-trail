<?php

/*  /migrations/ */
use Kernel\DBW;

class Articles_HashtagsMigration extends \Extensions\Migration{

    public function up(){
        // Create tables in db
        DBW::create('Articles_Hashtags',function($t){
            $t -> int('article_id')
            -> int('hashtag_id')
            -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });

        return true;
    }

    public function down(){
        // Drop tables from db
        DBW::drop('Articles_Hashtags');

        return true;
    }

}

