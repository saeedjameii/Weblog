@extends('layout.panel')

@section('title', 'پنل مدیریت — خبرنامه')

@section('content')

@php
    $user = auth('api')->user();
@endphp

<main class="page-area">
    <div class="main-content">

        <section class="intro">
            <p class="kicker">پنل مدیریت</p>
            <h1 class="display-font">سلام {{ $user->first_name }} 👋</h1>
            <p class="intro-copy">
                نقش فعلی شما:
                @if ($user->isCreator())
                    <span class="tag">creator</span>
                @else
                    @forelse ($user->roles as $role)
                        <span class="tag">{{ $role->name }}</span>
                    @empty
                        <span class="tag">ادمین</span>
                    @endforelse
                @endif
                — فقط بخش‌هایی که به آن‌ها دسترسی دارید در پایین نمایش داده می‌شود.
            </p>
        </section>

        <div class="dashboard-grid">

            <div class="form-card">
                <h3 class="dashboard-card-title">📦 اخبار من</h3>
                <p class="dashboard-card-description">اخباری که خودتان ثبت کرده‌اید را ببینید، ویرایش یا حذف کنید.</p>
                <a href="{{ route('panel.posts.mine') }}" class="button button-primary dashboard-card-link">مشاهده‌ی پست‌های من</a>
            </div>

            @canany(['update-any-post', 'delete-any-post'])
                <div class="form-card">
                    <h3 class="dashboard-card-title">📦 مدیریت اخبارها</h3>
                    <p class="dashboard-card-description">اخبار را ببینید، ویرایش یا حذف کنید.</p>

                    <div class="dashboard-card-actions">
                        <a href="{{ route('panel.posts.all') }}" class="button button-primary dashboard-card-link">لیست اخبار</a>
                    </div>
                </div>
            @endcanany

            @can('permission', 'create-post')
                <div class="form-card">
                    <h3 class="dashboard-card-title">➕ ثبت خبر جدید</h3>
                    <p class="dashboard-card-description">یک خبر تازه برای اجاره در سایت ثبت کنید.</p>
                    <a href="{{ route('panel.posts.create') }}" class="button button-primary dashboard-card-link">ثبت خبر</a>
                </div>
            @endcan

            @canany(['create-category', 'update-category', 'delete-category'])
                <div class="form-card">
                    <h3 class="dashboard-card-title">📁 مدیریت دسته‌بندی‌ها</h3>
                    <p class="dashboard-card-description">دسته‌بندی و زیردسته‌بندی اخبار را بسازید یا ویرایش کنید.</p>

                    <div class="dashboard-card-actions">
                        <a href="{{ route('panel.categories.index') }}" class="btn btn-secondary dashboard-card-action">لیست دسته‌بندی‌ها</a>

                        @can('permission', 'create-category')
                            <a href="{{ route('panel.categories.create') }}" class="btn btn-secondary dashboard-card-action">+ جدید</a>
                        @endcan
                    </div>
                </div>
            @endcanany

            @canany(['create-role', 'update-role', 'delete-role'])
                <div class="form-card">
                    <h3 class="dashboard-card-title">🛡️ مدیریت نقش‌ها</h3>
                    <p class="dashboard-card-description">نقش‌ها و اختیارات (permission) هر نقش را تعریف کنید.</p>

                    <div class="dashboard-card-actions">
                        <a href="{{ route('panel.roles.index') }}" class="btn btn-secondary dashboard-card-action">لیست نقش‌ها</a>

                        @can('permission', 'create-role')
                            <a href="{{ route('panel.roles.create') }}" class="btn btn-secondary dashboard-card-action">+ نقش جدید</a>
                        @endcan
                    </div>
                </div>
            @endcanany

            @canany(['assign-role', 'manage-users'])
                <div class="form-card">
                    <h3 class="dashboard-card-title">👥 مدیریت کاربران</h3>
                    <p class="dashboard-card-description">به کاربران نقش بدهید یا حساب‌های کاربری را مدیریت کنید.</p>
                    <a href="{{ route('panel.users.index') }}" class="button button-primary dashboard-card-link">مدیریت کاربران</a>
                </div>
            @endcanany

            @can('permission', 'delete-any-post')
                <div class="form-card">
                    <h3 class="dashboard-card-title">پست های حذف شده</h3>
                    <p class="dashboard-card-description">پست های درون سطل زباله را ببینید و در صورت نیاز آن ها را بازیابی کنید</p>
                    <a href="{{ route('panel.posts.trashed') }}" class="button button-primary dashboard-card-link">مدیریت پست های حذف شده</a>
                </div>
            @endcan

            @canany(['create-product', 'update-product', 'delete-product'])
                <div class="form-card">
                    <h3 class="dashboard-card-title"> مدیریت محصولات</h3>
                    <p class="dashboard-card-description">محصولات مورد نیاز را ایجاد کنید یا آن ها را ویرایش و حذف کنید.</p>
                    <a href="{{ route('panel.products.index') }}" class="button button-primary dashboard-card-link">مدیریت محصولات</a>
                </div>
            @endcanany

            @canany(['create-attribute', 'update-attribute', 'delete-attribute'])
                <div class="form-card">
                    <h3 class="dashboard-card-title"> مدیریت ویژگی های محصولات</h3>
                    <p class="dashboard-card-description"> ویژگی محصولات مورد نیاز را ایجاد کنید یا آن ها را ویرایش و حذف کنید.</p>
                    <a href="{{ route('panel.attributes.index') }}" class="button button-primary dashboard-card-link">مدیریت ویژگی های محصولات</a>
                </div>
            @endcanany

        </div>

    </div>
</main>

@endsection