<?php

namespace App\Http\Controllers;

use App\Enums\UserLevel;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

    public function forgetPassword(){
        return view('auth.forget-password');
    }

    public function sendResetLink(Request $request){
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
                return back()->with('success', 'لینک بازیابی رمز عبور به ایمیل شما ارسال شد.');
        }

        return back()->withErrors(['email' => 'کاربری با این ایمیل یافت نشد']);
    }

    public function resetPassword(Request $request, String $token){
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
            ]);
    }

    public function updatePassword(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password){
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if($status === Password::PASSWORD_RESET){
            return redirect()->route('login')->with('success', 'رمز عبور شما با موفقیت تغییر کرد. اکنون وارد شوید.');
        }

        return back()->withErrors(['email' => 'لینک تغییر رمز منقضی شده یا نامعتبر است.']);
    }
}
