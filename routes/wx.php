<?php

use Illuminate\Support\Facades\Route;

Route::post('auth/register', 'AuthController@register');
Route::post('auth/register/code', 'AuthController@regCaptcha');
