@extends('layout.master')
@section('title', 'Login — ToolShare')

@section('header-actions')
    <a class="btn btn-secondary" href="{{ route('home') }}">خانه</a>
    <a class="btn btn-primary" href="{{ route('signUp') }}">ثبت نام</a>
@endsection

@section('content')
    <main class="auth-page">
      <div class="auth-card">
        <a class="logo auth-logo" href="{{ route('home') }}"
          ><span class="logo-icon">🔧</span>ToolShare</a
        >
        <h1>خوش آمدید</h1>
        <p class="auth-subtitle">
           وارد شوید تا شروع به قرض گرفتن یا به اشتراک گذاشتن ابزارها کنید.
        </p>
        @if (session()->has('error'))
            <div class="text-danger">{{ session('error') }}</div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('login.post') }}" method="post">
            @csrf
          <div class="form-group">
            <label for="email">ایمیل</label
            ><input
              class="input"
              id="email"
              name="email"
              type="email"
              placeholder="you@example.com"
              value="{{ old('email') }}"
              required
            />
            @error('email')
                <p class="text-danger">{{ $message }}</p>
            @enderror
          </div>
          <div class="form-group">
            <div class="password-row">
              <label for="password">رمز عبور</label
              ><a class="small-link" href="#">رمز عبور را فراموش کرده‌اید؟</a>
            </div>
            <input
              class="input"
              id="password"
              name="password"
              type="password"
              placeholder="رمز عبور"
              required
            />
            @error('password')
                <p class="text-danger">{{ $message }}</p>
            @enderror
          </div>
          <label class="check-row"
            ><input type="checkbox" name="remember" /> مرا به یاد داشته باش</label
          ><button class="btn btn-primary full" type="submit">ورود</button>
        </form>
        <p class="auth-switch">
         حساب کاربری ندارید؟  <a href="{{ route('signUp') }}">ثبت نام</a>
        </p>
      </div>
    </main>

@endsection