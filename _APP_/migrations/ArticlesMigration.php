<?php

/*  /migrations/ */
use Kernel\DBW;

class ArticlesMigration extends \Extensions\Migration{

	public function up(){
		// Create tables in db
		DBW::create('Articles',function($t){
			$t -> varchar('title')
			-> text('content')
			-> text('excerpt')
			-> varchar('slug')
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});
		return true;
	}

	public function down(){
		// Drop tables from db
		DBW::drop('Articles');
		return true;
	}

}

