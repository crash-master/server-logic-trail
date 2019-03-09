<?php

use \Kernel\Console\Console;

Console::action('\Modules\Com\Controllers\ComController@create_controller');
Console::action('\Modules\Com\Controllers\ComController@create_model');
Console::action('\Modules\Com\Controllers\ComController@create_migration');
Console::action('\Modules\Com\Controllers\ComController@migration_up');
Console::action('\Modules\Com\Controllers\ComController@migration_down');
Console::route('com.migration-up-all', '\Modules\Com\Controllers\ComController@migration_up_all');
Console::route('com.migration-down-all', '\Modules\Com\Controllers\ComController@migration_down_all');

Console::route('com.migrations', '\Modules\Com\Controllers\ComController@show_migrations_list');
Console::route('com.show-migrations-list', '\Modules\Com\Controllers\ComController@show_migrations_list');