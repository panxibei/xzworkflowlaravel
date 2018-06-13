<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Template;
use App\Models\Mailinglist;

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
		
		$result = Mailinglist::create([
			'name'	=> $new['name'],
			'template_id'	=> $new['templateid'],
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
	
}
