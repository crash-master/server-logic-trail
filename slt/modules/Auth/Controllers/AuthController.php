<?php

namespace Modules\Auth\Controllers;

use Modules\Auth;
use Kernel\DBIO;
use Kernel\Request;

class AuthController extends \Extensions\Controller{

	public function install(){
		if(SLT_DEBUG != 'on'){
			return redirect('/');
		}
		if(!DBIO::table_exists('Users') or !file_exists(SLT_APP_NAME . '/auth.settings.php')){
			if(module('Auth') -> install()){
				return "<h1>Installation was done</h1>";
			}
		}

		return "<h1>Installation already done</h1>";
	}

	public function uninstall(){
		if(SLT_DEBUG != 'on'){
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
		if(!module('Auth') -> use_default_pages['signup']){
			return redirect('/page_not_found');
		}
		$signin_field = module('Auth') -> signin_field_name;
		$text = module('Auth') -> default_form_control;
		$text = array_merge($text, $text['pages_texts']['signup']);
		unset($texts['pages_texts']);
		$title = $text['title'];
		return view(module('Auth') -> page_view['signup'], compact('title', 'signin_field', 'text'));
	}

	public function signin_page(){
		if(!module('Auth') -> use_default_pages['signin']){
			return redirect('/page_not_found');
		}
		$signin_field = module('Auth') -> signin_field_name;
		$text = module('Auth') -> default_form_control;
		$text = array_merge($text, $text['pages_texts']['signin']);
		unset($texts['pages_texts']);
		$title = $text['title'];
		return view(module('Auth') -> page_view['signin'], compact('title', 'signin_field', 'text'));
	}

	public function signup(){
		Request::clear();
		$post = Request::post();
		$result = module('Auth') -> signup($post);
		$err = module('Auth') -> get_err_by_errcode($result);
		if($err){
			return redirect(link_to_signup_page() . '?errn=' . $result);
		}

		return redirect(link_to_signin_page());
	}

	public function signin(){
		Request::clear();
		$post = Request::post();
		$result = module('Auth') -> signin($post);
		$err = module('Auth') -> get_err_by_errcode($result);
		if($err){
			return redirect(link_to_signin_page() . '?errn=' . $result); 
		}

		return redirect('/');
	}

	public function signout(){
		if(!module('Auth') -> use_default_pages['signout']){
			return redirect('/page_not_found');
		}
		module('Auth') -> signout();
		return redirect(link_to_signin_page());
	}	
}