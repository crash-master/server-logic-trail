<?php
namespace Kernel;
header('Access-Control-Allow-Origin: *');

include_once('kernel/IncludeControll.php');

IncludeControll::init();

events_map();

Events::register('load_kernel');

$errHandler = new ErrorHandler();
$exceptionHandler = ExceptionHandler::getInstance() -> init($errHandler);

Module::includesAllModules();

DBIO::start();

Events::register('ready_connect_to_db');

components_map();

Router::run(Config::get('system -> showFuncName'));

if($errHandler -> err_disp)
	$errHandler -> viewErrs();

Events::register('app_finished');

DBIO::end();

$errHandler -> logsDump();
