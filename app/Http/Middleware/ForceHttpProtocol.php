<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttpProtocol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 本番環境のみ常時SSL化
        if (! $request->secure() && env('APP_ENV') === 'production') {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
