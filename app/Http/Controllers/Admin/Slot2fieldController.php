<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Slot2field;
use App\Models\Slot;
use App\Models\Field;
use DB;

class Slot2fieldController extends Controller
{

    /**
     * 列出slot2field页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slot2fieldIndex()
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

        return view('admin.slot2field', $share);
    }

    /**
     * slot2field列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slot2fieldGets(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$limit = $request->only('limit');
		$limit = sizeof($limit) == 0 ? 10 : $limit;
// dd($limit);
		// 所有的slot
		$slot = array_reverse(Slot::limit($limit)->pluck('name', 'id')->toArray());

		// 所有的field
		$field = array_reverse(Field::limit($limit)->pluck('name', 'id')->toArray());

		$slot2field = compact('slot', 'field');
		
		return $slot2field;
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
		
		// 根据slotid查询相应的field
		$fieldid = Slot2field::select('field_id')
			->where('slot_id', $slotid)
			->get()->toArray();
// dd($fieldid);
		if ($fieldid==[]) return 0;
		
		$arr_fieldid = explode(',', $fieldid[0]['field_id']);
// dd($arr_fieldid);		
		
		// 所有的field
		// $field = Field::select('id', 'name')
			// ->whereIn('id', $arr_fieldid)
			// ->orderByRaw(DB::raw("FIELD(id, ".$fieldid[0]['field_id']." )"))
			// ->get()
			// ->toArray();
		
		foreach ($arr_fieldid as $value) {
			$field[] = Field::select('id', 'name')
				->where('id', $value)
				->first()
				->toArray();
		}

// dd($field);

		return $field;
    }

    /**
     * fieldSort ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fieldSort(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$sortinfo = $request->only('params.fieldid', 'params.index', 'params.slotid', 'params.sort');
// dd($sortinfo['params']['index']);

		// 1.查询所有fieldid
		$fieldid = Slot2field::select('field_id')
			->where('slot_id', $sortinfo['params']['slotid'])
			->get()->toArray();
		
		// 2.所有fieldid变成一维数组
		$arr_fieldid = explode(',', $fieldid[0]['field_id']);
// dd($arr_fieldid);

		// 3.判断是向前还是向后排序
		$arr_temp = [];
		if ('up' == $sortinfo['params']['sort']) {

			foreach ($arr_fieldid as $index => $value) {
				if ($index == $sortinfo['params']['index']-1) {
					$arr_temp[] = $arr_fieldid[$index+1];
				} elseif ($index == $sortinfo['params']['index']) {
					$arr_temp[] = $arr_fieldid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} elseif ('down' == $sortinfo['params']['sort']) {

			foreach ($arr_fieldid as $index => $value) {
				if ($index == $sortinfo['params']['index']) {
					$arr_temp[] = $arr_fieldid[$index+1];
				} elseif ($index == $sortinfo['params']['index']+1) {
					$arr_temp[] = $arr_fieldid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} else {
			return 0;
		}
		
		$fieldid = implode(',', $arr_temp);
// dd($fieldid);
		
		// 根据slotid查询相应的field
		$result = Slot2field::where('slot_id', $sortinfo['params']['slotid'])
			->update([
				'field_id' => $fieldid
			]);
// dd($result);
		
		return $result;
    }

	/**
     * slot2fieldAdd ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function slot2fieldAdd(Request $request)
	 {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$slotid = $request->only('params.slotid');
		$slotid = $slotid['params']['slotid'];

		$fieldid = $request->only('params.fieldid');
		$fieldid = implode(',', $fieldid['params']['fieldid']);
// dd($slotid['params']['slotid']);

		$fieldid_before = Slot2field::select('field_id')
			->where('slot_id', $slotid)
			->get()->toArray();
// dd(implode(',', $fieldid_before[0]));

		if (sizeof($fieldid_before) == 0) {
			$fieldid_after = $fieldid;
// dd($slotid);			
			try {
				$result = Slot2field::create([
					'slot_id' => $slotid,
					'field_id' => $fieldid_after
				]);
				$result = 1;
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
// dd($result);
		} else {
			$fieldid_after = implode(',', $fieldid_before[0]) . ',' . $fieldid;

			try {
				$result = Slot2field::where('slot_id', $slotid)
					->update([
						'slot_id' => $slotid,
						'field_id' => $fieldid_after
					]);
				$result = 1;
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		
		}
// dd($fieldid_after);
// dd($result);
		return $result;

	 }


}