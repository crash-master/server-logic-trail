<?php
namespace Middleware\Kernelevents;
use Kernel\Events;

class Router{
	public function ready_routes_map($routes_map){

	}

	public function route_not_found($route){
		return view('not_found');
	}
}