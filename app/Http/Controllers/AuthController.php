<?php

namespace App\Http\Controllers;

use App\Enums\UserLevel;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Morilog\Jalali\Jalalian;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function signUp()
    {
        return view('auth.signUp');
    }

    public function signUpPost(SignUpRequest $request) 
    {

        try {
            $jalaliDate = Jalalian::fromFormat('Y/m/d', $request->birth_date);

            if ($jalaliDate->format('Y/m/d') !== $request->birth_date) {
                throw new \InvalidArgumentException('Invalid Jalali date after normalization.');
            }

            $birthDate = $jalaliDate->toCarbon()->format('Y-m-d');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->withErrors(['birth_date' => 'تاریخ تولد وارد شده معتبر نیست. لطفاً از فرمت YYYY/MM/DD استفاده کنید (مثلاً 1405/06/12).']);
        }

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'birth_date' => $birthDate,
            'national_code' => $request->national_code,
            'password' => Hash::make($request->password),
            'level' => UserLevel::User,
        ]);

        return redirect()->route('home')->with('success', 'ثبت نام موفقیت‌آمیز بود. لطفاً وارد شوید.');
    }

    public function login()
    {
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

        if (! $token = auth('api')->attempt($credentials)) {
            return redirect()->back()->withInput()->withErrors(['email' => 'ایمیل یا رمز عبور اشتباه است.']);
        }

        return redirect()->route('home')->withCookie(cookie('token', $token, 60))->with('success', 'ورود موفقیت‌آمیز بود.');
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

        return redirect()->route('home')->withCookie(cookie()->forget('token'))->with('success', 'با موفقیت خارج شدید.');
    }
}
