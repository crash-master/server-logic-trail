<?php

function auth_config(){
	return [
		'signin_field_name' => 'nickname',
		'min_password_length' => 6
	];
}

function auth_role_list(){
	return [
		'admin',
		'user',
		'superadmin'
	];
}

function auth_error_messages(){
	return [
		1 => 'No login or password',
		2 => 'The password is too short',
		3 => 'User with login like this already exists',
		4 => 'User with login like this was not found',
		5 => 'Wrong user password',
		6 => 'Passwords do not match'
	];
}

function auth_redirect_map(){
	auth_redirect('/auth/signin-page', is_signined(), '/test/auth');
	auth_redirect('/auth/signup-page', is_signined(), 'TestController@auth');

	auth_redirect('AdminController', !is_admin(), '/');
	auth_redirect('TestController@test_page', is_user(), '/auth/signin-page');
}