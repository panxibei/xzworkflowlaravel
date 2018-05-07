<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;

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
		$value = Cookie::get('token');
		dd($value);

		$credentials = request(['name', 'password']);

		if (! auth()->validate($credentials)) {
			// credentials are invalid
			dd('credentials are invalid');
		}
		

        // return $this->respondWithToken($token);
        // return $token;

		// 保存请求内容
		$response = $next($request);

		// 请求后处理内容

		// 返回请求
		return $response;
    }
}
