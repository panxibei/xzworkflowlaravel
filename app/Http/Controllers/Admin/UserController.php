<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use DB;

class UserController extends Controller
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
     * 创建用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userCreate(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		$newuser = $request->only('name', 'email');
		$nowtime = date("Y-m-d H:i:s",time());
		
		$result = User::create([
			'name'     => $newuser['name'],
			'email'    => $newuser['email'],
			'password' => bcrypt('12345678'),
			'login_time' => time(),
			'login_ip' => '127.0.0.1',
			'login_counts' => 0,
			'remember_token' => '',
			'created_at' => $nowtime,
			'updated_at' => $nowtime,
			'deleted_at' => NULL
		]);

		return $result;
    }

    /**
     * 禁用用户（软删除） ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userTrash(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		$userid = $request->only('userid');
		
		$usertrashed = User::select('deleted_at')
			->where('id', $userid)
			->first();

		// 如果在回收站里，则恢复它
		if ($usertrashed == null) {
			$result = User::where('id', $userid)->restore();
		} else {
			$result = User::where('id', $userid)->delete();
		}

		return $result;
    }

    /**
     * 删除用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userDelete(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		$userid = $request->only('userid');
		// dd($userid);


		// 判断两个表（model_has_permissions和model_has_roles）中，
		// 是否已有用户被分配了角色或权限
		// 如果已经分配了，则不允许删除
		$model_has_permissions = DB::table('model_has_permissions')
			->where('model_id', $userid)
			->first();
		// dd($model_has_permissions);

		$model_has_roles = DB::table('model_has_roles')
			->where('model_id', $userid)
			->first();
		// dd($model_has_roles);
		
		if ($model_has_permissions != null || $model_has_roles != null) {
			return 0;
		}
		
		$result = User::where('id', $userid)->forceDelete();

		return $result;
    }

    /**
     * 编辑用户 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userEdit(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		$tmp = $request->only('user.id', 'user.name', 'user.email', 'user.password');
		$updateuser = $tmp['user'];
// dump(isset($updateuser['password']));
// dd($updateuser);

		try	{
			// 如果password为空，则不更新密码
			if (isset($updateuser['password'])) {
				$result = User::where('id', $updateuser['id'])
					->update([
						'name'=>$updateuser['name'],
						'email'=>$updateuser['email'],
						'password'=>bcrypt($updateuser['password'])
					]);
			} else {
				$result = User::where('id', $updateuser['id'])
					->update([
						'name'=>$updateuser['name'],
						'email'=>$updateuser['email']
					]);
			}
		}
		catch (Exception $e) {//捕获异常
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
		
// dd($result);
		return $result;
    }
	
	
}
