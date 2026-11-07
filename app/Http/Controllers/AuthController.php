<?php

namespace App\Http\Controllers;

use App\Enums\UserLevel;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function signUp()
    {
        return view('auth.signUp');
    }

    public function signUpPost(SignUpRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'national_code' => $data['national_code'] ?? null,
            'password' => Hash::make($data['password']),
            'level' => UserLevel::User,
        ]);

        event(new Registered($user));
        $token = auth('api')->login($user);
        $cookie = cookie('token', $token, 120, null, null, true, true);

        return redirect()->route('verification.notice')->withCookie($cookie);
    }

    public function login()
    {
        if ($user = auth('api')->user()) {
            return redirect()->route($user->hasVerifiedEmail() ? 'home' : 'verification.notice');
        }

        return view('auth.login');
    }

    public function loginPost(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || $user->trashed()) {
            return redirect()->back()->withInput()->withErrors([
                'email' => 'ایمیل یا رمزعبور اشتباه می‌باشد',
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember', false);

        $ttlMinutes = $remember ? (30 * 24 * 60) : 120;

        auth('api')->factory()->setTTL($ttlMinutes);

        if (! $token = auth('api')->attempt($credentials)) {
            return redirect()->back()->withInput()->withErrors(['email' => 'ایمیل یا رمز عبور اشتباه است.']);
        }

        $cookie = cookie('token', $token, $ttlMinutes, null, null, true, true);
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->withCookie($cookie);
        }

        return redirect()->route('home')->withCookie($cookie)->with('success', 'ورود موفقیت‌آمیز بود.');
    }

    public function logout(Request $request)
    {
        $token = $request->cookie('token');

        if ($token) {
            try {
                JWTAuth::setToken($token)->invalidate();
            } catch (\Throwable $e) {

            }
        }

        auth('api')->logout();
        return redirect()->route('home')->withCookie(cookie()->forget('token'));
    }
    
    public function verifyNotice()
    {
        $user = auth('api')->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        abort_unless(hash_equals((string) $hash, sha1($user->getEmailForVerification())), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailVerified();
        }

        $token = auth('api')->login($user);
        $cookie = cookie('token', $token, 120, null, null, true, true);
        return redirect()->route('home')->withCookie($cookie);
    }

    public function verifyResend()
    {
        $user = auth('api')->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $user->sendEmailVerificationNotification();
        return back()->with('success', 'ایمیل تایید با موفقیت دوباره ارسال شد');
    }
}
