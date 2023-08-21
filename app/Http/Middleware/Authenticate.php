<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        //セッションが切れた後にログインするとエラーが発生する問題は、とりあえず、
        // php artisan config:clear　と
        // php artisan cache:clear　をしたらとりあえず発生しなくなった。
        // 原因はまだよくわかっていない
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
