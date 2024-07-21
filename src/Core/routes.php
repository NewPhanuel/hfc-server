<?php

// User routes
$router->get('api/v1/users', 'UsersController@index');
$router->get('api/v1/users/{uuid}', 'UsersController@show');

$router->post('api/v1/users', 'UsersController@store');

$router->put('api/v1/users/{uuid}', 'UsersController@update');

$router->delete('api/v1/users/{uuid}', 'UsersController@destroy');

// Event routes
$router->get('api/v1/events', 'EventsController@index');
$router->get('api/v1/events/{uuid}', 'EventsController@show');

$router->post('api/v1/events', 'EventsController@store');

$router->put('api/v1/events/{uuid}', 'EventsController@update');

$router->delete('api/v1/events/{uuid}', 'EventsController@destroy');