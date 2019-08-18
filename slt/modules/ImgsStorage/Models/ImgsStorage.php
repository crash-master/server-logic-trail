<?php

namespace Modules\ImgsStorage\Models;

use Modules\ImgsStorage\Models\ImgsB64;
use Kernel\Services\EssenceDataWrap;

class ImgsStorage extends \Extensions\Model{

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
		$img = $this -> wrap_up($ready_img_data);
		$img_id = $img -> set();
		$img = ImgsB64::ins() -> set_b64($file_img, $img);
		return $img;
	}

	public function remove_img($img_id){
		$img = $this -> one() -> id($img_id);
		if($img -> exists('imgsb64_id')){
			ImgsB64::ins() -> one() -> id($img -> imgsb64_id) -> remove();
		}
		return $img -> remove();
	}

}
