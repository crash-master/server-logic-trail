<?php
use Kernel\{
	Router,
	Err,
	Log,
	Model,
	Module,
	View,
	Events,
	ExceptionHandler
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
		$url = linkTo($url, $vars);
	}
	header('Location: '.$url);
	return true;
}

function show($data){
	if(is_array($data) or is_object($data)){
		dd($data);
		return false;
	}
	echo($data);
	Events::register('after_rendered_page', [
        'html' => $data
    ]);
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
	return Model::register($name);
}

function module($name){
	return Module::get($name);
}

function linkTo($controller, $args = false){
	return Router::linkTo($controller, $args);
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
