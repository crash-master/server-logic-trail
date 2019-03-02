<?php

use Kernel\DBW;

class UsersMigration extends \Extensions\Migration{

	public static function up(){
		// Create tables in db
		DBW::create('Users', function($t){
			$t -> varchar('name', 100)
			-> varchar('surname', 200)
			-> varchar('email', 200)
			-> varchar('phone', 200)
			-> varchar('user_pic_link', 255)
			-> varchar('nickname', 100)
			-> varchar('sex', 20)
			-> int('age')
			-> varchar('slug', 255)
			-> varchar('role', 30)
			-> varchar('password', 200)
			-> text('about')
			-> boolean('active')
			-> boolean('confirmed')
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});
	}

	public static function down(){
		// Drop tables from db
		DBW::drop('Users');
	}

}
