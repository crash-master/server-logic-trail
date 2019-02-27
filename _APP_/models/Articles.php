<?php

/*  /models/ */

class Articles extends \Extend\Model{

	public $table = "Articles";

	public function default_rows(){
		return [];
	}

	public function edw_with_hashtags($entry, $params = null){
		global $SLT_INARR;
		if(!$entry -> id) return $entry;
		$links = Articles_Hashtags::ins() -> get(['rows' => ['hashtag_id'], 'where' => ['article_id', '=', $entry -> id]]);
		$entry -> hashtags = Hashtags::ins() -> id($links -> simplify() -> to_array(), $SLT_INARR);
		return $entry;
	}

}
