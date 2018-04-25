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

// Route::get('/', function () {
    // return view('welcome');
// });

// home模块
Route::group(['prefix' => 'home', 'namespace' =>'Home'], function() {
	Route::get('/', 'LoginController@index');
});

// admin模块
Route::group(['prefix' => 'admin', 'namespace' =>'Admin'], function() {
	// 显示user页面
	Route::get('userIndex', 'AdminController@userIndex')->name('admin.user.index');
	// 获取user数据信息
	Route::get('userList', 'AdminController@userList')->name('admin.user.list');
});

// 测试用
Route::get('test', function(){
	return view('test');
});