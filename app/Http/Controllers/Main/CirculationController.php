<?php

namespace App\Http\Controllers\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\Circulation;
use App\Models\Template;
use App\Models\Mailinglist;
use App\Models\Template2slot;
use App\Models\Slot2user;
use App\Models\User;
use App\Models\Slot2field;
use App\Models\Field;
use App\Models\Slot;


class CirculationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
    /**
     * 列出circulation页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function circulationIndex()
    {
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等
		
        // 获取配置值
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();
        // return view('admin.user', $config);
		
		$share = compact('config', 'user');
        return view('main.circulation', $share);
    }
	
    /**
     * circulation列表 ajax
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function circulationGets(Request $request)
    {
		if (! $request->ajax()) { return null; }

		$perPage = $request->input('perPage');
		$page = $request->input('page');
		if (null == $page) $page = 1;

		// 重置角色和权限的缓存
		app()['cache']->forget('spatie.permission.cache');

		$circulation = Circulation::select('id', 'guid', 'name', 'template_id', 'mailinglist_id', 'slot2user_id', 'slot_id', 'user_id', 'current_station', 'creator', 'todo_time', 'progress', 'description', 'is_archived', 'created_at')
			->paginate($perPage, ['*'], 'page', $page);
// dd($circulation);
		return $circulation;
    }
	
    /**
     * getTemplateOptions
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTemplateOptions(Request $request)
    {
		if (! $request->ajax()) { return null; }

		$limit = $request->only('limit');
		$limit = empty($limit) ? 10 : $limit;

		$template = Template::limit($limit)->pluck('name', 'id');

		return $template;
    }

    /**
     * changeTemplate
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeTemplate(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$template_id = $request->only('template_id');

		$mailinglist = Mailinglist::where('template_id', $template_id['template_id'])
			->pluck('name', 'id');

		return $mailinglist;
    }

    /**
     * changeMailinglist
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeMailinglist(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		// $template_id = $request->only('template_id');
		$mailinglist_id = $request->only('mailinglist_id');

		// 1.查询Mailinglist
		$template_id = Mailinglist::select('template_id')
			->where('id', $mailinglist_id['mailinglist_id'])
			->first();

		// 2.查询template2slot
		$slot_id = Template2slot::select('slot_id')
			->where('template_id', $template_id['template_id'])
			->first();
		$slot_id = explode(',', $slot_id['slot_id']);

		// 3. 查询Slot2user
		foreach ($slot_id as $key => $value) {
			$user_id[] = Slot2user::select('user_id')
				->where('slot_id', $value)
				->first();
		}

		// 4.查询User
		// $user = [];
		foreach ($user_id as $val_user1) {
			$user1 = explode(',', $val_user1['user_id']);
			
			foreach ($user1 as $val_user2) {
				$user[] = $val_user2;
			}
			$user[] = '-';
		}

		$userinfo = [];
		foreach ($user as $key => $value) {
			if ($value == '-') {
				$userinfo[$key]['user'] = '-';
				$userinfo[$key]['email'] = '-';

				// $userinfo[] = ['user' => '-', 'email' => '-'];
				// $userinfo[]['email'] = '-';
				// $userinfo['email'][] = '-';
				// dd($userinfo);
			} else {
				$username = User::select('name', 'email')
					->where('id', $value)
					->first();
				// $userinfo['user'][] = $username['name'];
				// $userinfo['email'][] = $username['email'];
				$userinfo[$key]['user'] = $username['name'];
				$userinfo[$key]['email'] = $username['email'];
			}
		}
// dd($userinfo);

		// 5.根据Slot查询fieldid
		// dd($slot_id);
		unset($arr_tmp);
		foreach ($slot_id as $key => $value) {
				$slot_name = Slot::select('id', 'name')
				->where('id', $value)
				->first();
			
			$arr_tmp = Slot2field::select('field_id')
				->where('slot_id', $value)
				->first();
			$field_id[$key]['slot_id'] = $value;
			$field_id[$key]['slot_name'] = $slot_name['name'];
			$field_id[$key]['field_id'] = $arr_tmp['field_id'];
		}
		// dd($field_id);
		
		// 6.查询field
		unset($arr_tmp);
		foreach ($field_id as $key => $val_filed_id) {
			$field[$key]['slot_id'] = $val_filed_id['slot_id'];
			$field[$key]['slot_name'] = $val_filed_id['slot_name'];
		
			if (!empty($val_filed_id['field_id'])) {
				$arr_tmp = explode(',', $val_filed_id['field_id']);
				
				foreach ($arr_tmp as $value) {
					$field[$key]['field_id'][] = Field::where('id', $value)
					->first()->toArray();
				}
				
				
			} else {
				$field[$key]['field_id'][] = null;
			}
				
			// }
		}
		// dd($field);
		
		
		
		// dd($userinfo);
		$result = compact('userinfo', 'field');
		// dd($result);
		
		return $result;
    }
	
    /**
     * reviewCirculation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reviewCirculation(Request $request)
    {
		if (! $request->ajax()) { return null; }
		
		$guid = $request->only('guid');
		// dd($guid);
		// 1.查询circulation信息
		$circulation = Circulation::select('guid', 'name', 'mailinglist_id', 'slot_id', 'current_station', 'creator', 'description', 'created_at')
		->where('guid', $guid['guid'])
		->first();
		
		// dd($circulation);
		

		// 1.查询Mailinglist
		$template_id = Mailinglist::select('template_id')
			->where('id', $circulation['mailinglist_id'])
			->first();

		// 2.查询template2slot
		$slot_id = Template2slot::select('slot_id')
			->where('template_id', $template_id['template_id'])
			->first();
		$slot_id = explode(',', $slot_id['slot_id']);

		// 3. 查询Slot2user
		foreach ($slot_id as $key => $value) {
			$user_id[] = Slot2user::select('user_id')
				->where('slot_id', $value)
				->first();
		}

		// 4.查询User
		// $user = [];
		foreach ($user_id as $val_user1) {
			$user1 = explode(',', $val_user1['user_id']);
			
			foreach ($user1 as $val_user2) {
				$user[] = $val_user2;
			}
			$user[] = '-';
		}

		$userinfo = [];
		foreach ($user as $key => $value) {
			if ($value == '-') {
				$userinfo[$key]['user'] = '-';
				$userinfo[$key]['email'] = '-';

				// $userinfo[] = ['user' => '-', 'email' => '-'];
				// $userinfo[]['email'] = '-';
				// $userinfo['email'][] = '-';
				// dd($userinfo);
			} else {
				$username = User::select('name', 'email')
					->where('id', $value)
					->first();
				// $userinfo['user'][] = $username['name'];
				// $userinfo['email'][] = $username['email'];
				$userinfo[$key]['user'] = $username['name'];
				$userinfo[$key]['email'] = $username['email'];
			}
		}
// dd($userinfo);

		// 5.根据Slot查询fieldid
		// dd($slot_id);
		unset($arr_tmp);
		foreach ($slot_id as $key => $value) {
				$slot_name = Slot::select('id', 'name')
				->where('id', $value)
				->first();
			
			$arr_tmp = Slot2field::select('field_id')
				->where('slot_id', $value)
				->first();
			$field_id[$key]['slot_id'] = $value;
			$field_id[$key]['slot_name'] = $slot_name['name'];
			$field_id[$key]['field_id'] = $arr_tmp['field_id'];
		}
		// dd($field_id);
		
		// 6.查询field
		unset($arr_tmp);
		foreach ($field_id as $key => $val_filed_id) {
			$field[$key]['slot_id'] = $val_filed_id['slot_id'];
			$field[$key]['slot_name'] = $val_filed_id['slot_name'];
		
			if (!empty($val_filed_id['field_id'])) {
				$arr_tmp = explode(',', $val_filed_id['field_id']);
				
				foreach ($arr_tmp as $value) {
					$field[$key]['field_id'][] = Field::where('id', $value)
					->first()->toArray();
				}
				
				
			} else {
				$field[$key]['field_id'][] = null;
			}
				
			// }
		}
		// dd($field);
		
		
		
		// dd($userinfo);
		$result = compact('circulation', 'userinfo', 'field');
		// dd($result);
		
		return $result;
    }
	
    /**
     * createCirculation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function createCirculation(Request $request)
	{
		if (! $request->isMethod('post') || ! $request->ajax()) { return null; }

		$circulation = $request->only(
		'params.template_id',
		'params.mailinglist_id',
		'params.description',
		// 'params.user_id',
		'params.creator'
		);
		// dd($circulation);

// dd($_SERVER);
// dd(create_guid());
		// 1.查找template名称
		$template_name = Template::select('name')
			->where('id', $circulation['params']['template_id'])
			->first();
		$template_name = $template_name['name'];
		// dd($template_name);
		
		// 2.查找template2slot，获取第一个[slotid]
		$slot_id = Template2slot::select('slot_id')
			->where('template_id', $circulation['params']['template_id'])
			->first();
		$slot_id = explode(',', $slot_id['slot_id']);
		$slot_id = $slot_id[0];
		// dd($slot_id);
		
		// 3.查找slot2user，获取[slot2user_id]以及第一个[userid]
		$user_id = Slot2user::select('id', 'user_id')
			->where('slot_id', $slot_id)
			->first();
		$slot2user_id = $user_id['id'];
		$user_id = explode(',', $user_id['user_id']);
		$user_id = $user_id[0];
		// dd($slot2user_id);
		
		// 4.查找user名称
		$user_name = User::select('name')
			->where('id', $user_id)
			->first();
		$user_name = $user_name['name'];
		// dd($user_name);
		
		
		
		// 最后写入数据库
		try	{
			$result = Circulation::create([
				'guid'	=> create_guid(),
				'name'	=> $template_name,
				'template_id'	=> $circulation['params']['template_id'],
				'mailinglist_id'	=> $circulation['params']['mailinglist_id'],
				'slot2user_id'	=> $slot2user_id,
				'slot_id'	=> $slot_id,
				'user_id'	=> $user_id,
				'current_station'	=> $user_name,
				'creator'	=> $circulation['params']['creator'],
				'todo_time'	=> date("Y-m-d H:i:s",time()),
				'progress'	=> '0%',
				'description'	=> $circulation['params']['description'],
				'is_archived'	=> 0,
			]);
		}
		catch (Exception $e) {
			// echo 'Message: ' .$e->getMessage();
			$result = 0;
		}
dd($result);
		return $result;
		
	}
	
	
}
