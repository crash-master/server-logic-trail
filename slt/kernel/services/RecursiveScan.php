<?php
namespace Kernel\Services;

class RecursiveScan{
	private $dirs = [];
	private $files = [];

	public function __construct(){}

	public function get($start_point, $recursive_flag = true){
		$this -> recursive($start_point, $recursive_flag);
		return ['files' => $this -> files, 'dirs' => $this -> dirs];
	}

	public function get_files($start_point, $recursive_flag = true){
		$res = $this -> get($start_point, $recursive_flag);
		return $res['files'];
	}

	public function get_dirs($start_point, $recursive_flag = true){
		$res = $this -> get($start_point, $recursive_flag);
		return $res['dirs'];
	}

	public function recursive($dir, $recursive_flag = true){
		if(empty($dir) or !is_dir($dir)){
			return false;
		}
		
		$dir_essence = scandir($dir);
		foreach($dir_essence as $essence){
			if($essence == '.' or $essence == '..'){
				continue;
			}

			if(strpos($essence, '.') !== false or is_file($essence)){
				$this -> files[] = $dir . '/' . $essence;
			}else{
				$this -> dirs[] = $dir . '/' . $essence;
				if($recursive_flag){
					$this -> recursive($dir . '/' . $essence);
				}
			}
		}
	}

	private function merge($arr){
		$result = [];
		foreach($arr as $item){
			$result = array_merge($result, $item);
		}

		return $result;
	}

}