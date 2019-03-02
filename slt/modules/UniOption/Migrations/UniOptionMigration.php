<?php

use Kernel\{
    DBW
};

class UniOptionMigration extends \Extensions\Migration{

    public static function up(){

        DBW::create('UniOption', function($t){
            $t -> varchar('name')
            -> longtext('value')
            -> varchar('section_name')
            -> varchar('about_option')
            -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });

    }

    public static function down(){

        DBW::drop('UniOption');

    }

}

