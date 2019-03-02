<?php

/*  /models/ */

class Articles extends \Extensions\Model{

	public $table = "Articles";

	public function edw_with_hashtags($entry, $params = null){
		if(!$entry -> id) return $entry;
		$links = Articles_Hashtags::ins() -> one() -> get(['rows' => ['hashtag_id'], 'where' => ['article_id', '=', $entry -> id]]);
		$entry -> hashtags = Hashtags::ins() -> id($links -> simplify() -> to_array());
		return $entry;
	}

}
