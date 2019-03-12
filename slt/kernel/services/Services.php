<?php
use Kernel\{
	Router\Router,
	Model,
	Module,
	View,
	Events,
	ExceptionHandler,
	Components,
	Cache\Cache
};

function ddump($data, $indent=0) {
  $retval = '';
  $prefix=\str_repeat(' |  ', $indent);
  if (\is_numeric($data)) $retval.= big(b("Number: ")) . col($data, 'red');
  elseif (\is_string($data)) $retval.= big(b("String: ")) . col("'{$data}'", 'green');
  elseif (\is_null($data)) $retval.= b("NULL");
  elseif ($data===true) $retval.= b("TRUE");
  elseif ($data===false) $retval.= b("FALSE");
  elseif (is_array($data)) {
	$retval.= big(b("Array"))." ( ".col(big(b(count($data))), 'blue').' )';
	$indent++;
	foreach($data AS $key => $value) {
	  $retval.= "\n\n$prefix [ " . col(big(b($key)), 'blue') . " ] = ";
	  $retval.= ddump($value, $indent);
	}
  }
  elseif (is_object($data)) {
	$retval.= big(b("Object"))." (".col(big(b(get_class($data))), 'blue').")";
	$indent++;
	foreach($data AS $key => $value) {
	  $retval.= "\n\n$prefix " . col(big(b($key)), 'blue') . " -> ";
	  $retval.= ddump($value, $indent);
	}
  }
  return $retval;
}

function dd($var){
	function col($str, $c){
	  return '<span style="color: ' . $c . '">' . $str . '</span>';
	}

	function b($str){
	  return '<b>' . $str . '</b>';
	}

	function big($str){
	  return '<big>' . $str . '</big>';
	}

	die('<pre style="width: 90%; padding: 25px; background: #eee">'.ddump($var).'</pre>');
}

function redirect($url, $vars = []){
	if(strpos($url, '@') !== false){
		$url = urlto($url, $vars);
	}
	header('Location: '.$url);
	die();
	return true;
}

function show($data){
	if(is_array($data) or is_object($data)){
		dd($data);
		return false;
	}
	echo($data);
	return true;
}

function arrayToArray($arr){
	if(!$arr)
		return [];
	elseif(!isset($arr[0]))
		return [$arr];
	else
		return $arr;
}

function atarr($arr){
	return arrayToArray($arr);
}

function model($name){
	return call_user_func([$name, 'ins']);
}

function module($name){
	return Module::get($name);
}

function urlto($controller, $args = false){
	return Router::urlto($controller, $args);
}

function vjoin($name, $args = []){
	return View::join($name, $args);
}

function route($param1, $param2 = false, $param3 = false){
	Router::route_universe($param1, $param2, $param3);
}

function view($layout, $vars = NULL){
	return View::make($layout, $vars);
}

function exception($e, $response_code = false){
	return ExceptionHandler::getInstance() -> handler($e, $response_code);
}

function component($component_name, $template_path, $array_actions){
	if(is_string($array_actions)){
		$array_actions = [$array_actions];
	}
	return Components::create($component_name, [$template_path => $array_actions]);
}

function on_event($event_name, $callback_func){
	return Events::on($event_name, $callback_func);
}

function route_not_found($controller){
	return Router::_404($controller);
}

function cache_code($alias, $code_in_func){
	return Cache::code($alias, $code_in_func);
}