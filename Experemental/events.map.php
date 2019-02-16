<?php

use Kernel\Events;

use Middleware\Kernelevents\Base;
use Middleware\Kernelevents\Components;
use Middleware\Kernelevents\Controllers;
use Middleware\Kernelevents\DB;
use Middleware\Kernelevents\Error;
use Middleware\Kernelevents\Router;
use Middleware\Kernelevents\View;
use Middleware\Kernelevents\Cache;

function events_map(){
	on_event('load_kernel', function(){
		(new Base()) -> load_kernel();
	});

	on_event('app_finished', function(){
		(new Base()) -> app_finished();
	});

	on_event('register_component', function($component){
		(new Components()) -> register_component($component);
	});

	on_event('call_component', function($component){
		(new Components()) -> call_component($component);
	});

	on_event('ready_component_data', function($component_data){
		(new Components()) -> ready_component_data($component_data['component'], $component_data['data']);
	});

	on_event('call_action', function($action){
		(new Controllers()) -> call_action($action['controller'], $action['action'], $action['params'], $action['method']);
	});

	on_event('worked_action', function($action){
		(new Controllers()) -> worked_action($action['controller'], $action['action'], $action['result']);
	});

	on_event('route_not_found', function($route){
		(new Router()) -> route_not_found($route);
	});

	on_event('ready_template_for_view', function($template_content){
		(new View()) -> ready_template_for_view($template_content);
	});

	on_event('ready_connect_to_db', function(){
		(new DB()) -> ready_connect_to_db();
	});

	on_event('ready_sql_query_string', function($sql_query_string){
		(new DB()) -> ready_sql_query_string($sql_query_string);
	});

	on_event('response_from_db', function($response_from_db){
		(new DB()) -> response_from_db($response_from_db['sql_query_string '], $response_from_db['response']);
	});

	on_event('error_was_found', function($error){
		(new Error()) -> error_was_found($error);
	});

	on_event('cache_data_create', function($cache){
		(new Cache()) -> cache_data_create($cache['cache_alias'], $cache['cache_data']);
	});

	on_event('cache_data_used', function($cache){
		(new Cache()) -> cache_data_used($cache['cache_alias'], $cache['cache_data']);
	});

}

