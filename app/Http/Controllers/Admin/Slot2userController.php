<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Slot2user;
use App\Models\User;
use App\Models\Template2slot;
use App\Models\Slot;
use App\Models\Mailinglist;
use DB;

class Slot2userController extends Controller
{
    /**
     * 列出slot2field页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slot2userIndex()
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

        return view('admin.slot2user', $share);
    }
	
	// delete
    public function slot2userIndex0()
    {
		$me = response()->json(auth()->user());
		$user = json_decode($me->getContent(), true);
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
		$share = compact('config', 'user');
        return view('admin.slot2user0', $share);
    }

    /**
     * slot2field列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slot2userGets(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$limit = $request->input('limit');
		$limit = empty($limit) ? 1000 : $limit;
// dd($limit);
		// 所有的slot
		// $slot = array_reverse(Slot::limit($limit)->pluck('name', 'id')->toArray());
		$mailinglist = Mailinglist::limit($limit)->pluck('name', 'id')->toArray();
		
		// 所有的user
		$user = User::orderBy('id', 'desc')->limit($limit)->pluck('name', 'id')->toArray();

		$slot2user = compact('mailinglist', 'user');
		
		return $slot2user;
    }

    /**
     * changeMailinglist ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeMailinglist(Request $request)
    {
		if (! $request->ajax()) { return null; }

		$mailinglist_id = $request->input('mailinglist_id');

		// 1.查询slot2user_id
		$slot2user_id = Mailinglist::select('slot2user_id')
			->where('id', $mailinglist_id)
			->first();

		if (trim($slot2user_id['slot2user_id'])=='') return null;
		
		$slot2user_id = explode(',', $slot2user_id['slot2user_id']);
		// dd($slot2user_id);
		
		// 2.查询slot_id及user_id
		foreach ($slot2user_id as $key => $value) {
			$slot_and_user_id[$key] = Slot2user::select('id', 'slot_id')
				->where('id', $value)
				->first();
			
			$tmp = Slot::select('name')
				->where('id', $slot_and_user_id[$key]['slot_id'])
				->first();
			$slot_and_user_id[$key]['name'] = $tmp['name'];
		}
		
// dd($slot_and_user_id);
		$slot = array_column($slot_and_user_id, 'name', 'id');
		
		return $slot;
    }

    /**
     * changeSlot ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeSlot(Request $request)
    {
		if (! $request->ajax()) { return null; }

		$slot2user_id = $request->input('slot2user_id');
		
		// 1.所有user
		// $all_user = User::pluck('name', 'id')->toArray();

		// 2.根据slotid查询相应的user
		$user_id = Slot2user::select('user_id')
			->where('id', $slot2user_id)
			->first();
// dd($user_id['user_id']);
		// 如果没有被选择的用户，则返回所有用户
		// if (trim($user_id['user_id'])=='') {
			// $user['user_unselected'] = $all_user;
			// return $user;
		// }
		
		if (empty($user_id)) return null;
		if (trim($user_id['user_id'])=='') return null;
		
		// 3.查找已分配userid
		$arr_userid = explode(',', $user_id['user_id']);
		foreach ($arr_userid as $value) {
			$user[] = User::select('id', 'name')
				->where('id', $value)
				->first();
		}

		return $user;

    }	


    /**
     * usersort ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userSort(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$sortinfo = $request->only('slot2user_id', 'index', 'userid', 'sort');
// dd($sortinfo['slot2user_id']);

		// 1.查询现有userid
		$userid = Slot2user::select('user_id')
			->where('id', $sortinfo['slot2user_id'])
			->first();
		
		// 2.所有查询所有userid变成一维数组
		$arr_userid = explode(',', $userid['user_id']);
// dd($arr_userid);

		// 3.判断是向前还是向后排序
		$arr_temp = [];
		if ('up' == $sortinfo['sort']) {

			foreach ($arr_userid as $index => $value) {
				if ($index == $sortinfo['index']-1) {
					$arr_temp[] = $arr_userid[$index+1];
				} elseif ($index == $sortinfo['index']) {
					$arr_temp[] = $arr_userid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} elseif ('down' == $sortinfo['sort']) {

			foreach ($arr_userid as $index => $value) {
				if ($index == $sortinfo['index']) {
					$arr_temp[] = $arr_userid[$index+1];
				} elseif ($index == $sortinfo['index']+1) {
					$arr_temp[] = $arr_userid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} else {
			return 0;
		}
		
		$userid = implode(',', $arr_temp);
// dd($userid);
		
		// 4.排序好后写入数据库
		try {
			$result = Slot2user::where('id', $sortinfo['slot2user_id'])
				->update([
					'user_id' => $userid
				]);
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
			
		return $result;
    }
	
	/**
     * slot2userAdd ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function slot2userAdd(Request $request)
	 {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$slot2userid = $request->only('slot2userid');

		$userid = $request->only('userid');
		$userid = implode(',', $userid['userid']);

		// 1.先取出要添加用户的slot2user表的user_id
		$userid_before = Slot2user::select('user_id')
			->where('id', $slot2userid['slot2userid'])
			->first();

		// 2.如果记录为空，则$fieldid_after直接添加userid
		if (trim($userid_before['user_id']) == 0) {
			$userid_after = $userid;
		} else {
			$userid_after = $userid_before['user_id'] . ',' . $userid;
		}

		// 3.写入数据
		try {
			$result = Slot2user::where('id', $slot2userid['slot2userid'])
				->update([
					'user_id' => $userid_after
				]);
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
			
		return $result;
	}	
	
	/**
     * slot2userRemove ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function slot2userRemove(Request $request)
	{
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$slot2user_id = $request->input('slot2user_id');

		$index = $request->input('index');

		$userid_before = Slot2user::select('user_id')
			->where('id', $slot2user_id)
			->first();

		$userid_before = explode(',', $userid_before['user_id']);

		$userid_after = [];
		foreach ($userid_before as $key => $value) {
			if ($key != $index) {
				$userid_after[] = $value;
			}
		}

		$userid_after = implode(',', $userid_after);

		try {
			$result = Slot2user::where('id', $slot2user_id)
				->update([
					'user_id' => $userid_after
				]);
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}

		return $result;
	}
	

	/**
     * slot2userUpdate
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function slot2userUpdate(Request $request)
	 {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$slot2user_id = $request->input('slot2user_id');
		$user_id = $request->input('user_id');
		$user_id = implode(',', $user_id);

		$userid_exist = Slot2user::select('id')
			->where('id', $slot2user_id)
			->first();

		// 如果记录为空，则$fieldid_after直接为要添加的fieldid，并且用create
		if (empty($userid_exist)) {

			try {
				$result = Slot2user::create([
					'id' => $slot2user_id,
					'user_id' => $user_id
				]);
				$result = 1;
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		
		} else {
			// 如果有记录，则根据id更新即可
			try {
				$result = Slot2user::where('id', $slot2user_id)
					->update([
						'user_id' => $user_id
					]);
				$result = 1;
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		}
			
		return $result;
	}
		
	
}
