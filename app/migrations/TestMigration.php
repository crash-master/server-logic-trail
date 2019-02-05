<?php

/*  Automatically was generated from a template slt/templates/migration.php */
use Kernel\{
    DBW
};

class TestMigration extends \Extend\Migration{

    public static function up(){

        // Create tables in db

        DBW::create('Test',function($t){
            $t -> varchar("main_row")
             -> datetime('timestamp');
        });

    }

    public static function down(){

        // Drop tables from db

        DBW::drop('Test');

    }

}

