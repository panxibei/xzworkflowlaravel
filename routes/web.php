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

// CirculationController路由
Route::group(['prefix' => 'circulation', 'namespace' =>'Main'], function() {
	// Route::get('/', 'MainController@index')->name('main');
	
	// 显示circulation页面
	Route::post('circulationIndex', 'CirculationController@circulationIndex')->name('main.circulation.index');
});

// AdminController路由
Route::group(['prefix'=>'admin', 'namespace'=>'Admin', 'middleware'=>'jwtauth'], function() {
	// 显示config页面
	Route::get('configIndex', 'AdminController@configIndex')->name('admin.config.index');

	// 获取config数据信息
	Route::get('configList', 'AdminController@configList')->name('admin.config.list');

	// 获取group数据信息
	Route::get('groupList', 'AdminController@groupList')->name('admin.group.list');
	

	// 修改config数据
	Route::post('configChange', 'AdminController@configChange')->name('admin.config.change');

	// logout
	Route::get('logout', 'AdminController@logout')->name('admin.logout');

});

// FieldController路由
Route::group(['prefix'=>'field', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_page_user']], function() {

	// 显示field页面
	Route::get('fieldIndex', 'FieldController@fieldIndex')->name('admin.field.index');

	// field列表
	Route::get('fieldGets', 'FieldController@fieldGets')->name('admin.field.fieldgets');

	// 创建field
	Route::post('fieldCreateOrUpdate', 'FieldController@fieldCreateOrUpdate')->name('admin.field.createorupdate');

	// 删除field
	Route::post('fieldDelete', 'FieldController@fieldDelete')->name('admin.field.fielddelete');

});

// SlotController路由
Route::group(['prefix'=>'slot', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_page_user']], function() {

	// 显示slot页面
	Route::get('slotIndex', 'SlotController@slotIndex')->name('admin.slot.index');

	// slot列表
	Route::get('slotGets', 'SlotController@slotGets')->name('admin.slot.slotgets');

	// 创建slot
	Route::post('slotCreateOrUpdate', 'SlotController@slotCreateOrUpdate')->name('admin.slot.createorupdate');

	// 删除slot
	Route::post('slotDelete', 'SlotController@slotDelete')->name('admin.slot.slotdelete');

});


// TemplateController路由
Route::group(['prefix'=>'template', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_page_user']], function() {

	// 显示field页面
	Route::get('templateIndex', 'TemplateController@templateIndex')->name('admin.template.index');

	// slot列表
	Route::get('templateGets', 'TemplateController@templateGets')->name('admin.template.templategets');

	// 创建slot
	Route::post('templateCreateOrUpdate', 'TemplateController@templateCreateOrUpdate')->name('admin.template.createorupdate');

	// 删除slot
	Route::post('templateDelete', 'TemplateController@templateDelete')->name('admin.template.templatedelete');

});

// Slot2fieldController路由
Route::group(['prefix'=>'slot2field', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_page_user']], function() {

	// 显示slot2field页面
	Route::get('slot2fieldIndex', 'Slot2fieldController@slot2fieldIndex')->name('admin.slot2field.index');

	// slot2field列表
	Route::get('slot2fieldGets', 'Slot2fieldController@slot2fieldGets')->name('admin.slot2field.slot2fieldgets');

	// changeslot
	Route::get('changeSlot', 'Slot2fieldController@changeSlot')->name('admin.slot2field.changeslot');

	// fieldsort
	Route::post('fieldSort', 'Slot2fieldController@fieldSort')->name('admin.slot2field.fieldsort');

	// 创建slot
	// Route::post('templateCreateOrUpdate', 'TemplateController@templateCreateOrUpdate')->name('admin.template.createorupdate');

	// 删除slot
	// Route::post('templateDelete', 'TemplateController@templateDelete')->name('admin.template.templatedelete');

});

// Template2slotController路由
Route::group(['prefix'=>'slot2field', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_page_user']], function() {

	// 显示template2slot页面
	Route::get('template2slotIndex', 'Template2slotController@template2slotIndex')->name('admin.template2slot.index');


});

// UserController路由
Route::group(['prefix'=>'user', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_page_user']], function() {

	// 显示user页面
	Route::get('userIndex', 'UserController@userIndex')->name('admin.user.index');

	// 获取user数据信息
	Route::get('userList', 'UserController@userList')->name('admin.user.list');

	// 创建user
	Route::post('userCreate', 'UserController@userCreate')->name('admin.user.create');

	// 禁用user（软删除）
	Route::post('userTrash', 'UserController@userTrash')->name('admin.user.trash');

	// 删除user
	Route::post('userDelete', 'UserController@userDelete')->name('admin.user.delete');

	// 编辑user
	Route::post('userEdit', 'UserController@userEdit')->name('admin.user.edit');

	// 测试excelExport
	Route::get('excelExport', 'UserController@excelExport')->name('admin.user.excelexport');

});

// RoleController路由
Route::group(['prefix'=>'role', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_page_role']], function() {

	// 显示role页面
	Route::get('roleIndex', 'RoleController@roleIndex')->name('admin.role.index');

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

	// 权限同步到指定角色
	Route::post('syncPermissionToRole', 'RoleController@syncPermissionToRole')->name('admin.role.syncpermissiontorole');

	// 角色列表
	Route::get('roleGets', 'RoleController@roleGets')->name('admin.role.rolegets');
});

// PermissionController路由
Route::group(['prefix'=>'permission', 'namespace'=>'Admin', 'middleware'=>['jwtauth','permission:permission_page_permission']], function() {

	// 显示permission页面
	Route::get('permissionIndex', 'PermissionController@permissionIndex')->name('admin.permission.index');

	// 角色列表
	Route::get('permissionGets', 'PermissionController@permissionGets')->name('admin.permission.permissiongets');

	// 创建permission
	Route::post('permissionCreate', 'PermissionController@permissionCreate')->name('admin.permission.create');

	// 删除permission
	Route::post('permissionDelete', 'PermissionController@permissionDelete')->name('admin.permission.permissiondelete');

	// 赋予permission
	Route::post('permissionGive', 'PermissionController@permissionGive')->name('admin.permission.give');
	// 移除permission
	Route::post('permissionRemove', 'PermissionController@permissionRemove')->name('admin.permission.remove');

	// 列出当前角色拥有的权限
	Route::get('roleHasPermission', 'PermissionController@roleHasPermission')->name('admin.permission.rolehaspermission');

	// 列出所有待删除的权限
	Route::get('permissionListDelete', 'PermissionController@permissionListDelete')->name('admin.permission.permissionlistdelete');

	// 列出所有权限
	Route::get('permissionList', 'PermissionController@permissionList')->name('admin.permission.permissionlist');

	// 根据权限查看哪些角色
	Route::get('permissionToViewRole', 'PermissionController@permissionToViewRole')->name('admin.permission.permissiontoviewrole');

	// 角色同步到指定权限
	Route::post('syncRoleToPermission', 'PermissionController@syncRoleToPermission')->name('admin.permission.syncroletopermission');

});

// 测试用
Route::get('test', function(){
	return view('test');
});

// 测试adldap用
Route::get('adldap', 'adldapController@adLdap');


