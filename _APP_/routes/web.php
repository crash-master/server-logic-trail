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
route('ExampleController');