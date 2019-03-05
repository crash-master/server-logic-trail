<?php
use Kernel\Components;

$p2com = Kernel\Module::pathToModule('com');

Components::create('ComponentList', [$p2com.'view/components/component-list' => 
	'Modules\\Com\\ComController@getComponentList_component'
]);

Components::create('RouteList', [$p2com.'view/components/route-list' => 
	'Modules\\Com\\ComController@getRouteList_component'
]);

Components::create('MigrationList', [$p2com.'view/components/migration-list' => 
	'Modules\\Com\\ComController@getMigrationList_component'
]);

Components::create('EventList', [$p2com.'view/components/event-list' => 
	'Modules\\Com\\ComController@getEventList_component'
]);