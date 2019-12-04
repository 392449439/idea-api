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

$router->group(['middleware' => 'core'], function () use ($router) {
    $router->group(['namespace' => 'File', 'prefix' => 'file', 'middleware' => 'auth'], function () use ($router) {
        $router->post('upload', 'FileController@upload');
    });
});


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
$router->group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => 'core'], function () use ($router) {
    $router->group(['namespace' => 'Job', 'prefix' => 'job'], function () use ($router) {
        $router->post('list', 'JobController@list');
        $router->post('info', 'JobController@info');
        $router->post('save', 'JobController@save');
        $router->post('del', 'JobController@del');
    });
    $router->group(['namespace' => 'Order', 'prefix' => 'order'], function () use ($router) {
        $router->post('list', 'OrderController@list');
        $router->post('info', 'OrderController@info');
        $router->post('dada/test', 'OrderController@dadaTest');
        $router->post('dada/order/test', 'OrderController@dadaOrderTest');
        $router->post('dada/order/info', 'OrderController@dadaOrderInfo');
    });
    $router->group(['namespace' => 'Classi', 'prefix' => 'class'], function () use ($router) {
        $router->post('list', 'ClassiController@list');
        $router->post('del', 'ClassiController@del');
        $router->post('save', 'ClassiController@save');
        $router->post('info', 'ClassiController@info');
    });
    $router->group(['namespace' => 'Feedback', 'prefix' => 'feedback'], function () use ($router) {
        $router->post('list', 'FeedbackController@list');
        $router->post('info', 'FeedbackController@info');
        $router->post('del', 'FeedbackController@del');
    });
    $router->group(['namespace' => 'Paper', 'prefix' => 'paper'], function () use ($router) {
        $router->post('save', 'PaperController@save');
        $router->post('info', 'PaperController@info');
        $router->post('list', 'PaperController@list');
        $router->post('del', 'PaperController@del');
    });
    $router->group(['namespace' => 'Store', 'prefix' => 'store'], function () use ($router) {
        $router->post('save', 'StoreController@save');
        $router->post('list', 'StoreController@list');
        $router->post('info', 'StoreController@info');
        $router->post('del', 'StoreController@del');
        $router->post('app/list', 'StoreController@appList');
        $router->post('app/link', 'StoreController@linkApp');
        $router->post('app/unlink', 'StoreController@unlinkApp');
    });
    $router->group(['namespace' => 'App', 'prefix' => 'app'], function () use ($router) {
        $router->post('save', 'AppController@save');
        $router->post('list', 'AppController@list');
        $router->post('info', 'AppController@info');
        $router->post('del', 'AppController@del');
        $router->post('store/list', 'AppController@storeList');
        $router->post('store/link', 'AppController@link');
        $router->post('store/unlink', 'AppController@unlink');
    });
    $router->group(['namespace' => 'Domain', 'prefix' => 'domain'], function () use ($router) {
        $router->post('save', 'DomainController@save');
        $router->post('list', 'DomainController@list');
        $router->post('info', 'DomainController@info');
        $router->post('del', 'DomainController@del');
        //哒哒注册商户
        $router->post('addDada', 'DomainController@addDada');
        //哒哒余额查询
        $router->post('dadaBalance', 'DomainController@dadaBalance');
        //哒哒生成支付
        $router->post('dadaPay', 'DomainController@dadaPay');
    });
    $router->group(['namespace' => 'Goods', 'prefix' => 'goods'], function () use ($router) {
        $router->post('save', 'GoodsController@save');
        $router->post('list', 'GoodsController@list');
        $router->post('info', 'GoodsController@info');
        $router->post('del', 'GoodsController@del');
    });
    $router->group(['namespace' => 'Article', 'prefix' => 'article'], function () use ($router) {
        $router->post('save', 'ArticleController@save');
        $router->post('list', 'ArticleController@list');
        $router->post('info', 'ArticleController@info');
        $router->post('del', 'ArticleController@del');
    });
    $router->group(['namespace' => 'Open', 'prefix' => 'open'], function () use ($router) {
        $router->post('save', 'OpenController@save');
        $router->post('list', 'OpenController@list');
        $router->post('info', 'OpenController@info');
        $router->post('del', 'OpenController@del');
    });

    $router->group(['namespace' => 'User', 'prefix' => 'user'], function () use ($router) {
        $router->post('save', 'UserController@save');
        $router->post('info', 'UserController@info');
        $router->post('list', 'UserController@list');
        $router->post('del', 'UserController@del');
        $router->post('setpwd', 'UserCOntroller@setpwd');
    });

    $router->group(['namespace' => 'Auth', 'prefix' => 'auth'], function () use ($router) {
        $router->post('login', 'AuthController@login');
    });

    //打印机
    $router->group(['namespace' => 'Printer', 'prefix' => 'printer'], function () use ($router) {
        $router->post('save', 'PrinterController@save');
        $router->get('save', 'PrinterController@save');
        $router->post('info', 'PrinterController@info');
        $router->post('list', 'PrinterController@list');
        $router->post('del', 'PrinterController@del');
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
    $router->group(['namespace' => 'Address', 'prefix' => 'address', "middleware" => 'auth'], function () use ($router) {
        $router->post('save', 'AddressController@save');
        $router->post('list', 'AddressController@list');
        $router->post('info', 'AddressController@info');
        $router->post('del', 'AddressController@del');
    });
    $router->group(['namespace' => 'Store', 'prefix' => 'store'], function () use ($router) {
        $router->post('save', 'StoreController@save');
        $router->post('list', 'StoreController@list');
        $router->post('info', 'StoreController@info');
        $router->post('del', 'StoreController@del');
    });
    $router->group(['namespace' => 'User', 'prefix' => 'user', "middleware" => 'auth'], function () use ($router) {
        $router->post('save', 'UserController@save');
        $router->post('info', 'UserController@info');
        $router->post('verifyVip', 'UserController@verifyVip');
    });

    $router->group(['namespace' => 'Vip', 'prefix' => 'vip', "middleware" => 'auth'], function () use ($router) {
        $router->post('verifyVip', 'VipController@verifyVip');
        $router->post('price/list', 'VipController@priceList');
        $router->post('buy/time', 'VipController@buyTime');
        $router->post('buy/count', 'VipController@buyCount');
    });

    $router->group(['namespace' => 'Order', 'prefix' => 'order', "middleware" => 'auth'], function () use ($router) {
        $router->post('create', 'OrderController@create');
        $router->post('list', 'OrderController@list');
        $router->post('info', 'OrderController@info');
        $router->post('getMini', 'PayController@getMini');
    });
    $router->group(['namespace' => 'Article', 'prefix' => 'article', "middleware" => 'auth'], function () use ($router) {
        $router->post('save', 'ArticleController@save');
        $router->post('del', 'ArticleController@del');
    });
});


$router->get('pay/wx_notify_url/{domain_id}', 'Mini\Order\PayController@notify_url');
$router->post('pay/wx_notify_url/{domain_id}', 'Mini\Order\PayController@notify_url');

$router->post('dada/notify', 'Mini\Order\OrderController@notify');
