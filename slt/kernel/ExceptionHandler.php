<?php
namespace Kernel;

/**
 * Exception Handler
 */
class ExceptionHandler extends \Exception{

	/**
	 * [$ErrorHandler object]
	 *
	 * @var [object]
	 */
	public $ErrorHandler;

	/**
	 * [$instance self object]
	 *
	 * @var [object]
	 */
	private static $instance;

	/**
	 * [__construct empty]
	 */
	public function __construct(){}

	/**
	 * [init description]
	 *
	 * @param  [object] $ErrorHandler [ErrorHandler $object]
	 *
	 * @return [void] [description]
	 */
	public function init($ErrorHandler){
		$this -> ErrorHandler = $ErrorHandler;
		// set_exception_handler([$this, 'handlerFatal']);
	}

	 /**
	  * [getInstance create instance or return exists instance]
	  *
	  * @return [object] [self object]
	  */
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * [handlerFatal handler for fatal exception]
	 *
	 * @param  [Exception object] $e [Exception $object]
	 *
	 * @return [void] [description]
	 */
	public function handlerFatal($e){
		echo('<pre>');
		var_dump($e);
		$this -> ErrorHandler -> viewFatalError($e -> code, $e -> message, $e -> file, $e -> line);
	}

	/**
	 * [handler user exception]
	 *
	 * @param  [Exception object] $e [description]
	 * @param  boolean $response_code [http response code]
	 *
	 * @return [void] [description]
	 */
	public function handler($e, $response_code = false){
		if($response_code)
			http_response_code($response_code);
		$this -> ErrorHandler -> add($e -> code, $e -> message, $e -> file, $e -> line);
	}
}