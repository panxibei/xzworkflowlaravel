<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
		
		// laravel-permission异常处理
		if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {

			if (view()->exists('errors.' . $code)) {

				// return '没有权限！';
				// dd('没有权限！');
				dd($exception->getMessage());
				// return response()->view('error.'.$exception->getStatusCode(), [],$exception->getStatusCode());
			
			}
		}

		/* 错误页面 */
		if ($exception instanceof HttpException) {
			$code = $exception->getStatusCode();

			if (view()->exists('errors.' . $code)) {
				$message  = $exception->getMessage();
				return response()->view('errors.' . $code, ['message'=>$message], $code);
			}
		}


        return parent::render($request, $exception);
    }
}
