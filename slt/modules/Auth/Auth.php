<?php

namespace Modules\Auth;

use Kernel\Module;
use Kernel\Maker\Maker;
use Kernel\DBIO;
use Kernel\CodeTemplate;
use Kernel\Sess;
use Kernel\Events;

class Auth{
	/**
	 * [$module_name "Name of this module"]
	 *
	 * @var string
	 */
	public $module_name = 'Auth';

	/**
	 * [main field from user card, used for sign in]
	 *
	 * @var string
	 */
	public $signin_field_name;

	/**
	 * [minimal length of password for signup]
	 *
	 * @var int
	 */
	public $min_password_length;

	/**
	 * [exists roles]
	 *
	 * @var [array string]
	 */
	public $role_list;

	/**
	 * [$err_messages is container for error messages]
	 *
	 * @var [array string]
	 */
	public $err_messages;

	/**
	 * [$p2m "Path to this module"]
	 *
	 * @var [string]
	 */
	public $p2m;

	/**
	 * [Container for redirect map]
	 *
	 * @var [array]
	 */
	private $redirect_map = [];

	public $default_form_control = [];

	public $use_default_pages = [
		'signin' => true,
		'signup' => true,
		'signout' => true
	];

	public $page_view = [];

	public function __construct(){
		global $SLT_APP_NAME;
		$path_to_settings_file = $SLT_APP_NAME . '/auth.settings.php';
		$this -> p2m = Module::pathToModule($this -> module_name);
		include_once($this -> p2m . 'helpers.php');
		include_once($this -> p2m . 'functions.php');
		include_once($this -> p2m . 'routes.map.php');
		auth_routes_map();
		if(file_exists($path_to_settings_file)){
			include_once($path_to_settings_file);
			$auth_config = auth_config();
			$this -> signin_field_name = (isset($auth_config['signin_field_name']) and !empty($auth_config['signin_field_name'])) ? $auth_config['signin_field_name'] : 'nickname';
			$this -> min_password_length = (isset($auth_config['min_password_length']) and $auth_config['min_password_length']) ? $auth_config['min_password_length'] : 6;
			$this -> role_list = auth_role_list();
			$this -> err_messages = auth_error_messages();
			$this -> default_form_control = auth_default_form_control();
			
			if(isset($auth_config['use_default_pages']) and is_array($auth_config['use_default_pages'])){
				foreach ($auth_config['use_default_pages'] as $key => $value) {
					if(!isset($this -> use_default_pages[$key])){
						continue;
					}
					$this -> use_default_pages[$key] = $value;
				}
			}

			if(isset($auth_config['page_view']) and is_array($auth_config['page_view'])){
				$this -> page_view = $auth_config['page_view'];
			}

			$this -> redirect_controll();

			$auth_events_file = $SLT_APP_NAME . '/auth.events.map.php';
			if(file_exists($auth_events_file)){
				include_once($auth_events_file);
				auth_events_map();
			}
		}else{
			echo '(Auth module) Installation required <a href="/auth/install">Installation</a>';
		}
	}

	/**
	 * [Installation module]
	 *
	 * @method install
	 *
	 * @return [bool] []
	 */
	public function install(){
		global $SLT_APP_NAME, $SLT_DEBUG;
		if($SLT_DEBUG == 'off'){
			return false;
		}
		Maker::migration_up('Users', $this -> p2m . 'migrations/');
		$settings_file = $SLT_APP_NAME . '/auth.settings.php';
		if(!file_exists($settings_file)){
			CodeTemplate::create('auth.settings', ['filename' => 'auth.settings'], $this -> p2m . 'codetemplates/', $SLT_APP_NAME . '/');
		}

		$auth_events_map_file = $SLT_APP_NAME . '/auth.events.map.php';         
		if(!file_exists($auth_events_map_file)){
			CodeTemplate::create('auth.events.map', ['filename' => 'auth.events.map'], $this -> p2m . 'codetemplates/', $SLT_APP_NAME . '/');
		}

		$Auth_file = $SLT_APP_NAME . '/Auth.php';
		if(!file_exists($Auth_file)){
			CodeTemplate::create('Auth', ['filename' => 'Auth'], $this -> p2m . 'codetemplates/', $SLT_APP_NAME . '/middleware/modulesevents/');
		}

		return true;
	}

	/**
	 * [uninstallation module]
	 *
	 * @method uninstall
	 *
	 * @return [bool] []
	 */
	public function uninstall(){
		global $SLT_APP_NAME, $SLT_DEBUG;
		if($SLT_DEBUG == 'off'){
			return false;
		}
		Maker::migration_down('Users', $this -> p2m . 'migrations/');
		$settings_file = $SLT_APP_NAME . '/auth.settings.php';

		return true;
	}

	/**
	 * [method for signup]
	 *
	 * @method signup
	 *
	 * @param  [array] $user [array with userd data]
	 *
	 * @return [bool] [description]
	 */
	public function signup($user){
		return model('Modules\Auth\Models\Users') -> signup($user);
	}

	/**
	 * [method for signin]
	 *
	 * @method signin
	 *
	 * @param  [array] $user [assoc array with user data for signin]
	 *
	 * @return [bool] [description]
	 */
	public function signin($user){
		return model('Modules\Auth\Models\Users') -> signin($user);
	}

	/**
	 * [get error message by error code]
	 *
	 * @method get_err_by_errcode
	 *
	 * @param  [int] $errcode [error code]
	 *
	 * @return [string] [return error message or false]
	 */
	public function get_err_by_errcode($errcode){
		$err_messages = $this -> err_messages;
		return (is_int($errcode) and isset($err_messages[$errcode])) ? $err_messages[$errcode] : false;
	}

	/**
	 * [get current signin user card]
	 *
	 * @method current_signin
	 *
	 * @return [array] [return user card from session]
	 */
	public function current_signin(){
		return Sess::get('user_card');
	}

	/**
	 * [method for signout]
	 *
	 * @method signout
	 *
	 * @return [bool] [description]
	 */
	public function signout(){
		Sess::kill('user_card');
		Events::register('auth_signout', ['user_card' => Sess::get('user_card')]);
		return true;
	}

	/**
	 * [check signin session]
	 *
	 * @method is_signined
	 *
	 * @return boolean [description]
	 */
	public function is_signined(){
		$user = $this -> current_signin();
		return is_array($user) and $user['id'];
	}

	/**
	 * [confirm account]
	 *
	 * @method confirm
	 *
	 * @param  [int] $user_id [user id]
	 *
	 * @return [bool] [result]
	 */
	public function confirm($user_id){
		return model('Modules\Auth\Models\Users') -> confirm($user_id);
	}

	/**
	 * [withdraw confirmation account]
	 *
	 * @method withdraw_confirmation
	 *
	 * @param  [int] $user_id [user id]
	 *
	 * @return [bool] [result]
	 */
	public function withdraw_confirmation($user_id){
		return model('Modules\Auth\Models\Users') -> withdraw_confirmation($user_id);
	}

	/**
	 * [activate account by user id]
	 *
	 * @method activate_account
	 *
	 * @param  [int] $user_id [user id]
	 *
	 * @return [bool] [result]
	 */
	public function activate_account($user_id){
		return model('Modules\Auth\Models\Users') -> activate_account($user_id);
	}

	/**
	 * [deactivate account]
	 *
	 * @method deactivate_account
	 *
	 * @param  [tyintpe] $user_id [user id]
	 *
	 * @return [bool] [result]
	 */
	public function deactivate_account($user_id){
		return model('Modules\Auth\Models\Users') -> deactivate_account($user_id);
	}

	/**
	 * [change account data, only access fields]
	 *
	 * @method change_account_data
	 *
	 * @param  [int] $user_id [user id]
	 * @param  [array] $user_data [data of user]
	 *
	 * @return [bool] [result]
	 */
	public function change_account_data($user_id, $user_data){
		return model('Modules\Auth\Models\Users') -> change_account_data($user_id, $user_data);
	}

	/**
	 * [method for changing account role]
	 *
	 * @method change_account_role
	 *
	 * @param  [int] $user_id [user id]
	 * @param  [string] $new_role [new acount role]
	 *
	 * @return [bool] [result]
	 */
	public function change_account_role($user_id, $new_role){
		return model('Modules\Auth\Models\Users') -> change_account_role($user_id, $new_role);
	}

	private function redirect_controll(){
		$auth = $this;
		\Kernel\Events::on('call_action', function($p) use ($auth){
			$current_route = urlto($p['controller'] . '@' . $p['action']);
			auth_redirect_map();
			if($this -> redirect_map[$current_route][0] === true){
				redirect($this -> redirect_map[$current_route][1]);
			}
		});
	}

	public function new_redirect($route, $access, $new_route){
		$this -> redirect_map[$route] = [$access, $new_route];
		return true;
	}

	public function get_existed_roles(){
		return $this -> role_list;
	}

}