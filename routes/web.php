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

$router->group(['middleware' => 'core'], function () use ($router) {
    $router->get('test', 'TestController@test');
    $router->post('test', 'TestController@test');
});

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

// 官网的路由
$router->group(['namespace' => 'Official', 'prefix' => 'official'], function () use ($router) {
    $router->group(['namespace' => 'Job', 'prefix' => 'job'], function () use ($router) {
        $router->post('list', 'JobController@list');
        $router->post('info', 'JobController@info');
    });
});

// 官网后台管理
$router->group(['namespace' => 'Admin', 'prefix' => 'admin'], function () use ($router) {
    $router->group(['namespace' => 'Job', 'prefix' => 'job'], function () use ($router) {
        $router->post('list', 'JobController@list');
        $router->post('info', 'JobController@info');
        $router->post('save', 'JobController@save');
        $router->post('del', 'JobController@del');
    });
    $router->group(['namespace' => 'Feedback', 'prefix' => 'feedback'], function () use ($router) {
        $router->post('list', 'FeedbackController@list');
        $router->post('info', 'FeedbackController@info');
        $router->post('del', 'FeedbackController@del');
    });
    $router->group(['namespace' => 'Perpre', 'prefix' => 'perpre'], function () use ($router) {
        $router->post('save', 'PerpreController@save');
        $router->post('info', 'PerpreController@info');
        $router->post('list', 'PerpreController@list');
        $router->post('del', 'PerpreController@del');
    });
});


$router->group(['namespace' => 'Mini', 'prefix' => 'mini', 'middleware' => 'core'], function () use ($router) {
    $router->group(['namespace' => 'Auth', 'prefix' => 'auth'], function () use ($router) {
        $router->post('openid', 'AuthController@openid');
        $router->post('login', 'AuthController@login');
    });
    $router->group(['namespace' => 'Goods', 'prefix' => 'goods'], function () use ($router) {
        $router->post('list', 'GoodsController@list');
    });
    $router->group(['namespace' => 'Classi', 'prefix' => 'class'], function () use ($router) {
        $router->post('list', 'ClassiController@list');
    });
    $router->group(['namespace' => 'Address', 'prefix' => 'address'], function () use ($router) {
        $router->post('save', 'AddressController@save');
        $router->post('list', 'AddressController@list');
    });
    $router->group(['namespace' => 'User', 'prefix' => 'user', 'middleware' => 'miniauth'], function () use ($router) {
        $router->post('save', 'UserController@save');
        $router->post('info', 'UserController@info');
    });
});
