@extends('layout.master')
@section('title', 'فراموشی رمز عبور — چی بهتره؟')

@section('content')
<main class="auth-page">
    <div class="auth-card">
        <a class="logo auth-logo" href="{{ route('home') }}">
            <span class="logo-icon"></span>چی بهتره؟
        </a>
        <h1>بازیابی رمز عبور</h1>
        <p class="auth-subtitle">ایمیل حساب کاربری خود را وارد کنید تا لینک تغییر رمز عبور برای شما ارسال شود.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">ایمیل</label>
                <input class="input" id="email" name="email" type="email" placeholder="you@example.com" value="{{ old('email') }}" required />
                @error('email')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
            <button class="btn btn-primary full" type="submit">ارسال لینک بازیابی</button>
        </form>

        <p class="auth-switch">
            رمز عبور را به خاطر آوردید؟ <a href="{{ route('login') }}">ورود</a>
        </p>
    </div>
</main>
@endsection