<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
		$permission = Role::create(['name' => $permissionname]);
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
		// dd($permissionid);
		
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
	
}
