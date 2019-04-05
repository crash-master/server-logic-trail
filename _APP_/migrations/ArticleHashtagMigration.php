<?php

/*  /migrations/ */
use Kernel\DBW;

class ArticleHashtagMigration extends \Extensions\Migration{

	public function up(){
		// Create tables in db
		DBW::create('ArticleHashtag',function($t){
			$t -> int('article_id')
			-> int('hashtag_id')
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});

		return true;
	}

	public function down(){
		// Drop tables from db
		DBW::drop('ArticleHashtag');

		return true;
	}

}

