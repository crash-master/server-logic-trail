<?php

namespace Modules\Auth\Models;

use Kernel\Sess;
use Kernel\Events;

class Users extends \Extend\Model{
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
		
		Events::register('auth_signup', ['user' => $user]);
		$this -> set($user);

		return false;
	}

	public function signin($user){
		if(!isset($user['password']) or !isset($user[$this -> auth_module -> signin_field_name]) or empty($user[$this -> auth_module -> signin_field_name])){
			return 1;
		}

		$user['password'] = sha1($user['password']);

		$user_card = $this -> get([$this -> auth_module -> signin_field_name, '=', $user[$this -> auth_module -> signin_field_name]]);
		if(!$user_card){
			return 4;
		}

		if($user['password'] != $user_card['password']){
			return 5;
		}

		Events::register('auth_signin', ['user_card' => $user_card]);
		Sess::set('user_card', $user_card);

		return $user_card;
	}

	public function get_user($user_id){
		return $this -> get(['id', '=', $user_id]);
	}

	public function confirm($user_id){
		return $this -> update(['confirmed' => true], ['id', '=', $user_id]);
	}

	public function withdraw_confirmation($user_id){
		return $this -> update(['confirmed' => false], ['id', '=', $user_id]);
	}

	public function activate_account($user_id){
		return $this -> update(['active' => true], ['id', '=', $user_id]);
	}

	public function deactivate_account($user_id){
		return $this -> update(['active' => false], ['id', '=', $user_id]);
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
		$user_data = $this -> account_data_filter($user_data);
		return $this -> update($user_data, ['id', '=', $user_id]);
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