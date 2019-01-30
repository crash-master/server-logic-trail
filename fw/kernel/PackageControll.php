<?php
namespace Kernel;
use Kernel\Services\RecursiveScan;

class PackageControll{
	private static $packages = [
		'path' => [],
		'name' => []
	];
	
	
	private static $pathToPackgeDir = './packages';

	public static function getPackageList(){
		return self::$packages;
	}

	public static function getRealPackageList(){
		
	}

	public static function generatePackageList(){
		$dirObjects = ($rs = new RecursiveScan) -> get_dirs(self::$pathToPackgeDir);
		$count = count($dirObjects);
		$packages = [];
		for($i=0;$i<$count;$i++){
			if(!is_dir($dirObjects[$i]))
				continue;
			$packages['path'][] = $dirObjects[$i];
			$packages['name'][] = basename($dirObjects[$i]);
		}

		self::$packages = $packages;
	}

	public static function includePackages(){
		$rs = new RecursiveScan;
		$count = count(self::$packages['name']);
		for($i=0;$i<$count;$i++){
			$dirObjects = $rs -> get_dirs(self::$packages['path'][$i]);
			$phpFiles = [];
			$countPHPFiles = count($dirObjects);
			for($j=0;$j<$countPHPFiles;$j++){
				if(is_file($dirObjects[$j])){
					list(,$format) = explode('.', basename($dirObjects[$i]));
					if($format == 'php'){
						$phpFiles[] = $dirObjects[$j];
					}
				}else{
					list(,,,$dirname) = explode('/', $dirObjects[$j]);
					if($dirname == 'resources')
						continue;
					$inDir = $rs -> get_dirs($dirObjects[$j]);
					$countFilesInDir = count($inDir);
					for($n=0;$n<$countFilesInDir;$n++){
						list(,$format) = explode('.', basename($inDir[$n]));
						if($format == 'php'){
							$phpFiles[] = $inDir[$n];
						}
					}
				}
			}
			IncludeControll::inc($phpFiles);
		}
		

	}

	public static function init(){
		self::generatePackageList();
		self::includePackages();
	}
}