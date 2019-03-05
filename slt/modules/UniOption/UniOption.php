<?php

namespace Modules\UniOption;

use \Kernel\Maker;
use \Kernel\Module;
use \Kernel\Events;
use \Kernel\DBIO;

class UniOption{
	public $p2m;

	public function __construct(){
		$this -> p2m = Module::pathToModule('UniOption');
		Events::on('ready_connect_to_db', function(){
			$exists_table = cache_code('UniOption.install_flag', function(){
				return DBIO::table_exists('UniOption');
			});

			if(!$exists_table){
				$this -> set_migration();
				echo "UniOption install table db";
			}
		});

		include_once($this -> p2m . '/unioption.helper.php');
	}

	public function set_migration(){
		Maker::migration_up('UniOption', $this -> p2m . '/Migrations/');
	}
}