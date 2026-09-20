<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'ToolShare — Borrow more. Buy less.')</title>

@vite(['resources/css/style.css', 'resources/js/create-post.js'])

</head>
<body>
<header class="navbar">
    <nav class="container nav-inner">
        <a class="logo" href="{{ route('home') }}">
            <span class="logo-icon">🔧</span>
            ToolShare
        </a>

        <div class="nav-links">
            <a href="{{ route('home') }}">خانه</a>
            <a href="{{ route('posts.index') }}">مشاهده ابزارها</a>
            <a href="{{ route('categories.index') }}">مشاهده دسته بندی ها</a>
            @yield('page-actions')
        </div>

        <div class="nav-actions">
        @hasSection('header-actions')
            @yield('header-actions')
        @else
            @if (auth('api')->check())
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-secondary" type="submit">خروج</button>
                </form>
                <a class="btn btn-secondary" href="{{ route('panel') }}">پنل من</a>
                <p>
                    👤 {{ auth('api')->user()->first_name }}
                </p>
            @else
                <a class="btn btn-secondary" href="{{ route('login') }}">ورود</a>
                <a class="btn btn-primary" href="{{ route('signUp') }}">ثبت‌ نام</a>
            @endif
        @endif
        </div>
    </nav>
</header>
</body>
</html>
