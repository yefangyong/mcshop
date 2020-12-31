<?php

use Illuminate\Support\Facades\Route;

//用户模块
Route::post('auth/register', 'AuthController@register');
Route::post('auth/regCaptcha', 'AuthController@regCaptcha');
Route::post('auth/captcha', 'AuthController@regCaptcha');
Route::post('auth/login', 'AuthController@login');
Route::get('auth/user', 'AuthController@user');
Route::get('auth/info', 'AuthController@info');
Route::post('auth/logout', 'AuthController@logout');
Route::post('auth/profile', 'AuthController@profile');

//用户模块--地址
Route::get('address/list', 'AddressController@list');
Route::post('address/save', 'AddressController@save');
Route::post('address/delete', 'AddressController@delete');

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
Route::any('coupon/receive', 'CouponController@receive');

//商品模块--团购
Route::get('groupon/list', 'GrouponController@list');
Route::get('groupon/test', 'GrouponController@test');

//订单模块--购物车
Route::post('cart/add', 'CartController@add');
Route::post('cart/fastadd', 'CartController@fastAdd');
Route::any('cart/countProduct', 'CartController@countProduct');
Route::post('cart/update', 'CartController@update');
Route::post('cart/delete', 'CartController@delete');
Route::post('cart/checked', 'CartController@checked');
Route::any('cart/index', 'CartController@index');
Route::any('cart/goodscount', 'CartController@goodsCount');
Route::get('cart/checkout', 'CartController@checkout');


//订单模块--订单
Route::any('order/submit', 'OrderController@submit');
Route::any('order/cancel', 'OrderController@cancel');
Route::any('order/refund', 'OrderController@refund');
Route::any('order/delete', 'OrderController@delete');
Route::any('order/confirm', 'OrderController@confirm');
Route::any('order/detail', 'OrderController@detail');
Route::any('order/list', 'OrderController@list');

Route::get('home/redirectShareUrl', 'HomeController@redirectShareUrl')->name('home.redirectShareUrl');
Route::get('home/index', 'HomeController@index');

Route::get('topic/list', 'TopicController@getList');
Route::get('topic/detail', 'TopicController@getDetail');
Route::get('topic/related', 'TopicController@getRelated');
Route::get('user/index', 'UserController@index');
Route::get('user/index', 'UserController@index');
Route::post('feedback/submit', 'FeedbackController@submit');
Route::get('issue/list', 'IssueController@getList');
Route::any('collect/list', 'CollectController@getList'); //收藏列表
Route::any('collect/addordelete', 'CollectController@addOrDelete'); //添加或取消收藏
