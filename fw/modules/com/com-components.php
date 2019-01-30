<?php
$p2com = Kernel\Module::pathToModule('com');

Kernel\Components::create('ComponentList', [$p2com.'view/components/component-list' => 
	'Modules\ComController@getComponentList_component'
]);

Kernel\Components::create('RouteList', [$p2com.'view/components/route-list' => 
	'Modules\ComController@getRouteList_component'
]);

Kernel\Components::create('MigrationList', [$p2com.'view/components/migration-list' => 
	'Modules\ComController@getMigrationList_component'
]);

Kernel\Components::create('EventList', [$p2com.'view/components/event-list' => 
	'Modules\ComController@getEventList_component'
]);