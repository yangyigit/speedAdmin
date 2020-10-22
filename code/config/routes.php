<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;

//Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::addGroup('/auth',function (){
    Router::get('/User/showList','App\Controller\Admin\auth\UserController@showList');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/add','App\Controller\Admin\auth\UserController@add');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/look','App\Controller\Admin\auth\UserController@look');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/edit','App\Controller\Admin\auth\UserController@edit');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/del','App\Controller\Admin\auth\UserController@del');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/allot','App\Controller\Admin\auth\UserController@allot');

    Router::get('/Rule/showList','App\Controller\Admin\auth\RuleController@showList');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/Rule/ruleRefresh','App\Controller\Admin\auth\RuleController@ruleRefresh');
    Router::addRoute(['GET', 'POST', 'HEAD'], '/Rule/edit', 'App\Controller\Admin\auth\RuleController@edit');
});

Router::get('/favicon.ico', function () {
    return '';
});
