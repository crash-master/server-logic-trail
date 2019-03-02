<?php

class IndexController extends \Extensions\Controller{
	public function welcome_page(){
		return view('welcome');
	}

	public function not_found_page(){
		return view('not_found');
	}
}