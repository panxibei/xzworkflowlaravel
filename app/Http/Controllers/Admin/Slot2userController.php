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

    /**
     * slot2field列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slot2userGets(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$limit = $request->only('limit');
		$limit = empty($limit) ? 10 : $limit;
// dd($limit);
		// 所有的slot
		// $slot = array_reverse(Slot::limit($limit)->pluck('name', 'id')->toArray());
		$mailinglist = Mailinglist::limit($limit)->pluck('name', 'id');//->toArray();
		return $mailinglist;
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

		$mailinglistid = $request->only('mailinglistid');

		// 根据slotid查询相应的field
		$template_id = Mailinglist::select('template_id')
			->where('id', $mailinglistid['mailinglistid'])
			->first();
// dd($template_id['template_id']);
		if (trim($template_id['template_id'])=='') return null;
		
		$slot_id = Template2slot::select('slot_id')
			->where('template_id', $template_id['template_id'])
			->first();
// dd($slot_id['slot_id']);
		
		$arr_slotid = explode(',', $slot_id['slot_id']);
// dd($arr_slotid);		
		
		foreach ($arr_slotid as $value) {
			$slot[] = Slot::select('id', 'name')
				->where('id', $value)
				->first();
		}
		$slot = array_column($slot, 'name', 'id');

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

		$slotid = $request->only('slotid');
		
		// 1.所有user
		$all_user = User::pluck('name', 'id')->toArray();

		// 2.根据slotid查询相应的user
		$user_id = Slot2user::select('user_id')
			->where('slot_id', $slotid['slotid'])
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


    /**
     * usersort ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userSort(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$sortinfo = $request->only('params.userid', 'params.index', 'params.slotid', 'params.sort');
// dd($sortinfo['params']['index']);

		// 1.查询所有userid
		$userid = Slot2user::select('user_id')
			->where('slot_id', $sortinfo['params']['slotid'])
			->first();
		
		// 2.所有查询所有userid变成一维数组
		$arr_userid = explode(',', $userid['user_id']);
// dd($arr_userid);

		// 3.判断是向前还是向后排序
		$arr_temp = [];
		if ('up' == $sortinfo['params']['sort']) {

			foreach ($arr_userid as $index => $value) {
				if ($index == $sortinfo['params']['index']-1) {
					$arr_temp[] = $arr_userid[$index+1];
				} elseif ($index == $sortinfo['params']['index']) {
					$arr_temp[] = $arr_userid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} elseif ('down' == $sortinfo['params']['sort']) {

			foreach ($arr_userid as $index => $value) {
				if ($index == $sortinfo['params']['index']) {
					$arr_temp[] = $arr_userid[$index+1];
				} elseif ($index == $sortinfo['params']['index']+1) {
					$arr_temp[] = $arr_userid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} else {
			return 0;
		}
		
		$userid = implode(',', $arr_temp);
// dd($fieldid);
		
		// 根据slotid查询相应的user
		$result = Slot2user::where('slot_id', $sortinfo['params']['slotid'])
			->update([
				'user_id' => $userid
			]);
// dd($result);
		
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

		$slotid = $request->only('params.slotid');
		$slotid = $slotid['params']['slotid'];

		$userid = $request->only('params.userid');
		$userid = implode(',', $userid['params']['userid']);

		$userid_before = Slot2user::select('id', 'user_id')
			->where('slot_id', $slotid)
			->first();

		// 如果记录为空，则$fieldid_after直接为要添加的userid，并且用create
		if (empty($userid_before)) {
			$userid_after = $userid;

			try {
				$result = Slot2user::create([
					'slot_id' => $slotid,
					'user_id' => $userid_after
				]);
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		
		} else {
			// 如果有记录，则根据id更新即可
			if (trim($userid_before['user_id'])=='') {
				$userid_after = $userid;
			} else {
				$userid_after = $userid_before['user_id'] . ',' . $userid;
			}

			try {
				$result = Slot2user::where('id', $userid_before['id'])
					->update([
						'user_id' => $userid_after
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
     * slot2userRemove ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function slot2userRemove(Request $request)
	{
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$slotid = $request->only('params.slotid');
		$slotid = $slotid['params']['slotid'];

		$index = $request->only('params.index');
		$index = $index['params']['index'];

		$userid_before = Slot2user::select('user_id')
			->where('slot_id', $slotid)
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
			$result = Slot2user::where('slot_id', $slotid)
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
	
	
	
	
}
