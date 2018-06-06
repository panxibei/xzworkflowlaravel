<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use App\Models\User;
use Cookie;
use Validator;
use Adldap\Laravel\Facades\Adldap;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$config = Config::pluck('cfg_value', 'cfg_name')->toArray();

        return view('home.login', $config);
    }

    public function checklogin(Request $request)
    {
		if ($request->isMethod('post')) {

			// 1.判断验证码
			$rules = ['captcha' => 'required|captcha'];
			// $validator = Validator::make(Input::all(), $rules);
			$validator = Validator::make($request->all(), $rules);
			if ($validator->fails()) {
				// echo '<p style="color: #ff0000;">Incorrect!</p>';
				// dd('<p style="color: #ff0000;">Incorrect!</p>');
				return null;
			} else {
				// echo '<p style="color: #00ff30;">Matched :)</p>';
				// dd('<p style="color: #00ff30;">Matched :)</p>');
			}
			
			// 2.adldap判断AD认证
			if (env('ADLDAP_USE_ADLDAP') == 'adldap') {
				$user = $request->only('name', 'password');

				try {
					$adldap = Adldap::auth()->attempt(
						// $user['name'] . env('ADLDAP_ADMIN_ACCOUNT_SUFFIX'),
						$user['name'],
						$user['password']
						);
						
					// 获取用户email
					$user_tmp = Adldap::search()->users()->find($user['name']);		
					$user['email'] = $user_tmp['mail'][0];
				}
				// catch (Exception $e) {
				catch (\Adldap\Auth\BindException $e) { //捕获异常
					// echo 'Message: ' .$e->getMessage();
					$adldap = false;
				}
				
// dd($adldap);
				// 3.如果adldap认证成功，则同步本地用户的密码
				//   否则认证失败再由jwt-auth本地判断
				if ($adldap) {

					// 同步本地用户密码
					try	{
						$result = User::where('name', $user['name'])
							->update([
								'password'=>bcrypt($user['password'])
							]);

						// 4.如果没有这个用户，则自动新增用户
						if ($result == 0) {
							$nowtime = date("Y-m-d H:i:s",time());

							// $user['email'] = $user['name'] . env('ADLDAP_ACCOUNT_SUFFIX');

							$result = User::create([
								'name'     => $user['name'],
								'email'    => $user['email'],
								'password' => bcrypt($user['password']),
								'login_time' => $nowtime,
								'login_ip' => '127.0.0.1',
								'login_counts' => 1,
								'remember_token' => '',
								'created_at' => $nowtime,
								'updated_at' => $nowtime,
								'deleted_at' => NULL
							]);
						}
						// dd($result);
					}
					catch (Exception $e) {//捕获异常
						// echo 'Message: ' .$e->getMessage();
						// $result = 0;
						$result = $e->getMessage();
					}

				} else {
					// 注意：adldap认证失败再由jwt-auth本地判断，不返回失败
					// return null;
				}

			}


			// 5.jwt-auth，判断用户认证
			// $credentials['name'] = $request->input('name');
			// $credentials['password'] = $request->input('password');
			$credentials = $request->only('name', 'password');

			if (! $token = auth()->attempt($credentials)) {
				// 如果认证失败，则返回null
				// return response()->json(['error' => 'Unauthorized'], 401);
				return null;
			}

			// return $this->respondWithToken($token);
			// $minutes = 480;
			$minutes = config('jwt.ttl', 60);
			Cookie::queue('token', $token, $minutes);
			return $token;
		
		} else {
			return null;
		}
		
    }
	
	public function username()
	{
		return 'username';
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
}
