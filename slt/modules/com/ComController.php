<?php

namespace Modules;

use Kernel\{
	View,
	Router,
	Model,
	Module,
	Events,
	Err,
	Components,
	CodeTemplate,
	Config,
	Maker
};

class ComController extends \Extensions\Controller{

	public function dashboard(){
		return view(Module::pathToModule('com').'view/dashboard', [
			'breadcrumbs' => ['Com' => '/com']
		]);
	}

	public function about(){
		return view(Module::pathToModule('com').'view/about');
	}
	
	public function getEventList_component(){
		$events = Events::getList();
		return ['events' => $events];
	}

	public function getComponentList_component(){
		return [
			'components' => Components::getAll()
		];
	}
	
	public function createController($name){
		if($name){
			CodeTemplate::create('controller', ['name' => $name, 'filename' => $name.'Controller']);
			return 'TRUE';
		}
		
		return 'FALSE';
	}
	
	public function createModel($name){
		if($name){
			CodeTemplate::create('model', ['modelname' => $name, 'tablename' => $name, 'filename' => $name]);
			return 'TRUE';
		}

		return 'FALSE';
	}
	
	public function createMigration($name){
		if($name){
			CodeTemplate::create('migration', ['name' => $name, 'filename' => $name.'Migration']);
			return 'TRUE';
		}

		return 'FALSE';
	}

	public function migrationRefreshAll(){
	   return Maker::migration_refresh_all();
	}

	public function migrationRefresh($name){
		$res = Maker::migration_refresh($name);

		return $res;
	}
	
	public function migrationUpAll(){
		if(Config::get('system -> migration') == 'on'){
			if(Maker::migration_up_all(null, true))
				return 'TRUE';
		}

		throw new \Exception('Migrations is off in config');
		return 'FALSE';
		
	}
	
	public function migrationDownAll(){
		if(Config::get('system -> migration') == 'on'){
			if(Maker::migration_down_all())
				return 'TRUE';
		}

		throw new \Exception('Migrations is off in config');
		return 'FALSE';

	}
	
	public function migrationDown($name){
		if(Config::get('system -> migration') == 'on'){
			if(Maker::migration_down($name)){
				return 'TRUE';
			}else{
				throw new \Exception("Migration {$name} was not unset");
			}
		}

		throw new \Exception('Migrations is off in config');
		
		return 'TRUE';
	}
	
	public function migrationUp($name){
		if(!Maker::migration_up($name, null, true)){
			throw new \Exception("Migration {$name} was not unset");
			return 'FALSE';
		}

		return 'TRUE';
	}

	public function getMigrationList_component(){
		$migs = Maker::migrations_list();

		return [
			'migrations' => $migs
		];
	}

	public function getRouteList_component(){
		global $COM_BACKUP_ROUTES;
		$cont = $COM_BACKUP_ROUTES;
		return [
			'routes' => $cont
		];
	}
	
}