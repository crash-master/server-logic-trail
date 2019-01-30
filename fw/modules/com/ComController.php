<?php

namespace Modules;

use Kernel\{
	View,
	Router,
	Model,
	Module,
	Events,
	Err,
	Log,
	Components,
	CodeTemplate,
	Config,
	Maker,
	PackageControll
};

class ComController{

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
	
	public function createSet($name){
		if($name){
			CodeTemplate::create('set', ['setname' => $name, 'tablename' => $name, 'filename' => $name.'Set']);
			return 'TRUE';
		}

		return 'FALSE';
	}
	
	public function createModel($name){
		if($name){
			CodeTemplate::create('model', ['modelname' => $name, 'setname' => $name, 'filename' => $name]);
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
	   return $this -> migrationUpAll();
	}

	public function migrationRefresh($name){
		$res = $this -> migrationDown($name);
		if($res == 'TRUE'){
			$res = $this -> migrationUp($name);
		}

		return $res;
	}
	
	public function migrationUpAll(){
		if(Config::get('system -> migration') == 'on'){
			if(Maker::refreshMigration())
				return 'TRUE';
		}

		throw new Exception('Migrations is off in config');
		return 'FALSE';
		
	}
	
	public function migrationDownAll(){
		if(Config::get('system -> migration') == 'on'){
			if(Maker::unsetAllMigration())
				return 'TRUE';
		}

		throw new Exception('Migrations is off in config');
		return 'FALSE';

	}
	
	public function migrationDown($name){
		if(Config::get('system -> migration') == 'on'){
			if(!file_exists('app/migrations/'.$name.'Migration.php')){
				return 'FALSE';
			}
			
			if(Maker::unsetMigration([NULL, $name])){
				return 'TRUE';
			}
		}

		throw new Exception('Migrations is off in config');
		throw new Exception("Migration {$name} was not unset");
		
		return 'TRUE';
	}
	
	public function migrationUp($name){
		if(!file_exists('app/migrations/'.$name.'Migration.php')){
			return 'FALSE';
		}

		if(!Maker::setMigration([NULL, $name])){
			throw new Exception('ERR Com',"Migration {$name} was not unset");
			return 'FALSE';
		}

		return 'TRUE';
	}

	public function getMigrationList_component(){
		$migs = Maker::getMigrationList();

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