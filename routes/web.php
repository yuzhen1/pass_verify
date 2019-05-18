<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//注册
$router->post('/login/register', 'LoginController@register');
//登录
$router->post('/login/login', 'LoginController@login');
$router->post('/login/myself', 'LoginController@myself');//个人中心