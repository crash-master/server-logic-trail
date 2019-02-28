<?php

use Kernel\{
	DBW
};

class ImgsB64Migration extends \Extend\Migration{

	public static function up(){
		// Create tables in db
		DBW::create('ImgsB64',function($t){
			$t -> longtext('xs')
			-> longtext('sm')
			-> longtext('md')
			-> longtext('lg')
			-> longtext('xl')
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});
	}

	public static function down(){
		// Drop tables from db
		DBW::drop('ImgsB64');
	}

}

