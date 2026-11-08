@extends('layout.master')
@section('title', 'تعیین رمز عبور جدید — چی بهتره؟')

@section('content')
<main class="auth-page">
    <div class="auth-card">
        <a class="logo auth-logo" href="{{ route('home') }}">
            <span class="logo-icon"></span>چی بهتره؟
        </a>
        <h1>تعیین رمز عبور جدید</h1>
        <p class="auth-subtitle">رمز عبور جدید و مستحکم خود را وارد نمایید.</p>

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">ایمیل</label>
                <input class="input" id="email" name="email" type="email" value="{{ old('email', $email) }}" required readonly />
            </div>

            <div class="form-group">
                <label for="password">رمز عبور جدید</label>
                <input class="input" id="password" name="password" type="password" placeholder="حداقل ۸ کاراکتر" required />
            </div>

            <div class="form-group">
                <label for="password_confirmation">تکرار رمز عبور جدید</label>
                <input class="input" id="password_confirmation" name="password_confirmation" type="password" placeholder="تکرار رمز عبور" required />
            </div>

            <button class="btn btn-primary full" type="submit">ثبت رمز جدید و ورود</button>
        </form>
    </div>
</main>
@endsection