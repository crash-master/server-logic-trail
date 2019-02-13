<?php

/**
 * [base authentication config]
 *
 * @method auth_config
 *
 * @return [array] [array with config]
 */
function auth_config(){
	return [
		'signin_field_name' => 'nickname',
		'min_password_length' => 6
	];
}

/**
 * [list of all exists role, you can create custom role if you want]
 *
 * @method auth_role_list
 *
 * @return [array strings] [array with roles]
 */
function auth_role_list(){
	return [
		'admin',
		'user',
		'superadmin'
	];
}

/**
 * [Error messages, code => message_text]
 *
 * @method auth_error_messages
 *
 * @return [array strings] [Array with error messages]
 */
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

/**
 * [Map with rules for redirect]
 *
 * @method auth_redirect_map
 *
 * @return [void] 
 */
function auth_redirect_map(){
	auth_redirect('/auth/signin-page', is_signined(), '/'); // read like I want redirect from page '/auth/signin-page' to '/', but only if sign in fulfilled
	auth_redirect('/auth/signup-page', is_signined(), '/');
}