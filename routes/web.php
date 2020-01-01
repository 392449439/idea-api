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

    return '想法墙'.$router->app->version();
});

$router->group([], function () use ($router) {
    $router->get('test', 'TestController@test');
    $router->get('out', 'TestController@out');
    $router->get('outStoreList', 'TestController@outStoreList');
    $router->get('test2', 'Test2Controller@test');
    $router->get('test/print', 'TestController@print');
});

$router->group(['namespace' => 'Auth', 'prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
});

// $router->group(['middleware' => 'core'], function () use ($router) {
$router->group(['namespace' => 'File', 'prefix' => 'file'], function () use ($router) {
    $router->post('upload', 'FileController@upload');
});
// });


$router->group(['namespace' => 'Auth', 'prefix' => 'auth', 'middleware' => 'auth'], function () use ($router) {
    $router->get('create', 'AuthController@create');
});


// $router->group(['namespace' => 'User', 'prefix' => 'user', 'middleware' => 'auth'], function () use ($router) {
//     $router->post('save', 'UserController@save');
//     $router->post('info', 'UserController@info');
//     $router->post('list', 'UserController@list');
//     $router->post('del', 'UserController@del');
// });

// Feedback


// ideaH5端api代码

$router->group(['namespace' => 'Admin' , 'prefix' => 'admin'] , function () use ($router) {
    $router->group(['namespace' => 'Idea' , 'prefix' => 'idea'] , function () use ($router) {
        $router->post('save','IdeaController@save');
        $router->post('list','IdeaController@list');
        $router->post('info','IdeaController@info');
        $router->post('del','IdeaController@del');
    });
});
