<?php

namespace Modules\Auth\Models;

use Kernel\Sess;
use Kernel\Events;

class Users extends \Extensions\Model{
	public $table = 'Users';

	public $auth_module;

	public function default_cols(){
		return [
			'role' => 'user',
			'active' => true,
			'confirmed' => false
		];
	}

	public function __construct(){
		$this -> auth_module = module('Auth');
	}

	public function signup($user){
		if(!isset($user['password']) or !isset($user[$this -> auth_module -> signin_field_name]) or empty($user[$this -> auth_module -> signin_field_name])){
			return 1;
		}

		if(strlen($user['password']) < $this -> auth_module -> min_password_length){
			return 2;
		}

		if($user['password'] != $user['password_again']){
			return 6;
		}

		$user['password'] = sha1($user['password']);
		if($this -> length([$this -> auth_module -> signin_field_name, '=', $user[$this -> auth_module -> signin_field_name]])){
			return 3;
		}

		$id = $this -> set($user);
		$user['id'] = $id;
		Events::register('auth_signup', ['user' => $user]);

		return false;
	}

	public function signin($user){
		if(!isset($user['password']) or !isset($user[$this -> auth_module -> signin_field_name]) or empty($user[$this -> auth_module -> signin_field_name])){
			return 1;
		}

		$user_card = $this -> one() -> get([$this -> auth_module -> signin_field_name, '=', $user[$this -> auth_module -> signin_field_name]]) -> to_array();
		if(!$user_card){
			return 4;
		}

		if(sha1($user['password']) != $user_card['password']){
			return 5;
		}

		Sess::set('user_card', $user_card);
		Events::register('auth_signin', ['user_card' => $user_card]);

		return $user_card;
	}

	public function get_user($user_id){
		return $this -> one() -> id($user_id);
	}

	public function confirm($user_id){
		$user = $this -> one() -> id($user_id);
		$user -> confirmed = 1;
		return $user -> update();
	}

	public function withdraw_confirmation($user_id){
		return $this -> update(['confirmed' => false], ['id', '=', $user_id]);
	}

	public function activate_account($user_id){
		$user = $this -> one() -> id($user_id);
		$user -> active = 1;
		return $user -> update();
	}

	public function deactivate_account($user_id){
		$user = $this -> one() -> id($user_id);
		$user -> active = 0;
		return $user -> update();
	}

	private function account_data_filter($user_data){
		$forbidden_fields = [
			'active',
			'confirmed',
			'id',
			'date_of_update',
			'date_of_create',
			'role'
		];

		foreach($user_data as $key => $item){
			foreach($forbidden_fields as $field){
				if($key == $field){
					unset($user_data[$key]);
					break;
				}
			}
		}

		return $user_data;
	}

	public function change_account_data($user_id, $user_data){
		$user = $this -> one() -> id($user_id);
		$user_data = $this -> account_data_filter($user_data);
		foreach($user_data as $field => $val){
			$user -> $field = $val;
		}
		return $user -> update();
		// return $this -> update($user_data, ['id', '=', $user_id]);
	}

	private function role_exists($role){
		if(array_search($role, $this -> auth_module -> role_list) === false){
			return false;
		}

		return true;
	}

	public function change_account_role($user_id, $new_role){
		if(!$this -> role_exists($new_role)){
			return false;
		}
		return $this -> update(['role' => $new_role], ['id', '=', $user_id]);
	}
}