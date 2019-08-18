<?php

function auth_redirect($route, $access, $new_route){
	if(is_array($route)){
		foreach($route as $item){
			auth_redirect($item, $access, $new_route);
		}
		return true;
	}

	if(strpos($route, '\\') !== false){
		return false;
	}

	if(strpos($route, '@') !== false){
		$route = urlto($route);
	}elseif(strpos($route, 'Controller')){
		$classname = $route;
		if(!class_exists($classname)){
			throw new \Exception("Class {$classname} not exists");
		}
		$methods = get_class_methods($classname);
		foreach($methods as $method){
			auth_redirect($classname . '@' . $method, $access, $new_route);
		}
		return true;
	}
	module('Auth') -> new_redirect($route, $access, $new_route);
	return true;
}

function is_user($user_id = false){
	return auth_is('user', $user_id);
}

function is_admin($user_id = false){
	return auth_is('admin', $user_id);
}

function is_superadmin($user_id = false){
	return auth_is('superadmin', $user_id);
}

function current_signin(){
	return module('Auth') -> current_signin();
}

function current_signin_id(){
	$user = module('Auth') -> current_signin();
	return $user['id'];
}

function user_card($user_id){
	return model('\\Modules\\Auth\\Models\\Users') -> get_user($user_id);
}

function is_confirmed($user_id = false){
	$user = $user_id ? user_card($user_id) : current_signin();
	return !is_null($user) and isset($user['confirmed']) and $user['confirmed'];
}

function is_activated($user_id = false){
	$user = $user_id ? user_card($user_id) : current_signin();
	return !is_null($user) and isset($user['active']) and $user['active'];
}

function auth_is($role, $user_id = false){
	$user = $user_id ? user_card($user_id) : current_signin();
	return $user['role'] == $role;
}

function change_account_data($user_id, $user_data){
	return module('Auth') -> change_account_data($user_id, $user_data);
}

function change_account_role($user_id, $new_role){
	return module('Auth') -> change_account_role($user_id, $new_role);
}

function is_signined(){
	return module('Auth') -> is_signined();
}

function signin_form_controller(){
	return '/auth/form/signin';
}

function signup_form_controller(){
	return '/auth/form/signup';
}

function route_to_signout_controller(){
	return '/auth/signout';
}

function link_to_signin_page(){
	return '/auth/signin-page';
}

function link_to_signup_page(){
	return '/auth/signup-page';
}