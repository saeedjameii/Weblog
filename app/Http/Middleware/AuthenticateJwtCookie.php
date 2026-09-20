<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateJwtCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('token');

        if ($token) {
            try {
                $user = JWTAuth::setToken($token)->authenticate();
                if($user){
                    auth()->setUser($user);
                }
                if($user && $user->trashed()){
                    JWTAuth::invalidate($token);
                    return redirect()->route('login')->withCookie(cookie()->forget('token'))->withErrors([
                        'email' => 'حساب کاربری شما حذف شده است']);
                }
            } catch (\Throwable $e) {

            }
        }

        return $next($request);
    }
}