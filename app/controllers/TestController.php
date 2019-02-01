<?php

class TestController{
	public function test_page(){
		return view("test");
	}

	public function head_component(){
		return ["title" => "Hello"];
	}
}