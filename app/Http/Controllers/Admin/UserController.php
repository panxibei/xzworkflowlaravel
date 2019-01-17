<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\User;
use DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\userExport;

// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Support\Collection;

class UserController extends Controller
{

	// public function __construct(\Maatwebsite\Excel\Exporter $excel)
	// {
		// $this->excel = $excel;
	// }

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
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.user', $config);
		
		$share = compact('config', 'user');
        return view('admin.user', $share);
    }
	

    /**
     * 列出用户页面 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userList(Request $request)
    {
		if (! $request->ajax()) return null;
		
		$url = request()->url();
		$queryParams = request()->query();
		
		if (isset($queryParams['perPage'])) {
			$perPage = $queryParams['perPage'] ?: 10000;
		} else {
			$perPage = 10000;
		}
		
		if (isset($queryParams['page'])) {
			$page = $queryParams['page'] ?: 1;
		} else {
			$page = 1;
		}
		
		

        // 获取用户信息
		// $perPage = $request->input('perPage');
		// $page = $request->input('page');
		
		$queryfilter_name = $request->input('queryfilter_name');
		$queryfilter_logintime = $request->input('queryfilter_logintime');
		$queryfilter_email = $request->input('queryfilter_email');
		$queryfilter_loginip = $request->input('queryfilter_loginip');

		$user = User::select('id', 'name', 'email', 'login_time', 'login_ip', 'login_counts', 'created_at', 'updated_at', 'deleted_at')
			->when($queryfilter_logintime, function ($query) use ($queryfilter_logintime) {
				return $query->whereBetween('login_time', $queryfilter_logintime);
			})
			->when($queryfilter_name, function ($query) use ($queryfilter_name) {
				return $query->where('name', 'like', '%'.$queryfilter_name.'%');
			})
			->when($queryfilter_email, function ($query) use ($queryfilter_email) {
				return $query->where('name', 'like', '%'.$queryfilter_email.'%');
			})
			->when($queryfilter_loginip, function ($query) use ($queryfilter_loginip) {
				return $query->where('name', 'like', '%'.$queryfilter_loginip.'%');
			})
			->limit(5000)
			->orderBy('created_at', 'desc')
			->withTrashed()
			->paginate($perPage, ['*'], 'page', $page);

		return $user;
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
			'login_time' => $nowtime,
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
		if (! $request->isMethod('post') || ! $request->ajax()) { return 0; }

		$userid = $request->only('userid');

		// 如果是管理员id为1，则不能删除
		if ($userid['userid'] == 1) {return 0;}
		
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




	// 用户列表Excel文件导出
    public function excelExport()
    {
		
		// if (! $request->ajax()) { return null; }
		
		// 获取扩展名配置值
		$config = Config::select('cfg_name', 'cfg_value')
			->pluck('cfg_value', 'cfg_name')->toArray();

		$EXPORTS_EXTENSION_TYPE = $config['EXPORTS_EXTENSION_TYPE'];
		$FILTERS_USER_NAME = $config['FILTERS_USER_NAME'];
		$FILTERS_USER_EMAIL = $config['FILTERS_USER_EMAIL'];
		$FILTERS_USER_LOGINTIME_DATEFROM = $config['FILTERS_USER_LOGINTIME_DATEFROM'];
		$FILTERS_USER_LOGINTIME_DATETO = $config['FILTERS_USER_LOGINTIME_DATETO'];

        // 获取用户信息
		// Excel数据，最好转换成数组，以便传递过去
		$queryfilter_name = $FILTERS_USER_NAME || '';
		$queryfilter_email = $FILTERS_USER_EMAIL || '';

		$queryfilter_datefrom = strtotime($FILTERS_USER_LOGINTIME_DATEFROM) ? $FILTERS_USER_LOGINTIME_DATEFROM : '1970-01-01';
		$queryfilter_dateto = strtotime($FILTERS_USER_LOGINTIME_DATETO) ? $FILTERS_USER_LOGINTIME_DATETO : '9999-12-31';


		$user = User::select('id', 'name', 'email', 'login_time', 'login_ip', 'login_counts', 'created_at', 'updated_at', 'deleted_at')
			->where('name', 'like', '%'.$queryfilter_name.'%')
			->where('email', 'like', '%'.$queryfilter_email.'%')
			->whereBetween('login_time', [$queryfilter_datefrom, $queryfilter_dateto])
			->withTrashed()
			->get()->toArray();		
		


        // 示例数据，不能直接使用，只能把数组变成Exports类导出后才有数据
		// $cellData = [
            // ['学号','姓名','成绩'],
            // ['10001','AAAAA','199'],
            // ['10002','BBBBB','192'],
            // ['10003','CCCCC','195'],
            // ['10004','DDDDD','189'],
            // ['10005','EEEEE','196'],
        // ];

		// Excel标题第一行，可修改为任意名字，包括中文
		$title[] = ['id', 'name', 'email', 'login_time', 'login_ip', 'login_counts', 'created_at', 'updated_at', 'deleted_at'];

		// 合并Excel的标题和数据为一个整体
		$data = array_merge($title, $user);

		// dd(Excel::download($user, '学生成绩', 'Xlsx'));
		// dd(Excel::download($user, '学生成绩.xlsx'));
		return Excel::download(new userExport($data), 'users'.date('YmdHis',time()).'.'.$EXPORTS_EXTENSION_TYPE);
		
    }


}
