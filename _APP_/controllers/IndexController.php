<?php

class IndexController{
	public function welcome_page(){
		return view('welcome');
	}

	public function not_found_page(){
		return view('not_found');
	}

	public function form(){
		return view('form');
	}

	public function form_processing(){
		$post = \Kernel\Request::post();
		if(\Kernel\Request::is_secure_session($post['token'])){
			return dd($post);
		}

		return 'Not Secure';
	}
}