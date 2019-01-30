<?php 
// pages
route('/com', 'Modules\\ComController@dashboard');
route('/com/about', 'Modules\\ComController@about');

// api
route('/com/create/controller/{name}', 'Modules\\ComController@createController');
route('/com/create/model/{name}', 'Modules\\ComController@createModel');
route('/com/create/set/{name}', 'Modules\\ComController@createSet');
route('/com/create/migration/{name}', 'Modules\\ComController@createMigration');
route('/com/migrations/up/{name}', 'Modules\\ComController@migrationUp');
route('/com/migrations/down/{name}', 'Modules\\ComController@migrationDown');
route('/com/migrations/up', 'Modules\\ComController@migrationUpAll');
route('/com/migrations/down', 'Modules\\ComController@migrationDownAll');
route('/com/migrations/list', 'Modules\\ComController@migrationList');
route('/com/migrations/refresh/{name}', 'Modules\\ComController@migrationRefresh');
route('/com/migrations/refresh', 'Modules\\ComController@migrationRefreshAll');