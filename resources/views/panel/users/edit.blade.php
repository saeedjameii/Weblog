@extends('layout.panel')
@section('title', 'ویرایش کاربر — خبرنامه')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel.users.index') }}">← انصراف و بازگشت</a>
@endsection

@section('content')
<main class="page-area">
    <div class="main-content">
        <section class="intro">
            <p class="kicker">مدیریت سیستم</p>
            <h1 class="display-font">ویرایش کاربر: {{ $user->first_name }} {{ $user->last_name }}</h1>
        </section>

        <div class="workspace" style="grid-template-columns: 1fr; max-width: 800px;">
            <form action="{{ route('panel.users.update', $user) }}" method="POST" class="form-card">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="validation-message" style="display:block;margin-bottom:16px;">
                        <ul style="margin:0;padding-inline-start:20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div style="background: #eef4eb; color: var(--forest); padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="field-grid">
                    <div class="field">
                        <label for="first_name">نام</label>
                        <input class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}" required>
                    </div>

                    <div class="field">
                        <label for="last_name">نام خانوادگی</label>
                        <input class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" required>
                    </div>

                    <div class="field">
                        <label for="email">ایمیل</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="field">
                        <label for="phone_number">شماره موبایل</label>
                        <input class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" type="text" value="{{ old('phone_number', $user->phone_number) }}" dir="ltr" required>
                    </div>

                    <div class="field">
                        <label for="national_code">کدملی</label>
                        <input class="form-control @error('national_code') is-invalid @enderror" id="national_code" name="national_code" type="text" value="{{ old('national_code', $user->national_code) }}" dir="ltr" required>
                    </div>

                    <div class="field">
                        @php
                            $jalaliBirthDate = $user->birth_date ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($user->birth_date))->format('Y/m/d') : '';
                        @endphp
                        <label for="birth-date-display">تاریخ تولد</label>
                        <div class="jalali-date-wrap">
                            <input class="form-control jalali-date-input @error('birth_date') is-invalid @enderror"
                                id="birth-date-display" type="text" value="" placeholder="۱۳۷۰/۰۶/۱۲" readonly required aria-haspopup="dialog" aria-expanded="false"/>
                            <button class="jalali-calendar-button" type="button" aria-label="انتخاب تاریخ تولد" data-signup-date-picker>📅</button>
                        </div>
                        <input id="birth_date" name="birth_date" type="hidden" value="{{ old('birth_date', $jalaliBirthDate) }}"/>
                        <div id="signup-jalali-calendar" class="jalali-calendar" role="dialog" aria-label="تقویم شمسی تاریخ تولد" hidden></div>
                    </div>

                    <div class="field full-field" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--line);">
                        <p style="margin-top: 0; color: var(--muted); font-size: 0.9rem;">اگر نمی‌خواهید رمز عبور کاربر را تغییر دهید، فیلدهای زیر را خالی بگذارید.</p>
                    </div>

                    <div class="field">
                        <label for="password">رمز عبور جدید</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" dir="ltr">
                    </div>

                    <div class="field">
                        <label for="password_confirmation">تکرار رمز عبور جدید</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" dir="ltr">
                    </div>
                </div>

                <div class="actions">
                    <button class="button button-primary" type="submit">ذخیره تغییرات</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection