<?php
use Kernel\Router;
/*
*   Router::_404(action_name)
*   Router::get('route', 'action');
*   Router::post('field', 'action', 'route'?);
*   Router::actions(array_actions)
*   Router::controller(controller_name, [only])
*/

route_not_found('IndexController@not_found_page');
route('/', 'IndexController@welcome_page');
route('TestController', ['head_component']);
route('/test/new-entry/{entry}', 'TestController@new_entry');
route('/test/update-entry/{id}/{new_entry}', 'TestController@update_entry');
route('/cache/test', 'IndexController@cache_test');
