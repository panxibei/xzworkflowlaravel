<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Template2slot;
use App\Models\Slot;
use App\Models\Template;
use DB;

class Template2slotController extends Controller
{

    /**
     * 列出template2slot页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function template2slotIndex()
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

        return view('admin.template2slot', $share);
    }

    /**
     * template2slot列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function template2slotGets(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$limit = $request->only('limit');
		$limit = empty($limit) ? 10 : $limit;
// dd($limit);
		// 所有的slot
		// $slot = array_reverse(Slot::limit($limit)->pluck('name', 'id')->toArray());
		$template = Template::limit($limit)->pluck('name', 'id')->toArray();

		// 所有的field
		// $field = array_reverse(Field::limit($limit)->pluck('name', 'id')->toArray());
		$slot = Slot::limit($limit)->pluck('name', 'id')->toArray();

		$template2slot = compact('template', 'slot');
		
		return $template2slot;
    }

    /**
     * changeTemplate ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeTemplate(Request $request)
    {
		if (! $request->ajax()) { return null; }

		$templateid = $request->only('templateid');
		
		// 根据slotid查询相应的field
		$slotid = Template2slot::select('slot_id')
			->where('template_id', $templateid)
			->first();
// dd(empty($slotid));
		if (empty($slotid)) return 0;
		
		$arr_slotid = explode(',', $slotid['slot_id']);
// dd($arr_slotid);		
		
		// 所有的field
		foreach ($arr_slotid as $value) {
			$slot[] = Slot::select('id', 'name')
				->where('id', $value)
				->first();
				// ->toArray();
		}
// dd($slot);

		return $slot;
    }

    /**
     * slotSort ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slotSort(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$sortinfo = $request->only('params.slotid', 'params.index', 'params.templateid', 'params.sort');
// dd($sortinfo);
// dd($sortinfo['params']['index']);

		// 1.查询所有slotid
		$slotid = Template2slot::select('slot_id')
			->where('template_id', $sortinfo['params']['templateid'])
			->first();
		
		// 2.所有slotid变成一维数组
		$arr_slotid = explode(',', $slotid['slot_id']);
// dd($arr_slotid);

		// 3.判断是向前还是向后排序
		$arr_temp = [];
		if ('up' == $sortinfo['params']['sort']) {

			foreach ($arr_slotid as $index => $value) {
				if ($index == $sortinfo['params']['index']-1) {
					$arr_temp[] = $arr_slotid[$index+1];
				} elseif ($index == $sortinfo['params']['index']) {
					$arr_temp[] = $arr_slotid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} elseif ('down' == $sortinfo['params']['sort']) {

			foreach ($arr_slotid as $index => $value) {
				if ($index == $sortinfo['params']['index']) {
					$arr_temp[] = $arr_slotid[$index+1];
				} elseif ($index == $sortinfo['params']['index']+1) {
					$arr_temp[] = $arr_slotid[$index-1];
				} else {
					$arr_temp[] = $value;
				}
			}

		} else {
			return 0;
		}
		
		$slotid = implode(',', $arr_temp);
// dd($slotid);
		
		// 根据templateid查询相应的slot
		$result = Template2slot::where('template_id', $sortinfo['params']['templateid'])
			->update([
				'slot_id' => $slotid
			]);
// dd($result);
		
		return $result;
    }

	/**
     * template2slotAdd ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 public function template2slotAdd(Request $request)
	 {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$templateid = $request->only('params.templateid');
		$templateid = $templateid['params']['templateid'];

		$slotid = $request->only('params.slotid');
		$slotid = implode(',', $slotid['params']['slotid']);
// dd($templateid);
// dd($slotid);

		$slotid_before = Template2slot::select('slot_id')
			->where('template_id', $templateid)
			->first();
// dd($slotid_before);

		if (empty($slotid_before)) {
			$slotid_after = $slotid;

			try {
				$result = Template2slot::create([
					'template_id' => $templateid,
					'slot_id' => $slotid_after
				]);
				$result = 1;
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}

		} else {
			$slotid_before = explode(',', $slotid_before['slot_id']);
			
			$slotid_after = implode(',', $slotid_before) . ',' . $slotid;

			try {
				$result = Template2slot::where('template_id', $templateid)
					->update([
						'slot_id' => $slotid_after
					]);
				$result = 1;
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		
		}
dd($result);
		return $result;

	}

	/**
     * template2slotRemove ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function template2slotRemove(Request $request)
	{
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$templateid = $request->only('params.templateid');
		$templateid = $templateid['params']['templateid'];

		$index = $request->only('params.index');
		$index = $index['params']['index'];

		$slotid_before = Template2slot::select('slot_id')
			->where('template_id', $templateid)
			->first();

		$slotid_before = explode(',', $slotid_before['slot_id']);

		foreach ($slotid_before as $key => $value) {
			if ($key != $index) {
				$slotid_after[] = $value;
			}
		}

		$slotid_after = implode(',', $slotid_after);

		try {
			$result = Template2slot::where('template_id', $templateid)
				->update([
					'slot_id' => $slotid_after
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
