<?php

namespace Modules\ImgsStorage\Models;

use Kernel\Services\EssenceDataWrap;

trait ImgsStorageTrait{
	public function _remove_this(EssenceDataWrap $entry){
		return $this -> remove_img($entry);
	}

	public function _to_b64(EssenceDataWrap $entry, $size){
		list($size) = $size;
		if(array_search($size, array_keys($this -> module -> img_sizes)) === false){
			throw new \Exception("[ImgsStorage] I don`t no size like {$size}");
		}

		if($entry -> exists($field)){
			return $entry -> $size;
		}

		$b64_img = ImgsB64::ins() -> get_b64_img($entry, $size);
		$entry -> $size = $b64_img -> $size;
		return $entry -> $size;
	}

	public function _to_link(EssenceDataWrap $entry, $size){
		list($size) = $size;
		$field = 'link_' . $size;
		if(array_search($size, array_keys($this -> module -> img_sizes)) === false){
			throw new \Exception("[ImgsStorage] I don`t no size like {$size}");
		}

		if($entry -> exists($field)){
			return $entry -> $field;
		}

		$entry -> $field = ImgsB64::ins() -> get_link_on_img($entry, $size);
		return $entry -> $field;
	}
}