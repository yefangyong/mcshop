<?php

use Illuminate\Support\Facades\Route;

//用户模块
Route::post('auth/register', 'AuthController@register');
Route::post('auth/regCaptcha', 'AuthController@regCaptcha');
Route::post('auth/captcha', 'AuthController@regCaptcha');
Route::post('auth/login', 'AuthController@login');
Route::get('auth/user', 'AUthController@user');

//商品模块--分类
Route::get('catalog/index', 'CategoryController@index');
Route::get('catalog/current', 'CategoryController@current');

//商品模块--品牌
Route::get('brand/detail', 'BrandController@detail');
Route::get('brand/list', 'BrandController@list');

//商品模块--商品
Route::get('goods/category', 'GoodsController@category');
Route::get('goods/count', 'GoodsController@count');
Route::get('goods/list', 'GoodsController@list');
Route::get('goods/detail', 'GoodsController@detail');

//商品模块--优惠券
Route::get('coupon/list', 'CouponController@list');
Route::get('coupon/mylist', 'CouponController@mylist');
Route::get('coupon/receive', 'CouponController@receive');

//商品模块--团购
Route::get('groupon/list', 'GrouponController@list');
Route::get('groupon/test', 'GrouponController@test');

//订单模块--购物车
Route::post('cart/add', 'CartController@add');
Route::post('cart/fastadd', 'CartController@fastAdd');
Route::get('cart/countProduct', 'CartController@countProduct');
Route::post('cart/update', 'CartController@update');
Route::post('cart/delete', 'CartController@delete');
Route::post('cart/checked', 'CartController@checked');
Route::post('cart/index', 'CartController@index');
Route::get('cart/checkout', 'CartController@checkout');
Route::get('cart/checkout', 'CartController@checkout');

//订单模块--订单
Route::any('order/submit', 'OrderController@submit');
Route::any('order/cancel', 'OrderController@cancel');


Route::get('home/redirectShareUrl', 'HomeController@redirectShareUrl')->name('home.redirectShareUrl');
