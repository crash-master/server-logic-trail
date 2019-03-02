<?php

use Kernel\{
	DBW
};

class ImgsStorageMigration extends \Extensions\Migration{

	public function up(){
		DBW::create('ImgsStorage',function($t){
			$t -> int('imgsb64_id')
			-> varchar('title')
			-> varchar('type')
			-> text('description')
			-> timestamp('date_of_update')
			-> timestamp('date_of_create');
		});
		return true;
	}

	public function down(){
		DBW::drop('ImgsStorage');
		return true;
	}

}

