<?php

class ExampleController extends \Extensions\Controller{
	public function articles(){
		$article = Articles::ins() -> one() -> id(1) -> with_hashtags();
		return $article -> hashtags[0] -> to_array();
	}

	public function simple(){
		return Articles::ins() -> get(['id', '=', 1]);
	}
}