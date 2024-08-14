<?php

// User routes
$router->get('api/v1/users', 'UsersController@index', 'admin');
$router->get('api/v1/users/{uuid}', 'UsersController@show', 'admin');
$router->get('api/v1/profile', 'UsersController@getUser', 'auth');

$router->put('api/v1/profile', 'UsersController@update', 'auth');
$router->put('api/v1/users/{uuid}', 'UsersController@updateByAdmin', 'admin');

$router->delete('api/v1/users/{uuid}', 'UsersController@destroy', 'admin');

// Blog routes
$router->get('api/v1/blogs', 'BlogsController@index', 'admin');
$router->get('api/v1/blogs/{uuid}', 'BlogsController@show', 'admin');

$router->post('api/v1/blogs', 'BlogsController@store', 'admin');

$router->put('api/v1/blogs/{uuid}', 'BlogsController@update', 'admin');

$router->delete('api/v1/blogs/{uuid}', 'BlogsController@destroy', 'admin');

// Quiz routes
$router->get('api/v1/quizzes', 'QuizController@index', 'admin');
$router->get('api/v1/quizzes/{uuid}', 'QuizController@show', 'admin');

$router->post('api/v1/quizzes', 'QuizController@store', 'admin');

$router->put('api/v1/quizzes/{uuid}', 'QuizController@update', 'admin');

$router->delete('api/v1/quizzes/{uuid}', 'QuizController@update', 'admin');

// Question Routes
$router->get('api/v1/quizzes/{QuizUuid}/questions', 'QuestionsController@index', 'admin');

$router->post('api/v1/quizzes/{QuizUuid}/questions', 'QuestionsController@store', 'admin');

$router->put('api/v1/questions/{uuid}', 'QuestionsController@update', 'admin');

$router->delete('api/v1/questions/{uuid}', 'QuestionsController@update', 'admin');

// Options Route
$router->get('api/v1/questions/{QuestionUuid}/options', 'QuestionsController@show', 'admin');

$router->post('api/v1/questions/{QuestionUuid}/options', 'QuestionsController@store', 'admin');

$router->put('api/v1/options/{uuid}', 'QuestionsController@update', 'admin');

$router->delete('api/v1/options/{uuid}', 'QuestionsController@delete', 'admin');

// User - Quiz Interaction Routes
$router->post('api/v1/quizzes/{quizUuid}/attempts', 'QuizController@attempt', 'auth');
$router->post('api/v1/attempts/{attemptUuid}/submit', 'QuizController@answers', 'auth');

$router->get('api/v1/quizzes/{quizUuid}/scores', 'QuizController@answers', 'auth');

// Auth Routes
$router->post('api/v1/auth/register', 'AuthController@store', 'guest'); // Test Passed
$router->post('api/v1/auth/login', 'AuthController@login', 'guest'); // Test Passed
$router->post('api/v1/auth/logout', 'AuthController@logout', 'auth'); // Test Passed
$router->post('api/v1/auth/resend', 'AuthController@resend', 'guest'); // Test Passed
$router->post('api/v1/auth/verify', 'AuthController@verify', 'new'); // Test Passed
$router->post('api/v1/auth/request', 'AuthController@request', 'guest'); // Test Passed
$router->post('api/v1/auth/reset', 'AuthController@reset', 'new');// Test Passed