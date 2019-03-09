<?php

namespace Modules\Com\Controllers;

use \Kernel\CodeTemplate;
use \Kernel\Maker;

class ComController extends \Extensions\Controller{
	public function create_controller($with_name){
		if($with_name){
			CodeTemplate::create('controller', ['name' => $with_name, 'filename' => $with_name.'Controller']);
		}else{
			throw new \Exception("!Bad controller name!");
		}

		return "# {$with_name}Controller was created\n";
	}

	public function create_model($with_name){
		if($with_name){
			CodeTemplate::create('model', ['modelname' => $with_name, 'tablename' => $with_name, 'filename' => $with_name]);
		}else{
			throw new \Exception("!Bad model name!");
		}

		return "# Model < {$with_name} > was created\n";
	}

	public function create_migration($with_name){
		if($with_name){
			CodeTemplate::create('migration', ['name' => $with_name, 'filename' => $with_name.'Migration']);
		}else{
			throw new \Exception("!Bad migration name!");
		}

		return "# {$with_name}Migration was created\n";
	}

	public function migration_up($with_name){
		if(!Maker::migration_up($with_name, null, true)){
			throw new \Exception("!Migration {$with_name} was not raised!");
		}

		return "# {$with_name} migration was raised\n";
	}

	public function migration_down($with_name){
		if(!Maker::migration_down($with_name)){
			throw new \Exception("!Migration {$with_name} was not omitted!");
		}

		return "# {$with_name} migration was omitted\n";
	}

	public function migration_up_all(){
		if(Maker::migration_up_all(null, true)){
			$migs = Maker::migrations_list();
			$print_out = '';
			foreach($migs as $item){
				$print_out .= "# {$item['name']} was raised\n";
			}
		}else{
			throw new \Exception("!Some thing wrong!");
		}

		return $print_out;
	}

	public function migration_down_all(){
		if(Maker::migration_down_all()){
			$migs = Maker::migrations_list();
			$print_out = '';
			foreach($migs as $item){
				$print_out .= "# {$item['name']} was omitted\n";
			}
		}else{
			throw new \Exception("!Some thing wrong!");
		}

		return $print_out;
	}

	public function show_migrations_list(){
		$migs = Maker::migrations_list();
		$print_out = "-- Migrations list --\n";
		foreach($migs as $item){
			$print_out .= "# {$item['name']} migration\n";
		}

		return $print_out;
	}
}