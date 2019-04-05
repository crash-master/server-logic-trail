<?php

/*  /controllers/ */

use \Models\Articles;
use \Models\ArticleComment;
use \Models\Comments;

class TestController extends \Extensions\Controller{
	public function articles(){
		$article = Articles::ins() -> one() -> id(1);
		return $article -> with_comments() -> to_array();
	}

	public function comment($id){
		$comment = Comments::ins() -> one() -> id($id);
		return $comment -> get_article() -> to_array();
	}

	public function test(){
		$post = \Kernel\Request::post();
		$article_id = Articles::ins() -> set($post);
		return $article_id;
	}

	public function stand(){
		return '
			<form action="' . urlto('TestController@test') . '" method="post" accept-charset="utf-8">
				<input type="text" name="title" placeholder="TITLE"><br>
				<textarea name="content" placeholder="CONTENT"></textarea><br>
				<button>SUBMIT</button>
			</form>
		';
	}
}