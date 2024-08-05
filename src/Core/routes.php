<?php

// User routes
$router->get('api/v1/users', 'UsersController@index', 'admin');
$router->get('api/v1/users/{uuid}', 'UsersController@show', 'admin');
$router->get('api/v1/profile', 'UsersController@getUser', 'auth');

$router->put('api/v1/profile', 'UsersController@update', 'auth');
$router->put('api/v1/users/{uuid}', 'UsersController@updateByAdmin', 'admin');

$router->delete('api/v1/users/{uuid}', 'UsersController@destroy', 'admin');

// Event routes
$router->get('api/v1/events', 'EventsController@index', 'admin');
$router->get('api/v1/events/{uuid}', 'EventsController@show', 'admin');

$router->post('api/v1/events', 'EventsController@store', 'admin');

$router->put('api/v1/events/{uuid}', 'EventsController@update', 'admin');

$router->delete('api/v1/events/{uuid}', 'EventsController@destroy', 'admin');

// Auth Routes
$router->post('api/v1/auth/register', 'UsersController@store', 'guest'); // Test Passed
$router->post('api/v1/auth/login', 'UsersController@login', 'guest'); // Test Passed
$router->post('api/v1/auth/logout', 'UsersController@logout', 'auth'); // Test Passed
$router->post('api/v1/auth/resend', 'UsersController@resend', 'guest'); // Test Passed
$router->post('api/v1/auth/verify', 'UsersController@verify', 'new'); // Test Passed
$router->post('api/v1/auth/request', 'UsersController@request', 'guest');
$router->post('api/v1/auth/reset', 'UsersController@reset', 'new');