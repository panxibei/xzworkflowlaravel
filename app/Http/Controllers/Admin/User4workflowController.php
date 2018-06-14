<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\User4workflow;
use App\Models\User;


class User4workflowController extends Controller
{

    /**
     * 列出user4workflowIndex页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user4workflowIndex()
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

        return view('admin.user4workflow', $share);
    }

    /**
     * slot2field列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user4workflowGets(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$limit = $request->only('limit');
		$limit = empty($limit) ? 10 : $limit;

		// 所有的slot
		// $slot = array_reverse(Slot::limit($limit)->pluck('name', 'id')->toArray());
		$user = User::limit($limit)->pluck('name', 'id');//->toArray();
		return $user;
    }

    /**
     * changeUser ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeUser(Request $request)
    {
		if (! $request->ajax()) { return null; }

		$userid = $request->only('userid');
		dd($userid);
		// 1.所有user
		$all_user = User::pluck('name', 'id')->toArray();

		// 2.根据userid查询相应的substitute_user
		$substitute_user_id = User4workflow::select('user_id')
			->where('user_id', $userid['userid'])
			->first();

		// 如果没有被选择的用户，则返回所有用户
		if (trim($user_id['user_id'])=='') {
			$user['user_unselected'] = $all_user;
			return $user;
		}
		
		// 3.查找已分配userid
		$arr_userid = explode(',', $user_id['user_id']);
		foreach ($arr_userid as $value) {
			$user_selected_tmp1[] = User::select('id', 'name')
				->where('id', $value)
				->first();
		}
		
		// 4.根据userid查找已选择用户
		foreach ($user_selected_tmp1 as $key => $value) {
			$user_selected_tmp2[$value['id']] = $value['name'];
		}

		// json化，防止返回后乱序
		$user_selected = [];
		foreach ($user_selected_tmp2 as $k => $v) {
			array_push($user_selected, array("id" => $k, "name" => $v));
		}
		$user_selected_json = json_encode($user_selected);	

		// 5.未被选择的用户（步3和步4差集）
		$user_unselected = array_diff($all_user, $user_selected_tmp2);

		// 6.整理到$user
		$user['user_selected'] = $user_selected_json;
		$user['user_unselected'] = $user_unselected;

		return $user;
    }

}
