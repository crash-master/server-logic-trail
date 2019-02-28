<?php

namespace Modules\ImgsStorage\Models;

use Kernel\Services\EssenceDataWrap;

trait ImgsStorageTrait{
	public function edw_remove(EssenceDataWrap $entry){
		return $this -> remove_img($entry);
	}

	public function edw_to_b64(EssenceDataWrap $entry, $size){
		list($size) = $size;
		if(array_search($size, array_keys($this -> module -> img_sizes)) === false){
			throw new \Exception("[ImgsStorage] I don`t no size like {$size}");
		}

		if($entry -> $size){
			return $entry -> $size;
		}

		$b64_img = ImgsB64::ins() -> get_b64_img($entry, $size);
		$entry -> $size = $b64_img -> $size;
		return $entry -> $size;
	}

	public function edw_to_link(EssenceDataWrap $entry, $size){
		list($size) = $size;
		$field = 'link_' . $size;
		if(array_search($size, array_keys($this -> module -> img_sizes)) === false){
			throw new \Exception("[ImgsStorage] I don`t no size like {$size}");
		}

		if($entry -> $field){
			return $entry -> $field;
		}

		$entry -> $field = ImgsB64::ins() -> get_link_on_img($entry, $size);
		return $entry -> $field;
	}
}