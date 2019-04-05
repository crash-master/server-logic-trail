<?php

namespace Models;

class Comments extends \Extensions\Model{

	public $table = "Comments";

	public function default_rows(){
		return [];
	}

	public function _get_article($comment){
		$relations = ArticleComment::ins() -> relations($comment -> id, $this -> table);
		return Articles::ins() -> one() -> id($relations);
	}

}
