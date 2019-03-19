<?php

namespace Modules\Sysinfo;

use Kernel\Module;
use Kernel\Events;

class Sysinfo{
	public $p2m;
	public $state;
	public $sql_queries = [];
	public $components_list = [];
	public $current_controller;
	public $cache_use_list = [];

	public function __construct(){
		$this -> state = isset($_GET['sysinfo']) ? true : false;
		$this -> p2m = Module::pathToModule('Sysinfo');
		if($this -> state){
			$this -> set_event_listeners();
		}
	}

	public function set_event_listeners(){
		Events::on('app_finished', function(){
			module('Sysinfo') -> last_logic_and_draw_info();
		});

		Events::on('ready_sql_query_string', function($sql){
			module('Sysinfo') -> add_sql_query_to_report($sql);
		});

		Events::on('call_component', function($component){
			module('Sysinfo') -> add_to_components_list($component);
		});

		Events::on('call_action', function($controller){
			module('Sysinfo') -> current_controller = $controller;
		});

		Events::on('cache_data_used', function($cache){
			module('Sysinfo') -> add_to_cache_use_list($cache);
		});
	}

	public function last_logic_and_draw_info(){
		$report = [];
		$report['work_time'] = round(microtime(true) - SLT_START_POINT_TIME, 3);
		$report['total_sql_queries'] = count($this -> sql_queries);
		$report['sql_queries_list'] = $this -> sql_queries;
		$report['components_list'] = $this -> components_list;
		$report['total_components_for_page'] = count($this -> components_list);
		$report['controller_for_this_page'] = $this -> current_controller;
		$report['cache_use_list'] = $this -> cache_use_list;
		$report['total_files_of_cache_used'] = count($this -> cache_use_list);

		$this -> draw_info($report);
	}

	public function draw_info($report){
		echo view($this -> p2m . 'view/report', $report);
	}

	public function add_sql_query_to_report($sql){
		$this -> sql_queries[] = $sql;
	}

	public function add_to_components_list($component){
		$this -> components_list[] = $component;
	}

	public function add_to_cache_use_list($cache){
		$this -> cache_use_list[] = $cache['cache_alias'];
	}
}