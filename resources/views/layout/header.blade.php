<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>@yield('title', 'خبرنامه و فروشگاه — تازه‌ترین اخبار و محصولات')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script>document.documentElement.classList.add('js');</script>

    @vite(['resources/css/style.css', 'resources/css/theme.css', 'resources/js/app.js', 'resources/js/create-post.js', 'resources/js/site-ui.js'])
</head>

<body class="site-theme">
    <header class="navbar site-header-glass">
        <nav class="container nav-inner" aria-label="ناوبری اصلی">
            <a class="logo" href="{{ route('home') }}">
                <span class="logo-icon"><x-icon name="news" /></span>
                <span class="logo-text">خبرنامه <span class="logo-badge">و فروشگاه</span></span>
            </a>

            <div class="sx-nav-links">
                <a class="sx-nav-link {{ request()->routeIs('posts.*') ? 'is-active' : '' }}" href="{{ route('posts.index') }}">اخبار</a>
                <a class="sx-nav-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}" href="{{ route('products.index') }}">فروشگاه</a>
            </div>

            {{-- Clean Minimal Nav Actions with Universal Hamburger Menu --}}
            <div class="nav-actions">
                @hasSection('header-actions')
                    @yield('header-actions')
                @else
                    @if (auth('api')->check())
                        <span class="nav-user-greeting"><x-icon name="user" /> {{ auth('api')->user()->first_name }}</span>
                        @can('access-panel')
                            <a class="btn btn-secondary btn-sm nav-desktop-only" href="{{ route('panel') }}">پنل کاربری</a>
                        @endcan
                    @else
                        <a class="btn btn-secondary btn-sm nav-desktop-only" href="{{ route('login') }}">ورود</a>
                        <a class="btn btn-primary btn-sm nav-desktop-only" href="{{ route('signUp') }}">عضویت</a>
                    @endif
                @endif

                {{-- Universal Hamburger Menu Toggle (Desktop & Mobile) --}}
                <button type="button" class="menu-toggle-btn" aria-label="باز کردن منو" aria-expanded="false"
                    data-mobile-menu-toggle>
                    <span class="hamburger-box">
                        <span class="hamburger-bar"></span>
                        <span class="hamburger-bar"></span>
                        <span class="hamburger-bar"></span>
                    </span>
                    <span class="menu-toggle-label">منو</span>
                </button>
            </div>
        </nav>
    </header>

    {{-- Navigation Drawer & Backdrop (Universal Desktop & Mobile) - باید بیرون از <header> بماند --}}
    <div class="mobile-nav-backdrop" data-mobile-menu-backdrop></div>
    <aside class="mobile-nav-drawer" data-mobile-menu-drawer aria-hidden="true">
        <div class="mobile-nav-header">
            <a class="logo" href="{{ route('home') }}">
                <span class="logo-icon"><x-icon name="news" /></span>
                <span class="logo-text">خبرنامه و فروشگاه</span>
            </a>
            <button type="button" class="mobile-nav-close" aria-label="بستن منو" data-mobile-menu-close><x-icon name="close" /></button>
        </div>

        <div class="mobile-nav-body">
            {{-- وضعیت کاربر --}}
            <div class="mobile-user-card">
                @if (auth('api')->check())
                    <div class="mobile-user-info">
                        <span class="mobile-user-avatar"><x-icon name="user" /></span>
                        <div>
                            <strong class="mobile-user-name">{{ auth('api')->user()->first_name }}
                                {{ auth('api')->user()->last_name }}</strong>
                            <span class="mobile-user-sub">{{ auth('api')->user()->phone_number ?? auth('api')->user()->email }}</span>
                        </div>
                    </div>
                    <div class="mobile-user-actions">
                        @can('access-panel')
                            <a class="btn btn-primary btn-block" href="{{ route('panel') }}">ورود به پنل مدیریت</a>
                        @endcan
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button class="btn btn-secondary btn-block" type="submit">خروج از حساب</button>
                        </form>
                    </div>
                @else
                    <p class="mobile-guest-text">به خبرنامه و فروشگاه خوش آمدید!</p>
                    <div class="mobile-guest-actions">
                        <a class="btn btn-primary btn-block" href="{{ route('login') }}">ورود به حساب کاربری</a>
                        <a class="btn btn-secondary btn-block" href="{{ route('signUp') }}">ثبت‌نام حساب جدید</a>
                    </div>
                @endif
            </div>

            {{-- بخش ۱: اخبار و مجله --}}
            <div class="mobile-nav-section">
                <div class="mobile-nav-section-title">
                    <span>بخش اخبار و وبلاگ</span>
                </div>
                <div class="mobile-nav-links-list">
                    <a href="{{ route('home') }}"
                        class="mobile-nav-item {{ request()->routeIs('home') ? 'is-active' : '' }}">
                        <span class="item-icon"><x-icon name="home" /></span>
                        <span>صفحه اصلی</span>
                    </a>
                    <a href="{{ route('posts.index') }}"
                        class="mobile-nav-item {{ request()->routeIs('posts.*') ? 'is-active' : '' }}">
                        <span class="item-icon"><x-icon name="news" /></span>
                        <span>تازه‌ترین اخبار و مقالات</span>
                    </a>
                    <a href="{{ route('categories.posts.index') }}"
                        class="mobile-nav-item {{ request()->routeIs('categories.*') ? 'is-active' : '' }}">
                        <span class="item-icon"><x-icon name="folder" /></span>
                        <span>دسته‌بندی‌های خبری</span>
                    </a>
                </div>
            </div>

            {{-- بخش ۲: فروشگاه و محصولات --}}
            <div class="mobile-nav-section">
                <div class="mobile-nav-section-title">
                    <span>بخش فروشگاه و کالاها</span>
                </div>
                <div class="mobile-nav-links-list">
                    <a href="{{ route('products.index') }}"
                        class="mobile-nav-item {{ request()->routeIs('products.*') ? 'is-active' : '' }}">
                        <span class="item-icon"><x-icon name="shop" /></span>
                        <span>ویترین محصولات فروشگاه</span>
                    </a>
                    <a href="{{ route('categories.products.index') }}"
                        class="mobile-nav-item {{ request()->routeIs('categories.*') ? 'is-active' : '' }}">
                        <span class="item-icon"><x-icon name="folder" /></span>
                        <span>دسته‌بندی‌های محصولات</span>
                    </a>
                </div>
            </div>

            {{-- بخش ۳: میانبرهای پنل برای کاربران مجاز --}}
            @can('access-panel')
                <div class="mobile-nav-section">
                    <div class="mobile-nav-section-title">
                        <span>میانبرهای پنل مدیریت</span>
                    </div>
                    <div class="mobile-nav-links-list">
                        <a href="{{ route('panel') }}" class="mobile-nav-item">
                            <span class="item-icon"><x-icon name="chart" /></span>
                            <span>داشبورد پنل مدیریت</span>
                        </a>
                        <a href="{{ route('panel.posts.mine') }}" class="mobile-nav-item">
                            <span class="item-icon"><x-icon name="pen" /></span>
                            <span>اخبار و نوشته‌های من</span>
                        </a>
                        <a href="{{ route('panel.products.index') }}" class="mobile-nav-item">
                            <span class="item-icon"><x-icon name="box" /></span>
                            <span>مدیریت کالاهای فروشگاه</span>
                        </a>
                    </div>
                </div>
            @endcan
        </div>

        <div class="mobile-nav-footer">
            <span class="mobile-nav-hint">برای بستن منو کلید <kbd>Esc</kbd> را بزنید</span>
        </div>
    </aside>

