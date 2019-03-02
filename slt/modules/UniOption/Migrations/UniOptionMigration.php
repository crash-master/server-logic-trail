<?php

use Kernel\{
    DBW
};

class UniOptionMigration extends \Extensions\Migration{

    public function up(){
        DBW::create('UniOption', function($t){
            $t -> varchar('name')
            -> longtext('value')
            -> varchar('section_name')
            -> varchar('about_option')
            -> timestamp('date_of_update')
            -> timestamp('date_of_create');
        });
        return true;
    }

    public function down(){
        DBW::drop('UniOption');
        return true;
    }

}

