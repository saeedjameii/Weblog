@extends('layout.master')

@section('title', 'تایید ایمیل — خبرنامه')

@section('content')
<main class="auth-page">
    <div class="auth-card">
        <a class="logo auth-logo" href="{{ route('home') }}">
            <span class="logo-icon">📧</span>خبرنامه
        </a>
        <h1>تایید نشانی ایمیل</h1>
        <p class="auth-subtitle">
            از ثبت‌نام شما سپاسگزاریم! لطفاً ایمیل خود را بررسی کرده و روی پیوند تایید ارسال‌شده کلیک کنید.
        </p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button class="btn btn-primary full" type="submit">ارسال مجدد ایمیل تایید</button>
        </form>

        <form action="{{ route('logout') }}" method="POST" style="margin-top: 15px;">
            @csrf
            <button class="btn btn-secondary full" type="submit">خروج از حساب</button>
        </form>
    </div>
</main>
@endsection