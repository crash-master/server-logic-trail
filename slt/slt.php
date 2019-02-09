<?php
namespace Kernel;

class SLT{

	public function __construct($params){
		header('Access-Control-Allow-Origin: *');

		$this -> init_slt_vars($params);

		include_once('slt/kernel/IncludeControll.php');

		IncludeControll::init();
		IncludeControll::appRootInit();
		IncludeControll::loadKernel();

		Cache::autoclear_not_relevant_cache();
		
		$other_global_slt_vars = [
			'cache' => Config::get() -> system -> cache,
			'debug' => Config::get() -> system -> debug
		];
		$this -> init_slt_vars($other_global_slt_vars);

        IncludeControll::appRoutesInit();
		IncludeControll::appAutoLoadInit();
		IncludeControll::loadModules();

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
		foreach($params as $var_name => $value){
			$var_name = 'SLT_' . strtoupper($var_name);
			$GLOBALS[$var_name] = $value;
		}
	}
}