<?php

namespace Modules\ImgsStorage\Controllers;

use Kernel\{
	View,
	Model
};

use Modules\ImgsStorage\Models\ImgsStorage;
use Modules\ImgsStorage\Models\ImgsB64;

class ImgsStorageController extends \Extend\Controller{

	public function binary_image($id, $size){
		$ready_img = ImgsB64::ins() -> get_binary($id, $size);
		header('Content-type:image/' . $ready_img['format']);
		return $ready_img['bin'];
	}

	public function install(){
		global $SLT_APP_NAME, $SLT_DEBUG;
		if($SLT_DEBUG == 'off'){
			return false;
		}
		module('ImgsStorage') -> install();
		return '<h2>Installation complete</h2>';
	}

	public function uninstall(){
		global $SLT_APP_NAME, $SLT_DEBUG;
		if($SLT_DEBUG == 'off'){
			return false;
		}
		module('ImgsStorage') -> uninstall();
		return '<h2>Uninstallation complete</h2>';
	}
	
}