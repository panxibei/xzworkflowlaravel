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
// dd($userid);
		// 1.所有user
		$all_user = User::where('id', '<>', $userid['userid'])
			->pluck('name', 'id')->toArray();
// dd($all_user);
		// 2.根据userid查询相应的substitute_user
		$substitute_user_id = User4workflow::select('substitute_user_id', 'substitute_time')
			->where('user_id', $userid['userid'])
			->first();
// dd($substitute_user_id);
		// 如果没有被选择的用户，则返回所有用户
		if (trim($substitute_user_id['substitute_user_id'])=='') {
			$user['user_unselected'] = $all_user;
			$user['user_substitute_time'] = '';
			return $user;
		}
		
		// 3.查找已分配userid
		$arr_userid = explode(',', $substitute_user_id['substitute_user_id']);
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
		$user['user_substitute_time'] = $substitute_user_id['substitute_time'];
// dd($substitute_user_id['substitute_time']);
// dd($user);
		return $user;
    }


    /**
     * substituteuserSort ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function substituteuserSort(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$sortinfo = $request->only('params.substituteuserid', 'params.index', 'params.userid', 'params.sort');

		// 1.查询所有substituteuserid
		$substituteuserid = User4workflow::select('substitute_user_id')
			->where('user_id', $sortinfo['params']['userid'])
			->first();
		
		// 2.所有查询所有userid变成一维数组
		$arr_substituteuserid = explode(',', $substituteuserid['substitute_user_id']);

		// 3.判断是向前还是向后排序
		$arr_temp = [];
		if ('up' == $sortinfo['params']['sort']) {

			foreach ($arr_substituteuserid as $index => $value) {
				if ($index == $sortinfo['params']['index']-1) {
					$arr_temp[] = $arr_substituteuserid[$index+1];
				} elseif ($index == $sortinfo['params']['index']) {
					$arr_temp[] = $arr_substituteuserid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} elseif ('down' == $sortinfo['params']['sort']) {

			foreach ($arr_substituteuserid as $index => $value) {
				if ($index == $sortinfo['params']['index']) {
					$arr_temp[] = $arr_substituteuserid[$index+1];
				} elseif ($index == $sortinfo['params']['index']+1) {
					$arr_temp[] = $arr_substituteuserid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} else {
			return 0;
		}
		
		$substituteuserid = implode(',', $arr_temp);
		
		// 根据slotid查询相应的user
		$result = User4workflow::where('user_id', $sortinfo['params']['userid'])
			->update([
				'substitute_user_id' => $substituteuserid
			]);
		
		return $result;
    }

	/**
     * user4workflowAdd ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function user4workflowAdd(Request $request)
	 {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$userid = $request->only('params.userid');
		$userid = $userid['params']['userid'];

		$substituteuserid = $request->only('params.substituteuserid');
		$substituteuserid = implode(',', $substituteuserid['params']['substituteuserid']);

		$substituteuserid_before = User4workflow::select('id', 'substitute_user_id')
			->where('user_id', $userid)
			->first();

		// 如果记录为空，则$substituteuserid_after直接为要添加的substituteuserid，并且用create
		if (empty($substituteuserid_before)) {
			$substituteuserid_after = $substituteuserid;

			try {
				$result = User4workflow::create([
					'user_id' => $userid,
					'substitute_user_id' => $substituteuserid_after
				]);
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		
		} else {
			// 如果有记录，则根据id更新即可
			if (trim($substituteuserid_before['substitute_user_id'])=='') {
				$substituteuserid_after = $substituteuserid;
			} else {
				$substituteuserid_after = $substituteuserid_before['substitute_user_id'] . ',' . $substituteuserid;
			}

			try {
				$result = User4workflow::where('id', $substituteuserid_before['id'])
					->update([
						'substitute_user_id' => $substituteuserid_after
					]);
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		}
			
		return $result;
	}

	/**
     * user4workflowRemove ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function user4workflowRemove(Request $request)
	{
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$userid = $request->only('params.userid');
		$userid = $userid['params']['userid'];

		$index = $request->only('params.index');
		$index = $index['params']['index'];

		$substituteuserid_before = User4workflow::select('substitute_user_id')
			->where('user_id', $userid)
			->first();

		$substituteuserid_before = explode(',', $substituteuserid_before['substitute_user_id']);

		$substituteuserid_after = [];
		foreach ($substituteuserid_before as $key => $value) {
			if ($key != $index) {
				$substituteuserid_after[] = $value;
			}
		}

		$substituteuserid_after = implode(',', $substituteuserid_after);

		try {
			$result = User4workflow::where('user_id', $userid)
				->update([
					'substitute_user_id' => $substituteuserid_after
				]);
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}

		return $result;
	}
	
	/**
     * saveSubstitutetime ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function saveSubstitutetime(Request $request)
	{
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$userid = $request->only('params.userid');
		$userid = $userid['params']['userid'];

		$substitute_time = $request->only('params.substitute_time');
		$substitute_time = $substitute_time['params']['substitute_time'];
// dd($userid);	
// dd($substitute_time);

		$user = User4workflow::select('id')
			->where('user_id', $userid)
			->first();

		// 如果无记录，则忽略。无代理人时，暂不create记录。
		if (empty($user)) {
			return 0;
		} else {
			try {
				$result = User4workflow::where('user_id', $userid)
					->update([
						'substitute_time' => $substitute_time
					]);
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		}

		return $result;

	}

}
