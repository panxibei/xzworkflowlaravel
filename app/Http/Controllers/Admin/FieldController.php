<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Field;


class FieldController extends Controller
{
   
    /**
     * 列出field页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fieldIndex()
    {
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等
		
        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.role', $config);
		
		$share = compact('config', 'user');
// dd($user);
// dd($config['SITE_TITLE']);
        return view('admin.field', $share);
			// ->with('user', $user);
			// ->with('user',$user);
    }	
	
    /**
     * 列出field ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fieldList(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取field信息
		$field = Field::pluck('name', 'id')->toArray();
		return $field;
    }
	
    /**
     * field列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fieldGets(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取角色信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		$field = Field::select('id', 'name', 'guard_name', 'created_at', 'updated_at')
			->paginate($perPage, ['*'], 'page', $page);

		return $field;
    }

    /**
     * field create ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fieldCreate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$postdata = $request->input('params.postdata');
		// dd($postdata);
		
        // 新增field
		try	{
			$result = Field::create([
				'name' => $postdata['name'],
				'type' => $postdata['type'],
				'bgcolor' => $postdata['bgcolor'],
				'readonly' => $postdata['readonly'],
				'value' => $postdata['value'],
				'placeholder' => $postdata['placeholder'],
				'regexp' => $postdata['regexp'],
				'helpblock' => $postdata['helpblock']
			]);

			$result = 1;
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}

		return $result;
    }
	
	
}
