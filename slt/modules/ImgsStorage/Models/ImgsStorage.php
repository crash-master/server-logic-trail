<?php

namespace Modules\ImgsStorage\Models;

use Modules\ImgsStorage\Models\ImgsB64;
use Kernel\Services\EssenceDataWrap;

class ImgsStorage extends \Extend\Model{

	use ImgsStorageTrait;

	public $table = 'ImgsStorage';

	private $module;

	public function default_cols(){
		return [];
	}

	public function __construct(){
		$this -> module = module('ImgsStorage');
	}

	public function set_new_img($file_img, $img_name, $description = ''){
		$ready_img_data = ['title' => $img_name, 'description' => $description];
		$img = $this -> get_data_wrapper($ready_img_data);
		$img -> set();
		$img = ImgsB64::ins() -> set_b64($file_img, $img);
		return $img;
	}

	public function remove_img($img_id){
		return $this -> remove(['id', '=', $img_id]);
	}

}
