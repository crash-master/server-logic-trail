<?php

namespace Models;

class Articles extends \Extensions\Model{

	public $table = "Articles";

	public function default_rows(){
		return [];
	}

	public function _with_comments($article){
		$relations = ArticleComment::ins() -> relations($article -> id, $this -> table);
		$article -> comments = Comments::ins() -> get(['rows' => ['content'], 'where' => ['id', 'IN', $relations]]);
		return $article;
	}

}
