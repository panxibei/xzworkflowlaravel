<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Template;
use App\Models\Template2slot;
use App\Models\Slot2user;
use App\Models\Mailinglist;
use DB;

class MailinglistController extends Controller
{

    /**
     * 列出mailinglist页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mailinglistIndex()
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

        return view('admin.mailinglist', $share);
    }
	
	// delete
    public function mailinglistIndex0()
    {
		$me = response()->json(auth()->user());
		$user = json_decode($me->getContent(), true);
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
		$share = compact('config', 'user');
        return view('admin.mailinglist0', $share);
    }

    /**
     * 列出mailinglist页面 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mailinglistList(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取用户信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		
		$queryfilter_name = $request->input('queryfilter_name');
		$queryfilter_email = $request->input('queryfilter_email');
		$queryfilter_datefrom = $request->input('queryfilter_datefrom');
		$queryfilter_dateto = $request->input('queryfilter_dateto');

		$queryfilter_datefrom = strtotime($queryfilter_datefrom) ? $queryfilter_datefrom : '1970-01-01';
		$queryfilter_dateto = strtotime($queryfilter_dateto) ? $queryfilter_dateto : '9999-12-31';


		if (null == $page) $page = 1;

		$mailinglist = Mailinglist::select('id', 'name', 'template_id', 'isdefault', 'slot2user_id', 'created_at', 'updated_at')
			->where('name', 'like', '%'.$queryfilter_name.'%')
			->paginate($perPage, ['*'], 'page', $page);
			
		foreach ($mailinglist as $key => $value) {
			$template_name = Template::select('name')
				->where('id', $value['template_id'])
				->first();
			$mailinglist[$key]['template_name'] = $template_name['name'];
			// dd($mailinglist[$key]['template_name']);
		}
// dd($mailinglist[0]);
		return $mailinglist;
    }

    /**
     * 列出loadTemplateid页面 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function loadTemplateid(Request $request)
    {
		if (! $request->ajax()) { return null; }
		$templateid = Template::orderBy('id', 'desc')->pluck('name', 'id');
		return $templateid;
    }
	

    /**
     * 创建Mailinglist ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mailinglistCreate(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		$new = $request->only('name', 'templateid');
		
		// 1.查询template指向哪些slot
		$slot_id = Template2slot::select('slot_id')
			->where('template_id', $new['templateid'])
			->first();
		$slot_id = explode(',', $slot_id['slot_id']);

		// 2.添加相应的slot_id和空user_id到slot2user中去
		foreach ($slot_id as $value) {
			try {
				$result = Slot2user::create([
					'slot_id'	=> $value,
					'user_id'	=> ''
				]);
			} catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				return null;
			}
			$slot2user_id[] = $result['id'];
		}
		// dd($slot2user_id);
		
		// 3.收集slot2user_id
		$new['slot2user_id'] = implode(',', $slot2user_id);
		
		// 4.添加到mailinglist中去
		$result = Mailinglist::create([
			'name'	=> $new['name'],
			'template_id'	=> $new['templateid'],
			'slot2user_id'	=> $new['slot2user_id']
		]);

		return $result;
    }
	

    /**
     * 编辑mailinglist ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mailinglistEdit(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		$update = $request->only('id', 'name', 'template_id');

		try	{
			$result = Mailinglist::where('id', $update['id'])
				->update([
					'name'=>$update['name'],
					'template_id'=>$update['template_id'],
				]);

		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
		
		return $result;
    }


    /**
     * mailinglistDelete
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mailinglistDelete(Request $request)
    {
        //
		if (! $request->isMethod('post') || ! $request->ajax()) { return false; }

		$mailinglist_id = $request->only('mailinglist_id');
		
		// 1.删除mailinglist相关的slot2user项
		$slot2user_id = Mailinglist::select('slot2user_id')
			->where('id', $mailinglist_id['mailinglist_id'])
			->first();
		
		DB::beginTransaction();
		if (! empty($slot2user_id['slot2user_id'])) {
		
			$slot2user_id = explode(',', $slot2user_id['slot2user_id']);
		
			foreach ($slot2user_id as $value) {
				try {
					$result = Slot2user::where('id', $value)->delete();
				}
				catch (Exception $e) {
					// echo 'Message: ' .$e->getMessage();
					DB::rollBack();
					return null;
				}

				if (! $result) {
					DB::rollBack();
					return null;
				}
			}
		}

		// 2.删除mailinglist本身
		try	{
			$result = Mailinglist::where('id', $mailinglist_id['mailinglist_id'])->delete();
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			DB::rollBack();
			return null;
		}
		
		DB::commit();
		return $result;
    }
	
}
