<?php

namespace App\Http\Middleware;

use Closure;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

		// 请求前处理内容
		// return $next($request);
		
		
		// 获取JSON格式的jwt-auth用户响应
		$me = response()->json(auth()->user());
		
		// 获取JSON格式的jwt-auth用户信息（$me->getContent()），就是$me的data部分
		$user = json_decode($me->getContent(), true);
		// 用户信息：$user['id']、$user['name'] 等

		// 判断数组为空，以此来判断是否有有效用户登录
		if (! sizeof($user)) {
			// 无有效用户登录，则认证失败，退回登录界面
			// dd('credentials are invalid');
			if($request->ajax()){
				// 如果是ajax请求，则返回空数组，由axios处理返回登录页面
				return response()->json();
			} else {
				// 如果是正常请求，则直接返回登录页面
				return redirect()->route('login');
			}
		}


		// 保存请求内容
		$response = $next($request);


		// 请求后处理内容


		// 返回请求
		return $response;
    }
}
