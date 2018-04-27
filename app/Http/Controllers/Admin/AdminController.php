<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\User;

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
     * 列出用户页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userList(Request $request)
    {
        // 获取用户信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		$user = User::select('id', 'name', 'email', 'login_time', 'login_ip', 'login_counts')
			// ->get()
			->paginate($perPage, ['*'], 'page', $page);
			// ->toArray();

			// return $user->links();
			
			return $user;
		
		
    }
}
