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
    //用户管理相关路由
    Router::get('/User/showList','App\Controller\Admin\auth\UserController@showList');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/add','App\Controller\Admin\auth\UserController@add');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/look','App\Controller\Admin\auth\UserController@look');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/edit','App\Controller\Admin\auth\UserController@edit');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/del','App\Controller\Admin\auth\UserController@del');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/User/allot','App\Controller\Admin\auth\UserController@allot');

    //规则管理相关路由
    Router::get('/Rule/showList','App\Controller\Admin\auth\RuleController@showList');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/Rule/ruleRefresh','App\Controller\Admin\auth\RuleController@ruleRefresh');
    Router::addRoute(['GET', 'POST', 'HEAD'], '/Rule/edit', 'App\Controller\Admin\auth\RuleController@edit');

    //用户组管理相关路由
    Router::get('/Group/showList','App\Controller\Admin\auth\GroupController@showList');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/Group/add','App\Controller\Admin\auth\GroupController@add');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/Group/edit','App\Controller\Admin\auth\GroupController@edit');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/Group/del','App\Controller\Admin\auth\GroupController@del');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/Group/look','App\Controller\Admin\auth\GroupController@look');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/Group/removeUser','App\Controller\Admin\auth\GroupController@removeUser');
    Router::addRoute(['GET', 'POST', 'HEAD'],'/Group/allot','App\Controller\Admin\auth\GroupController@allot');
});

//请求日志
Router::get('/RequestLog/showList','App\Controller\Admin\RequestLogController@showList');

Router::get('/favicon.ico', function () {
    return '';
});
