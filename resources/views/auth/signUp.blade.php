@extends('layout.master')

@section('title', 'Sign Up — ToolShare')

@section('header-actions')
    <a class="btn btn-secondary" href="{{ route('home') }}">خانه</a>
    <a class="btn btn-primary" href="{{ route('login') }}">ورود</a>
@endsection

@section('content')
    <main class="auth-page">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="auth-card">
            <a class="logo auth-logo" href="#"><span class="logo-icon">🔧</span>ToolShare</a>
            <h1>حساب کاربری خود را ایجاد کنید</h1>
            <p class="auth-subtitle">
                به جامعه بپیوندید و شروع به قرض گرفتن یا به اشتراک گذاشتن ابزارها کنید.
            </p>
            <form action="{{ route('signUp.post') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="first_name">نام</label><input class="input" id="first_name" name="first_name"
                        type="text" value="{{ old('first_name') }}" placeholder="سعید" required />
                    <div>
                        @error('first_name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="last_name">نام خانوادگی</label><input class="input" id="last_name" name="last_name"
                        type="text" value="{{ old('last_name') }}" placeholder="جامعی" required />
                    <div>
                        @error('last_name')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="birth-date-display">تاریخ تولد</label><div class="jalali-date-wrap"><input class="input jalali-date-input"
                     id="birth-date-display" type="text" value="" placeholder="۱۴۰۵/۰۶/۱۲" readonly required aria-haspopup="dialog" aria-expanded="false"/><button
                     class="jalali-calendar-button" type="button" aria-label="انتخاب تاریخ تولد" data-signup-date-picker>📅</button></div><input
                     id="birth_date" name="birth_date" type="hidden" value="{{ old('birth_date') }}"/>
                    <div id="signup-jalali-calendar" class="jalali-calendar" role="dialog" aria-label="تقویم شمسی تاریخ تولد" hidden></div>
                    <div>
                        @error('birth_date')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="national_code">کد ملی</label><input class="input" id="national_code" name="national_code"
                        type="text" value="{{ old('national_code') }}" placeholder="1234567890" required />
                    <div>
                        @error('national_code')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone_number">شماره تلفن</label><input class="input" id="phone_number" name="phone_number"
                        type="text" value="{{ old('phone_number') }}" placeholder="09123456789" required />
                    <div>
                        @error('phone_number')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">ایمیل</label><input class="input" id="email" name="email"
                        type="email" value="{{ old('email') }}" placeholder="you@example.com" required />
                    <div>
                        @error('email')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">رمز</label><input class="input" id="password" name="password"
                        type="password" placeholder="رمز عبور" required />
                    <div>
                        @error('password')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">تأیید رمز</label><input class="input"
                        id="password_confirmation" name="password_confirmation" type="password"
                        placeholder="تکرار رمز عبور" required />
                    <div>
                        @error('password_confirmation')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary full" type="submit">
                    ایجاد حساب کاربری
                </button>
            </form>
            <p class="auth-switch">
                قبلاً حساب کاربری ایجاد کرده‌اید؟ <a href="{{ route('login') }}">ورود کنید</a>
            </p>
        </div>
    </main>
@endsection
