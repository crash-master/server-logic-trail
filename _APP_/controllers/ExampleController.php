<?php

class ExampleController{
	public function articles(){
		return Articles::ins() -> one() -> id(2) -> with_hashtags() -> to_array();
	}
}