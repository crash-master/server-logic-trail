<?php
namespace Kernel;

use Kernel\Router\Router;
use Kernel\Console\Console;
use Kernel\Cache\Cache;

class SLT{

	public function __construct($params){
		global $SLT_CONSOLE_MOD;
		header('Access-Control-Allow-Origin: *');

		$this -> init_slt_vars($params);
		$this -> init_slt_vars(['start_point_time' => microtime(true)]);

		include_once('slt/kernel/Boot.php');

		Boot::autoloader();
		Boot::get_all_files();

		Sess::init();

		Request::init_current_session_token();
		Request::set_future_session_token();

		Cache::autoclear_not_relevant_cache();
		
		$other_global_slt_vars = [
			'cache' => Config::get() -> system -> cache,
			'debug' => Config::get() -> system -> debug,
			'inobj' => 'inobject',
			'inarr' => 'inarray'
		];
		$this -> init_slt_vars($other_global_slt_vars);

		Boot::load_always([
			'./' . $params['app_name'],
			'./' . $params['app_name'] . '/routes'
		]);

		events_map();

		Events::register('load_kernel');

		$errHandler = new ErrorHandler();
		$exceptionHandler = ExceptionHandler::getInstance() -> init($errHandler);

		Module::includesAllModules();

		DBIO::start();

		Events::register('ready_connect_to_db');

		components_map();

		if(!$SLT_CONSOLE_MOD){
			Router::run(Config::get('system -> showFuncName'));
		}else{
			Console::routing();
		}

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