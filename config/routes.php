<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 */
$routes = array(
    '/' => 'application#index',                      // default route
    '/grid' => 'application#index',                  // index (view_all_in_grid)
    '/kind' => 'application#columnKind',              // column grouped by kind
    '/progress' => 'application#columnProgress',      // column grouped by progress

	'/task/create' => 'task#create',
    '/task/show' => 'task#show',
    '/task/update' => 'task#update',
    '/task/delete' => 'task#delete',
    '/task/advance' => 'task#upgradeProgress',
);
?>