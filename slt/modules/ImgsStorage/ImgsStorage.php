<?php

namespace Modules\ImgsStorage;

use Kernel\Module;
use Kernel\Maker\Maker;

class ImgsStorage{
	public $path_to_tmp = 'tmp/imgs_storage/';
	public $p2m;
	public $img_sizes = [];

	public function __construct(){
		$this -> p2m = Module::pathToModule('ImgsStorage');
		$this -> img_sizes = ['xl' => 1920, 'lg' => 1200, 'md' => 768, 'sm' => 320, 'xs' => 150];
		include_once($this -> p2m . 'imgs.storage.routes.map.php');
	}

	public function install(){
		Maker::migration_up('ImgsStorage', $this -> p2m . 'Migrations/');
		Maker::migration_up('ImgsB64', $this -> p2m . 'Migrations/');
		if(!file_exists($this -> path_to_tmp)){
			mkdir($this -> path_to_tmp);
		}
	}

	public function uninstall(){
		Maker::migration_down('ImgsStorage', $this -> p2m . 'Migrations/');
		Maker::migration_down('ImgsB64', $this -> p2m . 'Migrations/');
	}
}