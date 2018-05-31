<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\User;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	
    /**
     * 列出permission页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionIndex()
    {
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());

		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.permission', $config);
		
		$share = compact('config', 'user');
        return view('admin.permission', $share);
    }

    /**
     * 权限列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionGets(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取权限信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$permission = Permission::select('id', 'name', 'guard_name', 'created_at', 'updated_at')
			->paginate($perPage, ['*'], 'page', $page);

		return $permission;
    }
	
    /**
     * 创建permission ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionCreate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
        $permissionname = $request->input('params.permissionname');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		$permission = Permission::create(['name' => $permissionname]);

        return $permission;
    }

    /**
     * 删除permission ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionDelete(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$permissionid = $request->input('params.permissionname');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 判断是否在已被使用之列
		// 1.查出model_has_permissions表中的permission_id
		$model_has_permissions = DB::table('model_has_permissions')
			->select('permission_id as id')->pluck('id')->toArray();
		// $model_has_permissions_tmp = array_column($model_has_roles, 'id');

		// 2.查出role_has_permissions表中的permission_id
		$role_has_permissions = DB::table('role_has_permissions')
			->select('permission_id as id')->pluck('id')->toArray();
		// $role_has_permissions_tmp = array_column($role_has_permissions, 'id');

		// 3.合并前删除重复，model_has_permissions和role_has_permissions两个表的结果
		$permission_used = array_merge($model_has_permissions, $role_has_permissions);
		$permission_used_tmp = array_unique($permission_used);

		// 4.判断是否在列
		$flag = false;
		foreach ($permissionid as $value) {
			if (in_array($value, $permission_used_tmp)) {
				$flag = true;
				break;
			}
		}
		// dd($flag);
		// 如果在使用之列，则不允许删除
		if ($flag) { return false; }
		
        // 如没被使用，则可以删除
		$result = Permission::whereIn('id', $permissionid)->delete();
		// dd($result);
		return $result;
    }

    /**
     * 角色赋予permission
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionGive(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
		
        $roleid = $request->input('params.roleid');
        $permissionid = $request->input('params.permissionid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$role = Role::where('id', $roleid)->first();
		$permission = Permission::whereIn('id', $permissionid)->pluck('name')->toArray();
		
		// $role->givePermissionTo('edit articles');
		foreach ($permission as $permissionname) {
			$result = $role->givePermissionTo($permissionname);
		}
		
        return $result;
    }

    /**
     * 角色移除permission
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionRemove(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
		
        $roleid = $request->input('params.roleid');
        $permissionid = $request->input('params.permissionid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$role = Role::where('id', $roleid)->first();
		$permission = Permission::whereIn('id', $permissionid)->pluck('name')->toArray();

		// 注意：revokePermissionTo似乎不接受数组
		foreach ($permission as $permissionname) {
			// $role->revokePermissionTo('edit articles');
			$result = $role->revokePermissionTo($permissionname);
		}

        return $result;
    }

    /**
     * 列出角色拥有permissions ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleHasPermission(Request $request)
    {
		if (! $request->ajax()) { return null; }

		$roleid = $request->input('roleid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 获取当前角色拥有的权限
		// $rolehaspermission = DB::table('users')
			// ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
			// ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
			// ->where('users.id', $roleid)
			// ->pluck('roles.name', 'roles.id')->toArray();
		$rolehaspermission = Role::join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
			->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
			->where('roles.id', $roleid)
			->pluck('permissions.name', 'permissions.id')->toArray();

		$rolenothaspermission = Permission::select('id', 'name')
			->whereNotIn('id', array_keys($rolehaspermission))
			->pluck('name', 'id')->toArray();

		$result['rolehaspermission'] = $rolehaspermission;
		$result['rolenothaspermission'] = $rolenothaspermission;

		return $result;
    }

    /**
     * 列出所有待删除的权限 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionListDelete(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		// 1.查出全部permission的id
		// $role = Role::select('id')->get()->toArray();
		// $role_tmp = array_column($role, 'id'); //变成一维数组
		$permission_tmp = Permission::select('id')->pluck('id')->toArray();

		// 2.查出model_has_roles表中的role_id
		$model_has_permissions = DB::table('model_has_permissions')
			->select('permission_id as id')->pluck('id')->toArray();
		// $model_has_roles_tmp = array_column($model_has_permissions, 'id');

		// 3.查出role_has_permissions表中的role_id
		$role_has_permissions = DB::table('role_has_permissions')
			->select('permission_id as id')->pluck('id')->toArray();
		// $role_has_permissions_tmp = array_column($role_has_permissions, 'id');

		// 4.合并前删除重复，model_has_roles和role_has_permissions两个表的结果
		$permission_used = array_merge($model_has_permissions, $role_has_permissions);
		$permission_used_tmp = array_unique($permission_used);

		// 5.排除已被使用的role，剩余的既是没被使用的role的id
		$unused_permission_id = array_diff($permission_tmp, $permission_used_tmp);
		
		// 6.查询没被使用的role
		$result = Permission::whereIn('id', $unused_permission_id)
			->pluck('name', 'id')->toArray();

		return $result;
    }

    /**
     * 列出所有权限 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionList(Request $request)
    {
		if (! $request->ajax()) { return null; }
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		$permission = Permission::pluck('name', 'id')->toArray();
		return $permission;
    }

    /**
     * 根据权限查看哪些角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionToViewRole(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$permissionid = $request->input('permissionid');

		//
		$role = Role::join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
			->where('role_has_permissions.permission_id', $permissionid)
			->pluck('roles.name', 'roles.id')->toArray();

		return $role;
    }

    /**
     * 角色同步到指定权限 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncRoleToPermission(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
		
		$permissionid = $request->input('params.permissionid');
		$roleid = $request->input('params.roleid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		// 1.查询permission
		$permission = Permission::where('id', $permissionid)->first();

		// 2.查询role
		$roles = Role::whereIn('id', $roleid)
			->pluck('name')->toArray();

		$result = $permission->syncRoles($roles);

		return $result;
    }

}
