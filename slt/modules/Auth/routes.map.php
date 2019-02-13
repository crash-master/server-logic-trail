<?php

function auth_routes_map(){
	route('Modules\\Auth\\Controllers\\AuthController', ['signup', 'signin']);
	route('auth-signin', 'Modules\\Auth\\Controllers\\AuthController@signin', '/auth/form/signin');
	route('auth-signup', 'Modules\\Auth\\Controllers\\AuthController@signup', '/auth/form/signup');
}