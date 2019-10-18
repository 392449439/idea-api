<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Carbon\Carbon;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/test', 'TestController@test');

$router->group(['namespace' => 'Auth', 'prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
});


$router->group(['namespace' => 'Auth', 'prefix' => 'auth', 'middleware' => 'auth'], function () use ($router) {
    $router->get('create', 'AuthController@create');
});


$router->group(['namespace' => 'User', 'prefix' => 'user', 'middleware' => 'auth'], function () use ($router) {
    $router->post('save', 'UserController@save');
    $router->post('info', 'UserController@info');
    $router->post('list', 'UserController@list');
    $router->post('del', 'UserController@del');
});

// Feedback


$router->group(['namespace' => 'Feedback', 'prefix' => 'feedback'], function () use ($router) {
    $router->post('save', 'FeedbackController@save');
});

// job
$router->group(['namespace' => 'Job', 'prefix' => 'official/job'], function () use ($router) {
    $router->post('list', 'JobController@list');
    $router->post('info', 'JobController@info');
});
