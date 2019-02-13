<?php

namespace Modules\Auth\Controllers;

use Modules\Auth;
use Kernel\DBIO;
use Kernel\Request;

class AuthController{

	public function install(){
		global $SLT_DEBUG;
		if($SLT_DEBUG != 'on'){
			return redirect('/');
		}
		if(!DBIO::table_exists('Users')){
			if(module('Auth') -> install()){
				return "<h1>Installation was done</h1>";
			}
		}

		return "<h1>Installation already done</h1>";
	}

	public function uninstall(){
		global $SLT_DEBUG;
		if($SLT_DEBUG != 'on'){
			return redirect('/');
		}
		if(DBIO::table_exists('Users')){
			if(module('Auth') -> uninstall()){
				return "<h1>Uninstallation was done</h1>";
			}
		}

		return "<h1>Uninstallation already done</h1>";
	}

	public function signup_page(){
		$title = 'SignUp';
		return view(auth_path('view/signup'), compact('title'));
	}

	public function signin_page(){
		$title = 'SignIn';
		return view(auth_path('view/signin'), compact('title'));
	}

	public function signup(){
		Request::clear();
		$post = Request::post();
		$result = module('Auth') -> signup($post);
		$err = module('Auth') -> get_err_by_errcode($result);
		if($err){
			return $err; 
		}
		return redirect('/auth/signup-page');
	}

	public function signin(){
		Request::clear();
		$post = Request::post();
		$result = module('Auth') -> signin($post);
		$err = module('Auth') -> get_err_by_errcode($result);
		if($err){
			return $err; 
		}
		return redirect('/auth/signin-page');
	}

	public function signout(){
		module('Auth') -> signout();
		return redirect('/auth/signin-page');
	}	
}