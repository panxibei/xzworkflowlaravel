<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Template;

class TemplateController extends Controller
{

    /**
     * 列出template页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function templateIndex()
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

        return view('admin.template', $share);
    }	
	
    /**
     * template列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function templateGets(Request $request)
    {
		if (! $request->ajax()) { return null; }

        // 获取角色信息
		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		$template = Template::select('id', 'name', 'created_at', 'updated_at')
			->paginate($perPage, ['*'], 'page', $page);

		return $template;
    }

    /**
     * template create or update ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function templateCreateOrUpdate(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$postdata = $request->input('params.postdata');
		// dd($postdata);
		
		if ('create' == $postdata['createorupdate']) {
			// 新增template
			try	{
				$result = Template::create([
					'name' => $postdata['name'],
				]);

				$result = 1;
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}

			return $result;
		
		} elseif ('update' == $postdata['createorupdate']) {
			
			// 更新template
			try	{
				$result = Template::where('id', $postdata['id'])
					->update([
						'name' => $postdata['name'],
					]);
				
				$result = 1;
			}
			catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				$result = 0;
			}
		} else {
			$result = 0;
		}
    }
	
    /**
     * 删除template ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function templateDelete(Request $request)
    {
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$id = $request->only('params.id');

		$result = Template::whereIn('id', $id)->delete();
		return $result;

	}
}
