<?php
namespace Components;

class MainController{
	public function main_component($title = ''){
		return cache_code('main.head.component', function() use ($title){
			return ['title' => "Hello any body {$title}"];
		});
	}
}