<?php

use Kernel\Events;

function events_map(){
	on_event('load_kernel', function(){
		echo "LOAD KERNEL<br>";
	});

	on_event('app_finished', function(){
		echo("APP FINISHED");
	});

	on_event('register_component', function($component){
		echo $component['name'] . '<br>';
	});

	on_event('call_component', function($component){
		// dd($component);
	});

	on_event('ready_component_data', function($component_data){
		// dd($component_data);
	});

	on_event('call_action', function($action){
		// dd($action);
	});

	on_event('worked_action', function($action){
		// dd($action);
	});

	on_event('route_not_found', function($route){
		// dd($route);
	});

	on_event('ready_template_for_view', function($template_content){
		// dd($template_content);
	});

	on_event('ready_connect_to_db', function(){
		echo("<br><strong>DB CONNECT READY</strong><br>");
	});

	on_event('ready_sql_query_string', function($sql_query_string){
		echo("<br>{$sql_query_string}<br>");
	});

	on_event('response_from_db', function($response_from_db){
		// echo("<br>{$response_from_db['sql_query_string']}<br>");
		// dd($response_from_db['response']);
	});

	on_event('error_was_found', function($error){
		// dd($error);
	});


}

