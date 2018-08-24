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
	
	// delete
    public function slot2fieldIndex0()
    {
		$me = response()->json(auth()->user());
		$user = json_decode($me->getContent(), true);
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
		$share = compact('config', 'user');
        return view('admin.slot2field0', $share);
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
		$limit = empty($limit) ? 1000 : $limit['limit'];
// dd($limit);
		// 所有的slot
		// $slot = array_reverse(Slot::limit($limit)->pluck('name', 'id')->toArray());
		$slot = Slot::orderBy('id', 'desc')->limit($limit)->pluck('name', 'id')->toArray();

		// 所有的field
		// $field = array_reverse(Field::limit($limit)->pluck('name', 'id')->toArray());
		$field = Field::orderBy('id', 'desc')->limit($limit)->pluck('name', 'id')->toArray();

		$slot2field = compact('slot', 'field');
// dd($slot2field);
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
			->where('slot_id', $slotid['slotid'])
			->first();
// dd($fieldid);
		if (empty($fieldid)) return null;
		if (trim($fieldid['field_id'])=='') return null;
		
		$arr_fieldid = explode(',', $fieldid['field_id']);
// dd($arr_fieldid);		
		
		foreach ($arr_fieldid as $value) {
			$field[] = Field::select('id', 'name')
				->where('id', $value)
				->first();
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

		$sortinfo = $request->only('fieldid', 'index', 'slotid', 'sort');
// dd($sortinfo['index']);

		// 1.查询所有fieldid
		$fieldid = Slot2field::select('field_id')
			->where('slot_id', $sortinfo['slotid'])
			->first();
		
		// 2.所有fieldid变成一维数组
		$arr_fieldid = explode(',', $fieldid['field_id']);
// dd($arr_fieldid);

		// 3.判断是向前还是向后排序
		$arr_temp = [];
		if ('up' == $sortinfo['sort']) {

			foreach ($arr_fieldid as $index => $value) {
				if ($index == $sortinfo['index']-1) {
					$arr_temp[] = $arr_fieldid[$index+1];
				} elseif ($index == $sortinfo['index']) {
					$arr_temp[] = $arr_fieldid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} elseif ('down' == $sortinfo['sort']) {

			foreach ($arr_fieldid as $index => $value) {
				if ($index == $sortinfo['index']) {
					$arr_temp[] = $arr_fieldid[$index+1];
				} elseif ($index == $sortinfo['index']+1) {
					$arr_temp[] = $arr_fieldid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} else {
			return 0;
		}
		
		$fieldid = implode(',', $arr_temp);
		
		// 根据slotid查询相应的field
		try {
			$result = Slot2field::where('slot_id', $sortinfo['slotid'])
				->update([
					'field_id' => $fieldid
				]);
			$result = 1;
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
		
		return $result;
    }
	

	/**
     * slot2fieldUpdate
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function slot2fieldUpdate(Request $request)
	 {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$slotid = $request->only('slotid');
		$slotid = $slotid['slotid'];

		$fieldid = $request->only('fieldid');
		$fieldid = implode(',', $fieldid['fieldid']);

		$fieldid_exist = Slot2field::select('id')
			->where('slot_id', $slotid)
			->first();

		// 如果记录为空，则$fieldid_after直接为要添加的fieldid，并且用create
		if (empty($fieldid_exist)) {

			try {
				$result = Slot2field::create([
					'slot_id' => $slotid,
					'field_id' => $fieldid
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
				$result = Slot2field::where('id', $slotid)
					->update([
						'field_id' => $fieldid
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
	
    /**
     * slotReview
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slotReview(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$slotid = $request->only('slotid');
		// dd($slotid['slotid']);

		// 1.查询slot信息
		$slot = Slot::select('id', 'name')
			->where('id', $slotid['slotid'])
			->first();
		$result['slot'] = $slot;

		// 2.查询slot2field信息
		$slot2field = Slot2field::select('field_id')
			->where('slot_id', $slotid['slotid'])
			->first();
		// dd($slot2field);
		
		// 3.查询field信息
		$field_id = explode(',', $slot2field['field_id']);
		if (empty($field_id)) {
			$result['field'] = null;
		} else {
			foreach ($field_id as $value) {
				$result['field'][] = Field::where('id', $value)
					->first();
			}
		}
		// dd($result);
		return $result;
    }	
	
	
	
	
	

	/**
     * slot2fieldAdd 未用保留未用保留
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

		$fieldid_before = Slot2field::select('id', 'field_id')
			->where('slot_id', $slotid)
			->first();

		// 如果记录为空，则$fieldid_after直接为要添加的fieldid，并且用create
		if (empty($fieldid_before)) {
			$fieldid_after = $fieldid;

			try {
				$result = Slot2field::create([
					'slot_id' => $slotid,
					'field_id' => $fieldid_after
				]);
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		
		} else {
			// 如果有记录，则根据id更新即可
			if (trim($fieldid_before['field_id'])=='') {
				$fieldid_after = $fieldid;
			} else {
				$fieldid_after = $fieldid_before['field_id'] . ',' . $fieldid;
			}

			try {
				$result = Slot2field::where('id', $fieldid_before['id'])
					->update([
						'field_id' => $fieldid_after
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
     * slot2fieldRemove 未用保留未用保留
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function slot2fieldRemove(Request $request)
	{
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$slotid = $request->input('slotid');
		$index = $request->input('index');

		$fieldid_before = Slot2field::select('field_id')
			->where('slot_id', $slotid)
			->first();

		$fieldid_before = explode(',', $fieldid_before['field_id']);

		$fieldid_after = [];
		foreach ($fieldid_before as $key => $value) {
			if ($key != $index) {
				$fieldid_after[] = $value;
			}
		}

		$fieldid_after = implode(',', $fieldid_after);

		try {
			$result = Slot2field::where('slot_id', $slotid)
				->update([
					'field_id' => $fieldid_after
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
