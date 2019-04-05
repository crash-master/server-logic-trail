<?php

namespace Models;

class ArticleComment extends \Extensions\Model{

	public $table = "ArticleComment";

	public $relations_map = ['Articles' => 'article_id', 'Comments' => 'comment_id'];

	public function default_rows(){
		return [];
	}

}
