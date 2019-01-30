<?php
namespace Kernel;

use Kernel\Services\RecursiveScan;

/**
 * Error Handler
 */
class ErrorHandler{

	/**
	 * [$errs container with all errs for display in html]
	 *
	 * @var [array]
	 */
	private $errs;

	/**
	 * [$errs_src container with all errs in source view]
	 *
	 * @var [array]
	 */
	private $errs_src;

	/**
	 * [$err_disp flag show errs or not]
	 *
	 * @var [bool]
	 */
	public $err_disp;
	
	/**
	 * [$important_errors what errors need to be displayed and logined]
	 *
	 * @var [array]
	 */
	private $important_errors;

	/**
	 * [$path_to__log_file Path to errors logs]
	 *
	 * @var [string]
	 */
	private $path_to_log_dir;

	/**
	 * [$clear_log_after_days unlink old logs after ... days]
	 *
	 * @var integer
	 */
	private $clear_log_after_days = 3;

	/**
	 * [__construct of ErrorHandler]
	 */
	public function __construct(){
		$err_disp = Config::get() -> system -> debug;
		$this -> err_disp = $err_disp;
		$this -> important_errors = Config::get() -> system -> ErrorHandler -> ImportantErrors;
		$this -> path_to_log_dir = Config::get() -> system -> ErrorHandler -> ErrorLogDir;
		if($err_disp){
			error_reporting(-1);
		}else{
			error_reporting(0);
		}
		$this -> setErrHandler();
	}

	/**
	 * [add error]
	 *
	 * @param  [int] $errno [number of error code]
	 * @param  [string] $errstr [error message]
	 * @param  [string] $errfile [file with error]
	 * @param  [int] $errline [number line with error]
	 *
	 * @return [void] []
	 */
	public function add($errno, $errstr, $errfile, $errline){		
		$err_type = $this -> getErrType($errno);
		$this -> errs_src[] = compact('errno', 'err_type', 'errstr', 'errfile', 'errline', 'code');
	}

	/**
	 * [setErrHandler set custom error handler]
	 */
	public function setErrHandler(){
		set_error_handler([$this, 'handler'], E_ALL);

		register_shutdown_function([$this, 'fatalErrorHandler']);

		// ob_start();
	}

	/**
	 * [fatalErrorHandler set custom FATAL error handler]
	 *
	 * @return [null] [nothing]
	 */
	public function fatalErrorHandler(){
		$error = error_get_last();
		if ($error){
			if($error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_COMPILE_ERROR || $error['type'] == E_CORE_ERROR){
				// ob_end_clean();
				$this -> viewFatalError($error['type'], $error['message'], $error['file'], $error['line']);
				// $this -> errs_src[] = ['errno' => $error['type'], 'err_type' => $err_type, 'errstr' => $error['message'], 'errfile' => $error['file'], 'errline' => $error['line']];
				// $this -> logsDump();
			}
		}
		// ob_end_flush();
	}

	/**
	 * [handle of error]
	 *
	 * @param  [int] $errno [number of error code]
	 * @param  [string] $errstr [error message]
	 * @param  [string] $errfile [file with error]
	 * @param  [int] $errline [number line with error]
	 *
	 * @return [bool] [true]
	 */
	public function handler($errno, $errstr, $errfile, $errline){
		$err_type = $this -> getErrType($errno);
		if(!$this -> isImportantError($err_type)){
			return true;
		}
		$this -> add($errno, $errstr, $errfile, $errline);
		return true;
	}

	/**
	 * [getErrType get type of error]
	 *
	 * @param  [int] $errno [error code]
	 *
	 * @return [string] [name of error]
	 */
	private function getErrType($errno){
		$errors = array(
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        );

        return isset($errors[$errno]) ? $errors[$errno] : 'EXCEPTION';
	}

	/**
	 * [get lines with errors from file]
	 *
	 * @param  [string] $errfile [file with error]
	 * @param  [int] $errline [number line with error]
	 *
	 * @return [type] [description]
	 */
	public function getProgCode($errfile, $errline){
		$file = file($errfile);
		$code = [];
		for($i=$errline - 4; $i<$errline+4; $i++){
			if(trim($file[$i]) == '') continue;
			$code[$i+1] = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', htmlspecialchars($file[$i]));
		}
		return $code;
	}

	/**
	 * [Log errs to log system]
	 *
	 * @return [bool] [false]
	 */
	public function logsDump(){
		if(!is_array($this -> errs_src) or !count($this -> errs_src)){
			return true;
		}
		$i = 0;
		$end = count($this -> errs_src) - 1;
		foreach($this -> errs_src as $err){
			$endflag = ($i++ == $end) ? true : false;
			$this -> log($err['err_type'], $err['errno'], $err['errstr'], $err['errfile'], $err['errline'], $endflag);
		}

		$this -> clearOldLogs();
		return true;
	}

	/**
	 * [log if error is important to log file]
	 *
	 * @param  [string] $errtype [description]
	 * @param  [int] $errno [description]
	 * @param  [string] $errstr [description]
	 * @param  [string] $errfile [description]
	 * @param  [int] $errline [description]
	 * @param  boolean $endflag [description]
	 *
	 * @return [void] [description]
	 */
	protected function log($errtype, $errno, $errstr, $errfile, $errline, $endflag = false){
		if(!$this -> isImportantError($errtype)){
			return true;
		}

		$message = "[" . date('Y-m-d H:i:s') . "] {$errtype} (code {$errno}) | {$errstr} IN FILE {$errfile} IN LINE {$errline}";
		if($endflag){
			$message .= "\n==================================================================================================\n";
		}else{
			$message .= "\n-------------------------------------------------\n";
		}
		$timestamp = $this -> getTimestamp();
		error_log($message, 3, $this -> path_to_log_dir . '/errors_' . $timestamp . '.txt');
	}

	/**
	 * [clearOldLogs if log file is old, we need remove him]
	 *
	 * @return [void] [description]
	 */
	private function clearOldLogs(){
		$files = (new RecursiveScan) -> get_files($this -> path_to_log_dir, false);
		$current_timestamp = $this -> getTimestamp();
		foreach($files as $file){
			list(, $name) = explode('/errors_', $file);
			$timestamp = intval($name);
			if($current_timestamp - $timestamp > $this -> clear_log_after_days){
				unlink($file);
			}
		}
	}

	/**
	 * [getTimestamp returned current timestamp in format "days"]
	 *
	 * @return [void] [description]
	 */
	private function getTimestamp(){
		return floor(time() / (60 * 60 * 24));
	}

	/**
	 * [isImportantError description]
	 *
	 * @param  [string] $errtype [type of error]
	 *
	 * @return boolean [true if err typr is important]
	 */
	protected function isImportantError($errtype){
		foreach($this -> important_errors as $important_error){
			if($errtype == $important_error){
				return true;
			}
		}
		return false;
	}

	/**
	 * [view all errors (after another code of site)]
	 *
	 * @return [void] [nothing]
	 */
	public function viewErrs(){
		if(!is_array($this -> errs_src) or !count($this -> errs_src))
			return false;

		foreach($this -> errs_src as $err){
			$errno = $err['errno'];
			$err_type = $err['err_type'];
			$errstr = $err['errstr'];
			$errfile = $err['errfile'];
			$errline = $err['errline'];
			$code = $this -> getProgCode($errfile, $errline);
			$page = view('default/error-layout/error-page-dev', compact('errno', 'err_type', 'errstr', 'errfile', 'errline', 'code'));
			$errs[] = $page;
		}

		show(view('default/errors', ['errs' => $errs]));
	}

	/**
	 * [viewFatalError show styles and html code for fatal error]
	 *
	 * @param  [int] $errno [number of error code]
	 * @param  [string] $errstr [error message]
	 * @param  [string] $errfile [file with error]
	 * @param  [int] $errline [number line with error]
	 *
	 * @return [type] [description]
	 */
	public function viewFatalError($errno, $errstr, $errfile, $errline){
		http_response_code(500);				
		if(!$this -> err_disp) return false;
		$err_type = $this -> getErrType($errno);
		$this -> log($err_type, $errno, $errstr, $errfile, $errline, true);
		$code = $this -> getProgCode($errfile, $errline);
		?>
		<style>
			*{padding:0;margin:0}body{background:#444}.fw-err-block{position:absolute;z-index:10000;top:0;left:0;width:100%;height:auto;padding:20px;background-color:#444;color:#eee;font-family:Courier!important;font-size:15px}.fw-err-block .line{display:block;padding:10px;background:#eee;font-weight:700}.fw-err-block .line.error{background-color:#ccc;color:#B71C1C}.fw-err-block code{padding:10px;color:#00695C}.fw-err-block code .line b{padding-right:10px;border-right:2px solid #ccc;display:inline-block;margin-right:10px;font-weight:400}
		</style>
		<div class="fw-err-block">
			<div class="fw-err-block-head">
				<h1><?= $err_type ?> <small>code(<?= $errno ?>)</small></h1><br>
				<p><strong>Error text:</strong> <?= $errstr ?></p>
			</div>
			<div class="fw-err-block-body">
				<p><strong>In file:</strong> <?= $errfile ?> <strong>on line</strong> <?= $errline ?></p>
				<code>
					<?php foreach ($code as $inx => $line): ?>
						<?php if ($inx == $errline): ?>
							<span class="line error"><b><?= $inx ?></b> <?= $line ?></span>
						<?php else: ?>
							<span class="line"><b><?= $inx ?></b> <?= $line ?></span>
						<?php endif; ?>
					<?php endforeach ?>
				</code>
			</div>
		</div>
		<?php
	}
}