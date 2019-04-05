<?php

/*  /migrations/ */
use Kernel\DBW;

class HashtagsMigration extends \Extensions\Migration{

	public function up(){
		// Create tables in db
		DBW::create('Hashtags',function($t){
			$t -> varchar('title')
			-> varchar('slug')
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});

		return true;
	}

	public function down(){
		// Drop tables from db
		DBW::drop('Hashtags');

		return true;
	}

}

