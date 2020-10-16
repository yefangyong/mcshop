<?php

use Illuminate\Support\Facades\Route;

Route::post('auth/register',    'AuthController@register');
Route::post('auth/regCaptcha',  'AuthController@regCaptcha');
Route::post('auth/captcha',     'AuthController@regCaptcha');
Route::post('auth/login',       'AuthController@login');
Route::get( 'auth/user',         'AUthController@user');
