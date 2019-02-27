<?php

namespace Modules\UniOption\Models;

class UniOption extends \Extend\Model{

	private $session_cache = [];

	public $table = "UniOption";

	public function default_rows(){
		return [];
	}

	public function get_all(){
		global $SLT_INARR;
		return atarr($this -> all('ASC', $SLT_INARR));
	}

	private function isset_in_session_cache($option){
		foreach($this -> session_cache as $i => $session_cache_option){
			if((isset($option['id']) and $session_cache_option['id'] == $option['id']) or (isset($option['name']) and $session_cache_option['name'] == $option['name'])){
				return $i;
			}
		}

		return false;
	}

	private function add_to_session_cache($option, $arr_flag = false){
		if(!$arr_flag){
			if($this -> isset_in_session_cache($option)){
				return $option;
			}
			$this -> session_cache[] = $option;
		}else{
			foreach($option as $i => $item){
				$this -> add_to_session_cache($item, false);
			}
		}
		return $option;
	}

	private function get_session_cache($field_name, $val, $arr_flag = false){
		$options = [];
		foreach($this -> session_cache as $i => $option){
			if(isset($option[$field_name]) and $option[$field_name] == 'val'){
				if(!$arr_flag){
					return $option;
				}else{
					$options[] = $option;
				}
			}
		}

		return !$arr_flag ? false : $options;
	}

	private function get_by_id_or_name($field_name, $field_value){
		global $SLT_INARR;
		$option = $this -> get_session_cache($field_name, $field_value);
		if(!$option){
			$option = $this -> add_to_session_cache($this -> get([$field_name, '=', $field_value], $SLT_INARR));
		}
		return $option;
	}

	public function get_by_id($option_id){
		return $this -> get_by_id_or_name('id', $option_id);
	}

	public function get_by_name($option_name){
		return $this -> get_by_id_or_name('name', $option_name);
	}

	public function get_option($option_name){
		return $this -> get_by_name_value($option_name);
	}

	public function get_by_id_value($option_id){
		$option = $this -> get_by_id($option_id);
		return $option['value'];
	}

	public function get_by_name_value($option_name){
		$option = $this -> get_by_name($option_name);
		return $option['value'];
	}

	public function get_by_section_name($section_name){
		global $SLT_INARR;
		$options = $this -> get_session_cache('section_name', $section_name);
		if(!$options or !count($options)){
			$options = $this -> add_to_session_cache(atarr($this -> get(['section_name', '=', $section_name], $SLT_INARR)), true);
		}

		return is_array($options) ? $options : [];
	}

	public function set_option_from_arr($option){
		if(!isset($option['name']) or !$option['name']){
			return false;
		}

		$session_cache_option_inx = $this -> isset_in_session_cache($option);

		if($session_cache_option){
			foreach ($option as $option_field_name => $option_field_value) {
				$this -> session_cache[$session_cache_option_inx][$option_field_name] = $option_field_value;
			}
			$this -> update($option, ['name', '=', $option['name']]);
		}else{
			if($this -> length(['name', '=', $option['name']])){
				$this -> update($option, ['name', '=', $option['name']]);
			}else{
				$this -> set($option, ['name', '=', $option['name']]);
			}
		}
		return true;
	}

	public function set_option($name, $value, $section_name = false, $about_option = false){
		$option = [
			'name' => $name,
			'value' => $value
		];

		if($section_name !== false){
			$option['section_name'] = $section_name;
		}

		if($about_option !== false){
			$option['about_option'] = $about_option;
		}

		return $this -> set_option_from_arr($option);
	}

}
