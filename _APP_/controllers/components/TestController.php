<?php

/*  /controllers/ */
use Kernel\{
	View,
	Model
};

class TestController extends \Extensions\Controller{
     
	public function test($name = null){
		return compact('name');
	}
}