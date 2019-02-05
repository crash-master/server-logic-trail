<?php

class TestController{
	public function test_page(){
		return view("test");
	}

	public function model_test(){
		return model('Test') -> all();
	}

	public function new_entry($entry){
		$entry = urldecode($entry);
		return model('Test') -> set(['username' => $entry]);
	}

	public function update_entry($id, $new_entry){
		$new_entry = urldecode($new_entry);
		return model('Test') -> update(['username' => $new_entry], ['id', '=', $id]);
	}

	public function head_component(){
		return ["title" => "Hello"];
	}
}