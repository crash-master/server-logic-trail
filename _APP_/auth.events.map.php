<?php

use Middleware\Modulesevents\Auth;

function auth_events_map(){
	on_event('auth_signin', function($params){
		(new Auth()) -> signin($params['user_card']);
	});

	on_event('auth_signup', function($params){
		(new Auth()) -> signup($params['user']);
	});

	on_event('auth_signout', function($params){
		(new Auth()) -> signout($params['user_card']);
	});
}