<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'پنل مدیریت — خبرنامه')</title>
    @vite(['resources/css/style.css'])
</head>
<body>
<header class="navbar">
    <nav class="container nav-inner">
        <a class="logo" href="{{ route('panel') }}">
            <span class="logo-icon">🛠️</span>
            پنل مدیریت
        </a>

        <div class="nav-links">
            <a href="{{ route('panel') }}">داشبورد</a>
            <a href="{{ route('panel.posts.mine') }}">اخبار من</a>

            @canany(['create-category', 'update-category', 'delete-category'])
                <a href="{{ route('panel.categories.index') }}">دسته‌بندی‌ها</a>
            @endcanany

            @canany(['create-role', 'update-role', 'delete-role'])
                <a href="{{ route('panel.roles.index') }}">نقش‌ها</a>
            @endcanany

            @canany(['assign-role', 'manage-users'])
                <a href="{{ route('panel.users.index') }}">کاربران</a>
            @endcanany

            @yield('page-actions')
        </div>

        <div class="nav-actions">
            <a class="btn btn-secondary" href="{{ route('home') }}">بازگشت به سایت</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-secondary" type="submit">خروج</button>
            </form>
            <p>👤 {{ auth('api')->user()->first_name }}</p>
        </div>
    </nav>
</header>
</body>
</html>