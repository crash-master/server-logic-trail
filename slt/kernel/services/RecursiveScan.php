<?php
namespace Kernel\Services;

class RecursiveScan{
	private $dirs = [];
	private $files = [];

	public function __construct(){}

	public function get($start_point, $recursive_flag = true){
		$this -> recursive($start_point, $recursive_flag);
		$res = [
			'dirs' => $this -> merge($this -> dirs),
			'files' => $this -> merge($this -> files)
		];
		return $res;
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
		$result = $this -> _scan($dir);
		$this -> dirs[] = $result['dirs'];
		$this -> files[] = $result['files'];

		if(!$recursive_flag){
			return false;
		}

		$i = 0;
		do{
			$count = count($this -> dirs[$i]);
			for($j=0; $j<$count; $j++){
				$result = $this -> _scan($this -> dirs[$i][$j]);
				if($result['dirs']){
					$this -> dirs[] = $result['dirs'];
				}
				if($result['files']){
					$this -> files[] = $result['files'];
				}
			}
			
			$i++;
		}while(count($result['dirs']));

	}

	private function merge($arr){
		$result = [];
		foreach($arr as $item){
			$result = array_merge($result, $item);
		}

		return $result;
	}

	public function _scan($dir){
		$files = [];
		$dirs = [];
		$result = scandir($dir);
		if(!$result) $result = [];
		foreach($result as $item){
			if($item == '.' or $item == '..'){
				continue;
			}
			$essence = $dir . '/' . $item;
			if(is_file($essence)){
				$files[] = $essence;
			}else{
				$dirs[] = $essence;
			}
		}

		return ['files' => $files, 'dirs' => $dirs];
	}

}