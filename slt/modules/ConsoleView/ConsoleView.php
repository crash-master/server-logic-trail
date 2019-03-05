<?php

namespace Modules\ConsoleView;
use Modules\ConsoleView\Libs\Console_table;

class ConsoleView{
	private $Console_Table_instance;

	public function new_table(){
		$this -> Console_Table_instance = new Console_Table();
		return $this -> Console_Table_instance;
	}

	public function current_table(){
		return $this -> Console_Table_instance;
	}
}