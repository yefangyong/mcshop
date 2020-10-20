<?php

use Illuminate\Support\Facades\Route;

//用户相关路由
Route::post('auth/register',    'AuthController@register');
Route::post('auth/regCaptcha',  'AuthController@regCaptcha');
Route::post('auth/captcha',     'AuthController@regCaptcha');
Route::post('auth/login',       'AuthController@login');
Route::get( 'auth/user',         'AUthController@user');

//商品相关路由

Route::get('catalog/index',               'CategoryController@index');
Route::get('catalog/current',               'CategoryController@current');
