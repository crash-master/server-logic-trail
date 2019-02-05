<?php

class TestController{
	public function test_page(){
		return view("test");
	}

	public function model_test(){
		return model('Test') -> all();
	}

	public function new_entry($entry){
		return model('Test') -> set(['main_row' => $entry]);
	}

	public function head_component(){
		return ["title" => "Hello"];
	}
}