<?php

class TestController{
	public function test_page(){
		return view("test");
	}

	public function model_test(){
		$id = 2;
		return cache_code('model.test', function() use ($id){
			return model('Test') -> get(['id', '=', $id]);
		});
	}

	public function all_entries(){
		return cache_code('all.entries', function(){
			return model('Test') -> all();
		});
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