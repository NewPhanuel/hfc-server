<?php

// User routes
$router->get('api/v1/users', 'UsersController@index', 'admin');
$router->get('api/v1/users/{uuid}', 'UsersController@show', 'admin');
$router->get('api/v1/users/me', 'UsersController@show', 'auth');

$router->post('api/v1/users', 'UsersController@store', 'guest');

$router->put('api/v1/users', 'UsersController@update', 'auth');

$router->delete('api/v1/users/{uuid}', 'UsersController@destroy', 'admin');

// Event routes
$router->get('api/v1/events', 'EventsController@index', 'admin');
$router->get('api/v1/events/{uuid}', 'EventsController@show', 'admin');

$router->post('api/v1/events', 'EventsController@store', 'admin');

$router->put('api/v1/events/{uuid}', 'EventsController@update', 'admin');

$router->delete('api/v1/events/{uuid}', 'EventsController@destroy', 'admin');

// Auth Routes
$router->post('api/v1/auth/login', 'UsersController@login', 'guest');
$router->post('api/v1/auth/logout', 'UsersController@logout', 'auth');