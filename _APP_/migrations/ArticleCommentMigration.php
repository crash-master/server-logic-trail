<?php

/*  /migrations/ */
use Kernel\DBW;

class ArticleCommentMigration extends \Extensions\Migration{

	public function up(){
		// Create tables in db
		DBW::create('ArticleComment',function($t){
			$t -> int('article_id')
			-> int('comment_id')
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});

		return true;
	}

	public function down(){
		// Drop tables from db
		DBW::drop('ArticleComment');

		return true;
	}

}

