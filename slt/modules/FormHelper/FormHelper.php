<?php

namespace Modules\FormHelper;

use \Kernel\Router\Router;
use \Kernel\Request;

class FormHelper{
	public function __construct(){
		include_once(\Kernel\Module::pathToModule('FormHelper') . '/functions.php');
	}

	public function get_form_to($controller_name, $with_files = false){
		$action = urlto($controller_name);
		$enctype = $with_files ? ' enctype="multipart/form-data"' : '';
		$route_list = Router::getRouteList();
		$needs_route = substr_replace($action, '', 0, 1);
		list($route_filtered) = array_filter($route_list['post'], function($item) use ($needs_route){
			return strpos($item, $needs_route) !== false;
		});
		list($activation_field_name) = explode(':', $route_filtered);

		$secure_code = Request::get_future_session_token();

		$form_header = "<form action=\"{$action}\" method=\"post\"{$enctype}>
		<input type=\"hidden\" name=\"{$activation_field_name}\">
		<input type=\"hidden\" name=\"trust_code\" value=\"{$secure_code}\">
		";
		return $form_header;
	}

	public function trust_form(){
		$trust_code = Request::post('trust_code');
		return Request::is_secure_session($trust_code);
	}
}