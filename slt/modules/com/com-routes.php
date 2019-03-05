<?php 
// pages
route('/com', 'Modules\\Com\\ComController@dashboard');
route('/com/about', 'Modules\\Com\\ComController@about');

// api
route('/com/create/controller/{name}', 'Modules\\Com\\ComController@createController');
route('/com/create/model/{name}', 'Modules\\Com\\ComController@createModel');
route('/com/create/migration/{name}', 'Modules\\Com\\ComController@createMigration');
route('/com/migrations/up/{name}', 'Modules\\Com\\ComController@migrationUp');
route('/com/migrations/down/{name}', 'Modules\\Com\\ComController@migrationDown');
route('/com/migrations/up', 'Modules\\Com\\ComController@migrationUpAll');
route('/com/migrations/down', 'Modules\\Com\\ComController@migrationDownAll');
route('/com/migrations/list', 'Modules\\Com\\ComController@migrationList');
route('/com/migrations/refresh/{name}', 'Modules\\Com\\ComController@migrationRefresh');
route('/com/migrations/refresh', 'Modules\\Com\\ComController@migrationRefreshAll');