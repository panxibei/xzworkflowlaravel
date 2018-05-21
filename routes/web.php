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

// AdminController路由
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
	
	// 显示role页面
	Route::get('roleIndex', 'AdminController@roleIndex')->name('admin.role.index');
	// 显示permission页面
	Route::get('permissionIndex', 'AdminController@permissionIndex')->name('admin.permission.index');

	// 创建role
	// Route::post('roleCreate', 'AdminController@roleCreate')->name('admin.role.create');
	// 创建permission
	Route::post('permissionCreate', 'AdminController@permissionCreate')->name('admin.permission.create');

	// 赋予permission
	Route::post('permissionGive', 'AdminController@permissionGive')->name('admin.permission.give');
	// 移除permission
	Route::post('permissionRevoke', 'AdminController@permissionRevoke')->name('admin.permission.revoke');

	// 赋予role
	// Route::post('roleGive', 'AdminController@roleGive')->name('admin.role.give');
	// 移除role
	// Route::post('roleRemove', 'AdminController@roleRemove')->name('admin.role.remove');

	// 显示role
	Route::get('roleShow', 'AdminController@roleShow')->name('admin.role.show');
	// 显示permission
	Route::get('permissionShow', 'AdminController@permissionShow')->name('admin.permission.show');
	
	// 修改config数据
	Route::post('configChange', 'AdminController@configChange')->name('admin.config.change');

	// logout
	Route::get('logout', 'AdminController@logout')->name('admin.logout');
	
	
});

// RoleController路由
Route::group(['prefix'=>'role', 'namespace'=>'Admin', 'middleware'=>'jwtauth'], function() {

	// 列出所有用户
	Route::get('userList', 'RoleController@userList')->name('admin.role.userlist');

	// 列出所有角色
	Route::get('roleList', 'RoleController@roleList')->name('admin.role.rolelist');

	// 列出所有权限
	Route::get('permissionList', 'RoleController@permissionList')->name('admin.role.permissionlist');

	// 列出所有待删除的角色
	Route::get('roleListDelete', 'RoleController@roleListDelete')->name('admin.role.rolelistdelete');

	// 创建role
	Route::post('roleCreate', 'RoleController@roleCreate')->name('admin.role.create');

	// 删除角色
	Route::post('roleDelete', 'RoleController@roleDelete')->name('admin.role.roledelete');

	// 列出当前用户拥有的角色
	Route::get('userHasRole', 'RoleController@userHasRole')->name('admin.role.userhasrole');

	// 列出当前用户可追加的角色
	Route::get('userGiveRole', 'RoleController@userGiveRole')->name('admin.role.usergiverole');

	// 赋予role
	Route::post('roleGive', 'RoleController@roleGive')->name('admin.role.give');
	// 移除role
	Route::post('roleRemove', 'RoleController@roleRemove')->name('admin.role.remove');

	// 根据角色查看哪些用户
	Route::get('roleToViewUser', 'RoleController@roleToViewUser')->name('admin.role.roletoviewuser');
});


// 测试用
Route::get('test', function(){
	return view('test');
});


