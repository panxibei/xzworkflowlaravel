<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;
use Cookie;
use Validator;

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

			// 判断验证码
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

			// jwt-auth，判断用户认证
			$credentials['name'] = $request->input('username');
			$credentials['password'] = $request->input('password');

			if (! $token = auth()->attempt($credentials)) {
				// 如果认证失败，则返回null
				// return response()->json(['error' => 'Unauthorized'], 401);
				return null;
			}

			// return $this->respondWithToken($token);
			$minutes = 30;
			Cookie::queue('token', $token, $minutes);
			return $token;
		
		} else {
			return null;
		}
		
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
