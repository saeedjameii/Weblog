<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'پنل مدیریت — چی بهتره؟ ')</title>

    @vite(['resources/css/style.css', 'resources/js/app.js', 'resources/js/create-post.js', 'resources/js/product-create.js', 'resources/js/category-sortable.js'])
</head>

<body>
    <header class="navbar navbar--panel">
        <nav class="container nav-inner">
            <a class="logo" href="{{ route('panel') }}">
                <span class="logo-icon">🛠️</span>
                <span class="logo-text">پنل مدیریت</span>
            </a>

            {{-- Desktop & Mobile Clean Actions --}}
            <div class="nav-actions">
                @hasSection('header-actions')
                    @yield('header-actions')
                @endif

                @if (auth('api')->check())
                    <span class="nav-user-greeting">👤 {{ auth('api')->user()->first_name }}</span>
                @endif
                <a class="btn btn-secondary btn-sm nav-desktop-only" href="{{ route('home') }}">بازگشت به سایت</a>

                {{-- Universal Hamburger Menu Toggle (Desktop & Mobile) --}}
                <button type="button" class="menu-toggle-btn" aria-label="باز کردن منو" aria-expanded="false"
                    data-mobile-menu-toggle>
                    <span class="hamburger-box">
                        <span class="hamburger-bar"></span>
                        <span class="hamburger-bar"></span>
                        <span class="hamburger-bar"></span>
                    </span>
                    <span class="menu-toggle-label">منوی پنل</span>
                </button>
            </div>
        </nav>
    </header>

    {{-- Mobile Navigation Drawer & Backdrop for Panel --}}
    <div class="mobile-nav-backdrop" data-mobile-menu-backdrop></div>
    <aside class="mobile-nav-drawer" data-mobile-menu-drawer aria-hidden="true">
        <div class="mobile-nav-header">
            <a class="logo" href="{{ route('panel') }}">
                <span class="logo-icon">🛠️</span>
                <span class="logo-text">پنل مدیریت</span>
            </a>
            <button type="button" class="mobile-nav-close" aria-label="بستن منو" data-mobile-menu-close>✕</button>
        </div>

        <div class="mobile-nav-body">
            <div class="mobile-user-card">
                <div class="mobile-user-info">
                    <span class="mobile-user-avatar">👤</span>
                    <div>
                        <strong class="mobile-user-name">{{ auth('api')->user()?->first_name ?? 'کاربر' }}
                            {{ auth('api')->user()?->last_name ?? 'سیستم' }}</strong>
                        <span
                            class="mobile-user-sub">{{ auth('api')->user()?->phone_number ?? 'مدیر سیستم / کاربر مجاز' }}</span>
                    </div>
                </div>
                <div class="mobile-user-actions">
                    <a class="btn btn-secondary btn-block" href="{{ route('home') }}">🌐 بازگشت به سایت اصلی</a>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button class="btn btn-secondary btn-block" type="submit">خروج از حساب</button>
                    </form>
                </div>
            </div>

            {{-- منوی عمومی پنل --}}
            <div class="mobile-nav-section">
                <div class="mobile-nav-section-title">
                    <span>📊 میز کار</span>
                </div>
                <div class="mobile-nav-links-list">
                    <a href="{{ route('panel') }}"
                        class="mobile-nav-item {{ request()->routeIs('panel') ? 'is-active' : '' }}">
                        <span class="item-icon">📈</span>
                        <span>داشبورد مدیریت</span>
                    </a>
                </div>
            </div>

            {{-- بخش اخبار در پنل --}}
            <div class="mobile-nav-section">
                <div class="mobile-nav-section-title">
                    <span>مدیریت اخبار و وبلاگ</span>
                </div>
                <div class="mobile-nav-links-list">
                    <a href="{{ route('panel.posts.mine') }}"
                        class="mobile-nav-item {{ request()->routeIs('panel.posts.mine') ? 'is-active' : '' }}">
                        <span class="item-icon">✍️</span>
                        <span>اخبار من</span>
                    </a>
                    @canany(['update-any-post', 'delete-any-post'])
                        <a href="{{ route('panel.posts.all') }}"
                            class="mobile-nav-item {{ request()->routeIs('panel.posts.all') ? 'is-active' : '' }}">
                            <span class="item-icon">📋</span>
                            <span>مدیریت همه اخبار</span>
                        </a>
                    @endcanany
                    @canany(['create-category', 'update-category', 'delete-category'])
                        <a href="{{ route('panel.categories.posts.index') }}"
                            class="mobile-nav-item {{ request()->routeIs('panel.categories.posts.*') ? 'is-active' : '' }}">
                            <span class="item-icon">📁</span>
                            <span>دسته‌بندی پست‌ها</span>
                        </a>
                    @endcanany
                </div>
            </div>

            {{-- بخش فروشگاه در پنل --}}
            <div class="mobile-nav-section">
                <div class="mobile-nav-section-title">
                    <span>🛍️ مدیریت فروشگاه</span>
                </div>
                <div class="mobile-nav-links-list">
                    @canany(['create-product', 'update-product', 'delete-product'])
                        <a href="{{ route('panel.products.index') }}"
                            class="mobile-nav-item {{ request()->routeIs('panel.products.*') ? 'is-active' : '' }}">
                            <span class="item-icon">📦</span>
                            <span>محصولات و تنوع قیمت‌ها</span>
                        </a>
                    @endcanany
                    @canany(['create-category', 'update-category', 'delete-category'])
                        <a href="{{ route('panel.categories.products.index') }}"
                            class="mobile-nav-item {{ request()->routeIs('panel.categories.products.*') ? 'is-active' : '' }}">
                            <span class="item-icon">🛍️</span>
                            <span>دسته‌بندی محصولات</span>
                        </a>
                    @endcanany
                    @canany(['create-attribute', 'update-attribute', 'delete-attribute'])
                        <a href="{{ route('panel.attributes.index') }}"
                            class="mobile-nav-item {{ request()->routeIs('panel.attributes.*') ? 'is-active' : '' }}">
                            <span class="item-icon">🏷️</span>
                            <span>ویژگی‌ها و مشخصات کالا</span>
                        </a>
                    @endcanany
                    @canany(['update-product', 'delete-product'])
                        <a href="{{ route('panel.products.trashed') }}"
                            class="mobile-nav-item {{ request()->routeIs('panel.products.trashed') ? 'is-active' : '' }}">
                            <span class="item-icon">📦</span>
                            <span>محصولات حذف شده</span>
                        </a>
                    @endcanany
                </div>
            </div>

            {{-- بخش تنظیمات سیستم --}}
            <div class="mobile-nav-section">
                <div class="mobile-nav-section-title">
                    <span>⚙️ تنظیمات و دسترسی‌ها</span>
                </div>
                <div class="mobile-nav-links-list">
                    @canany(['create-role', 'update-role', 'delete-role'])
                        <a href="{{ route('panel.roles.index') }}"
                            class="mobile-nav-item {{ request()->routeIs('panel.roles.*') ? 'is-active' : '' }}">
                            <span class="item-icon">🛡️</span>
                            <span>نقش‌ها و اختیارات</span>
                        </a>
                    @endcanany
                    @canany(['assign-role', 'manage-users'])
                        <a href="{{ route('panel.users.index') }}"
                            class="mobile-nav-item {{ request()->routeIs('panel.users.*') ? 'is-active' : '' }}">
                            <span class="item-icon">👥</span>
                            <span>مدیریت کاربران</span>
                        </a>
                    @endcanany
                </div>
            </div>
        </div>

        <div class="mobile-nav-footer">
            <span class="mobile-nav-hint">⌨️ برای بستن منو کلید <kbd>Esc</kbd> را بزنید</span>
        </div>
    </aside>
