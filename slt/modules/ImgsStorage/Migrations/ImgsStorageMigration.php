<?php

use Kernel\{
	DBW
};

class ImgsStorageMigration extends \Extensions\Migration{

	public static function up(){
		// Create tables in db

		DBW::create('ImgsStorage',function($t){
			$t -> int('imgsb64_id')
			-> varchar('title')
			-> varchar('type')
			-> text('description')
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});

	}

	public static function down(){
		// Drop tables from db

		DBW::drop('ImgsStorage');
	}

}

