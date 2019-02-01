<?php
namespace Middleware\Kernelevents;
use Kernel\Events;

class Base{
	public function load_kernel(){
		dd("hello");
	}

	public function app_finished(){

	}
}