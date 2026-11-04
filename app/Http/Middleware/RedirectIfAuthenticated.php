<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth('api')->check()){
            return redirect()->route('home')->with('info', 'شما قبلا وارد حساب کاریری خود شده اید');
        }
        return $next($request);
    }
}
