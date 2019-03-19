<?php

/*  /controllers/ */

use \Modules\UniOption\Models\UniOption;

class TestController extends \Extensions\Controller{
	public function test(){
		return UniOption::ins() -> get_by_section_name('Hello section');
	}
}