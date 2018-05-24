<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\User;
use App\Models\Group;
use Cookie;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
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

	// logout
	public function logout()
	{
		// 删除cookie
		Cookie::queue(Cookie::forget('token'));

		// Pass true to force the token to be blacklisted "forever"
		// auth()->logout(true);
		auth()->logout();

		// 返回登录页面
		return redirect()->route('login');
	}
	
	
    /**
     * 列出配置页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configIndex()
    {
        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        return view('admin.config', $config);
    }

    /**
     * 列出配置页面 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configList(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
        // 获取用户信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		$config = Config::select('cfg_id', 'cfg_name', 'cfg_value', 'cfg_description')
			->get();
			
		return $config;
    }

    /**
     * 修改配置 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configChange(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		$up2data = $request->all();
		$res = Config::where('cfg_name', $up2data['cfg_name'])->update(['cfg_value'=>$up2data['cfg_value']]);
		return $res;
    }

    /**
     * 列出用户页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userIndex()
    {
        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        return view('admin.user', $config);
    }

    /**
     * 列出用户页面 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userList(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取用户信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		$user = User::select('id', 'name', 'email', 'login_time', 'login_ip', 'login_counts')
			// ->get()
			->paginate($perPage, ['*'], 'page', $page);
			
		return $user;
    }
	
    /**
     * 列出用户组页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function groupIndex()
    {
        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        return view('admin.group', $config);
    }

    /**
     * 列出用户组页面 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function groupList(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取用户信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		$group = Group::select('id', 'title', 'rules', 'created_at')
			->paginate($perPage, ['*'], 'page', $page);
			
		return $group;
    }

    /**
     * 列出权限页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ruleIndex()
    {
        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        return view('admin.rule', $config);
    }


    /**
     * 显示role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function roleShow(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		// 请求的用户名
        $username = $request->input('user');
		$user = User::where('name', $username)->first();
		// dd($user);
		// $result = $user->permissions;
		// $result = $user->getPermissionsViaRoles();
		// $result = $user->getAllPermissions();
		$roles = $user->getRoleNames();
        return $roles;
    }

    /**
     * 显示permission
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permissionShow(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		// 请求的role名称
        $role = $request->input('role');
		// dd($roleid);
		// $permission = Permission::where('name', $role)->get();
		// $permission = DB::table('role_has_permissions')
			// ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
			// ->where('role_id', $roleid)
			// ->get();
		$permission = DB::table('roles')
			->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.permission_id')
			->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
			->where('roles.name', $role)
			->get();
		// dd($permission);
        return $permission;
    }

}
