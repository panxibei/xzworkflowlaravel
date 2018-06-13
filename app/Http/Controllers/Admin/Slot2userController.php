<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Slot2user;
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
     * changeSlot ajax
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
			->where('id', $mailinglistid)
			->first();
// dd($template_id);
		if (empty($template_id)) return 0;
		
		$slot_id = Template2slot::select('slot_id')
			->where('template_id', $template_id['template_id'])
			->first();
// dd($slot_id);
		
		$arr_slotid = explode(',', $slot_id['slot_id']);
dd($arr_slotid);		
		
		// 所有的field
		// $field = Field::select('id', 'name')
			// ->whereIn('id', $arr_fieldid)
			// ->orderByRaw(DB::raw("FIELD(id, ".$fieldid[0]['field_id']." )"))
			// ->get()
			// ->toArray();
		
		foreach ($arr_fieldid as $value) {
			$field[] = Field::select('id', 'name')
				->where('id', $value)
				->first();
				// ->toArray();
		}
// dd($field);

		return $field;
    }	

}
