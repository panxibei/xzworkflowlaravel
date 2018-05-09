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
Route::group(['prefix' => 'login', 'namespace' =>'Home'], function() {
	Route::get('/', 'LoginController@index')->name('login');
	Route::post('checklogin', 'LoginController@checklogin')->name('login.checklogin');
});

// admin模块
Route::group(['prefix'=>'admin', 'namespace'=>'Admin', 'middleware'=>'jwtauth'], function() {
	// 显示config页面
	Route::get('configIndex', 'AdminController@configIndex')->name('admin.config.index');
	// 显示user页面
	Route::get('userIndex', 'AdminController@userIndex')->name('admin.user.index');
	// 显示group页面
	Route::get('groupIndex', 'AdminController@groupIndex')->name('admin.group.index');
	// 显示rule页面
	Route::get('ruleIndex', 'AdminController@ruleIndex')->name('admin.rule.index');

	// 获取config数据信息
	Route::get('configList', 'AdminController@configList')->name('admin.config.list');
	// 获取user数据信息
	Route::get('userList', 'AdminController@userList')->name('admin.user.list');
	// 获取group数据信息
	Route::get('groupList', 'AdminController@groupList')->name('admin.group.list');
	// 获取rule数据信息
	Route::get('ruleList', 'AdminController@ruleList')->name('admin.rule.list');
	
	// 修改config数据
	Route::post('configChange', 'AdminController@configChange')->name('admin.config.change');

});

// 测试用
Route::get('test', function(){
	return view('test');
});