<?php

class ConsoleController extends \Extensions\Controller{
	public function welcome(){
		return view('console/welcome');
	}

	public function not_found(){
		return view('console/not_found');
	}
}