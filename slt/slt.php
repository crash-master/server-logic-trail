<?php
namespace Kernel;

class SLT{

	public function __construct($params){
		header('Access-Control-Allow-Origin: *');

		$this -> init_slt_vars($params);

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
	}

	public function init_slt_vars($params){
		$GLOBALS['SLT_APP_NAME'] = $params['app_name'];
	}
}