<?php

namespace Modules\ImgsStorage\Models;

use Modules\ImgsStorage\Libs\ImageResize;
use Modules\ImgsStorage\Models\ImgsStorage;
use Kernel\Services\EssenceDataWrap;

class ImgsB64 extends \Extensions\Model{
	public $table = "ImgsB64";

	private $size = [];

	private $module;

	public function default_cols(){
		return [];
	}

	public function __construct(){
		$this -> module = module('ImgsStorage');
		$this -> size = $this -> module -> img_sizes;
	}


	public function set_b64($file_img, $img){
		$tmp_title = sha1(rand(100000, 9999999) . $file_img);
		$img_tmp_name = $this -> module -> path_to_tmp . '__' . $tmp_title;

		$this -> resize_img_file($file_img, $img_tmp_name, $this -> size['xl']);

		$img_b64 = $this -> wrap_up();
		$img_b64 -> xl = $this -> b64_from_file($img, $img_tmp_name);
		$img_b64id = $img_b64 -> set();

		$img -> imgsb64_id = $img_b64id;
		$img -> type = $this -> get_type($img -> title);
		$img -> update();

		return $img;
	}

	private function b64_from_file($img, $file){
		$b64 = $this -> gen_b64_meta($img -> title) . base64_encode(file_get_contents($file));
		unlink($file);
		return $b64;
	}

	private function resize_img_file($file_img, $new_name, $size){
		$image = new ImageResize($file_img);
		$image -> resizeToWidth($size);
		unlink($file_img);
		return $image -> save($new_name);
	}

	private function gen_b64_meta($filename){
		return 'data:'.$this -> get_type($filename).';base64,';
	}

	private function get_type($filename){
		$fname = explode('.', $filename);
		$fname = strtolower($fname[count($fname) - 1]);
		if($fname == 'jpg' || $fname == 'jpeg'){
			return 'image/jpeg';
		}

		return 'image/png';
	}

	public function b64_to_file($b64){
		list($meta, $b64) = explode('base64,', $b64);
		$format = strpos($meta, 'image/jpeg') !== false ? 'jpg' : 'png';
		$tmp_name = $this -> module -> path_to_tmp . sha1(rand(10000, 9999999)).'.'.$format;
		file_put_contents($tmp_name, base64_decode($b64));
		return $tmp_name;
	}

	private function create_new_size(EssenceDataWrap $img, $size){
		$exists_img = $this -> get_b64_img($img, 'xl');
		$tmp_bin_img = $this -> b64_to_file($exists_img -> xl);
		$img_out = $this -> module -> path_to_tmp . '__' . basename($tmp_bin_img);
		$this -> resize_img_file($tmp_bin_img, $img_out, $this -> size[$size]);
		$b64 = $this -> b64_from_file($img, $img_out);
		$wrapper = $this -> wrap_up(['id' => $img -> imgsb64_id]);
		$wrapper -> $size = $b64;
		$wrapper -> update();
		return $this -> get_b64_img($img, $size);
	}

	public function get_b64_img(EssenceDataWrap $img, $size){
		$rows = [$size];
		$where = ['id', '=', $img -> imgsb64_id];
		$res = $this -> one() -> get(compact('where', 'rows'));
		if($res -> $size == ''){
			$res = $this -> create_new_size($img, $size);
		}
		return $res;
	}

	public function get_binary($img_id, $size){
		$img = ImgsStorage::ins() -> one() -> id($img_id);
		list($meta, $b64) = explode('base64,', $img -> to_b64($size));
		$format = strpos($meta, 'image/jpeg') !== false ? 'jpg' : 'png';
		return ['bin' => base64_decode($b64), 'format' => $format];
	}

	public function get_link_on_img(EssenceDataWrap $img, $size){
		return urlto('\Modules\ImgsStorage\Controllers\ImgsStorageController@binary_image', ['id' => $img -> id, 'size' => $size]);
	}
}