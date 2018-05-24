<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\User;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
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
     * 列出role页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleIndex()
    {
        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        return view('admin.role', $config);
    }

    /**
     * 列出用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userList(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取用户信息
		$user = User::pluck('name', 'id')->toArray();
		// dd($user);
		return $user;
    }

    /**
     * 列出所有待删除的角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleListDelete(Request $request)
    {
		if (! $request->ajax()) { return null; }

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		// 1.查出全部role的id
		// $role = Role::select('id')->get()->toArray();
		// $role_tmp = array_column($role, 'id'); //变成一维数组
		$role_tmp = Role::select('id')->pluck('id')->toArray();

		// 2.查出model_has_roles表中的role_id
		$model_has_roles = DB::table('model_has_roles')
			->select('role_id as id')->get()->toArray();
		$model_has_roles_tmp = array_column($model_has_roles, 'id');

		// 3.查出role_has_permissions表中的role_id
		$role_has_permissions = DB::table('role_has_permissions')
			->select('role_id as id')->get()->toArray();
		$role_has_permissions_tmp = array_column($role_has_permissions, 'id');

		// 4.合并前删除重复，model_has_roles和role_has_permissions两个表的结果
		$role_used = array_merge($model_has_roles_tmp, $role_has_permissions_tmp);
		$role_used_tmp = array_unique($role_used);

		// 5.排除已被使用的role，剩余的既是没被使用的role的id
		$unused_role_id = array_diff($role_tmp, $role_used_tmp);
		
		// 6.查询没被使用的role
		$result = Role::whereIn('id', $unused_role_id)
			->pluck('name', 'id')->toArray();

		return $result;
    }

    /**
     * 列出用户拥有roles ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userHasRole(Request $request)
    {
		if (! $request->ajax()) { return null; }

		$userid = $request->input('userid');
		
		// 获取当前用户拥有的角色
		$userhasrole = DB::table('users')
			->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
			->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
			->where('users.id', $userid)
			->pluck('roles.name', 'roles.id')->toArray();

		// $tmp_array = DB::table('roles')
			// ->select('id', 'name')
			// ->whereNotIn('id', array_keys($userhasrole))
			// ->get()->toArray();
			// $usernothasrole = array_column($tmp_array, 'name', 'id'); //变成一维数组
		$usernothasrole = DB::table('roles')
			->select('id', 'name')
			->whereNotIn('id', array_keys($userhasrole))
			->pluck('name', 'id')->toArray();

		$result['userhasrole'] = $userhasrole;
		$result['usernothasrole'] = $usernothasrole;

		return $result;
    }

    /**
     * 创建role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleCreate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
        $rolename = $request->input('params.rolename');
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		$role = Role::create(['name' => $rolename]);
        return $role;
    }

    /**
     * 删除角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleDelete(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$roleid = $request->input('params.rolename');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		
		// 判断是否在已被使用之列
		// 1.查出model_has_roles表中的role_id
		$model_has_roles = DB::table('model_has_roles')
			->select('role_id as id')->get()->toArray();
		$model_has_roles_tmp = array_column($model_has_roles, 'id');

		// 2.查出role_has_permissions表中的role_id
		$role_has_permissions = DB::table('role_has_permissions')
			->select('role_id as id')->get()->toArray();
		$role_has_permissions_tmp = array_column($role_has_permissions, 'id');

		// 3.合并前删除重复，model_has_roles和role_has_permissions两个表的结果
		$role_used = array_merge($model_has_roles_tmp, $role_has_permissions_tmp);
		$role_used_tmp = array_unique($role_used);

		// 4.判断是否在列
		$flag = false;
		foreach ($roleid as $value) {
			if (in_array($value, $role_used_tmp)) {
				$flag = true;
				break;
			}
		}
		// dd($flag);
		// 如果在使用之列，则不允许删除
		if ($flag) { return false; }
		
        // 如没被使用，则可以删除
		$result = Role::whereIn('id', $roleid)->delete();
		// dd($result);
		return $result;
    }

    /**
     * 用户赋予role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleGive(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
		
        $userid = $request->input('params.userid');
        $roleid = $request->input('params.roleid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$user = User::where('id', $userid)->first();
		$role = Role::whereIn('id', $roleid)->pluck('name')->toArray();
		
		$result = $user->assignRole($role);
        return $result;
    }

    /**
     * 用户移除role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleRemove(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
		
        $userid = $request->input('params.userid');
        $roleid = $request->input('params.roleid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$user = User::where('id', $userid)->first();
		$role = Role::whereIn('id', $roleid)->pluck('name')->toArray();

		// 注意：removeRole似乎不接受数组
		foreach ($role as $rolename) {
			$result = $user->removeRole($rolename);
		}

        return $result;
    }

    /**
     * 列出所有角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleList(Request $request)
    {
		if (! $request->ajax()) { return null; }
		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');
		$role = Role::pluck('name', 'id')->toArray();
		return $role;
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
		$permission = Permission::pluck('name', 'id')->toArray();
		return $permission;
    }

    /**
     * 根据角色查看哪些用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleToViewUser(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$roleid = $request->input('roleid');

		//
		$user = DB::table('model_has_roles')
			->join('users', 'model_has_roles.model_id', '=', 'users.id')
			->where('role_id', $roleid)
			->pluck('users.name', 'users.id')->toArray();

		return $user;
    }

    /**
     * 权限同步到指定角色 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncPermissionToRole(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }
		
		$roleid = $request->input('params.roleid');
		$permissionid = $request->input('params.permissionid');

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		// 1.查询role
		$role = Role::where('id', $roleid)->first();

		// 2.查询permission
		$permissions = Permission::whereIn('id', $permissionid)
			->pluck('name')->toArray();

		$result = $role->syncPermissions($permissions);

		return $result;
    }

    /**
     * 角色列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleGets(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取角色信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$role = Role::select('id', 'name', 'guard_name', 'created_at', 'updated_at')
			->paginate($perPage, ['*'], 'page', $page);

		return $role;
    }

}
