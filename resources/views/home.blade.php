@extends('layout.master')

@section('title', 'چی بهتره؟  — پایگاه جامع اخبار و ویترین تخصصی محصولات')

@section('content')
    <main class="home-main">
        @if (session('success'))
            <div class="container mt-14">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mt-14">
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- سکشن هیرو مدرن با رویکرد دوگانه (اخبار + فروشگاه) --}}
        <section class="hero hero--home">
            <div class="container hero-grid">
                <div class="hero-content">
                    <div class="hero-badge">
                        <span class="hero-badge-dot"></span>
                        <span>پایگاه یکپارچه تحریریه خبری و فروشگاه کالا</span>
                    </div>
                    <h1 class="hero-title">مرجع موثق اخبار روز و فروشگاه مستقیم محصولات باکیفیت</h1>
                    <p class="hero-desc">
                        تازه‌ترین گزارش‌ها و تحلیل‌های دنیای فناوری، اقتصاد و جامعه را دنبال کنید و همزمان کالاهای مورد نیاز
                        خود را با تنوع ویژگی، رنگ و بهترین قیمت بازار سفارش دهید.
                    </p>
                    <div class="hero-buttons">
                        <a class="btn btn-primary" href="{{ route('posts.index') }}">
                            <span> آخرین اخبار و مقالات</span>
                        </a>
                        <a class="btn btn-secondary btn-store" href="{{ route('products.index') }}">
                            <span> مشاهده فروشگاه محصولات</span>
                        </a>
                    </div>

                    {{-- نوار مزایای کلیدی --}}
                    <div class="hero-highlights">
                        <div class="hero-highlight-item">
                            <div>
                                <strong>پوشش زنده اخبار</strong>
                                <small>دقیق، سریع و بی‌طرف</small>
                            </div>
                        </div>
                        <div class="hero-highlight-item">
                            <div>
                                <strong>تنوع قیمت و رنگ</strong>
                                <small>انتخاب با مشخصات دقیق</small>
                            </div>
                        </div>
                        <div class="hero-highlight-item">
                            <div>
                                <strong>تضمین اصالت</strong>
                                <small>کالاهای تست‌شده و معتبر</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hero-image-wrap">
                    <div class="hero-image-card">
                        <div class="hero-image-media">
                            <img src="{{ asset('images/logo.svg') }}" alt="چی بهتره؟" class="hero-img" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- کارت‌های تفکیک دوگانه پلتفرم (اخبار در برابر فروشگاه) --}}
        <section class="section section--gateways">
            <div class="container">
                <div class="gateways-grid">
                    {{-- گیت‌وی اخبار --}}
                    <article class="gateway-card gateway-card--news">
                        <div class="gateway-header">
                            <span class="gateway-tag">تحریریه و مجله</span>
                            <h2 class="gateway-title">تازه‌ترین تحلیل‌ها و اخبار موثق</h2>
                        </div>
                        <p class="gateway-text">
                            تحلیل‌های کارشناسی، گزارش‌های میدانی و رویدادهای فناوری، اقتصادی و فرهنگی را لحظه‌به‌لحظه در
                            سرویس‌های خبری اختصاصی بخوانید.
                        </p>
                        <div class="gateway-actions">
                            <a class="btn btn-secondary" href="{{ route('posts.index') }}">ورود به آرشیو اخبار ←</a>
                            <a class="btn-ghost" href="{{ route('categories.posts.index') }}">دسته‌بندی‌های خبری</a>
                        </div>
                    </article>

                    {{-- گیت‌وی فروشگاه --}}
                    <article class="gateway-card gateway-card--shop">
                        <div class="gateway-header">
                            <span class="gateway-tag gateway-tag--shop">🛍️ ویترین فروشگاه</span>
                            <h2 class="gateway-title">کالاهای برتر با تنوع رنگ و قیمت</h2>
                        </div>
                        <p class="gateway-text">
                            کالاهای دیجیتال، ابزارها و لوازم جانبی را با مقایسه موجودی، کد رنگ هگز و درصد تخفیف ویژه به صورت
                            آنلاین انتخاب کنید.
                        </p>
                        <div class="gateway-actions">
                            <a class="btn btn-primary" href="{{ route('products.index') }}">مشاهده ویترین محصولات ←</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- سکشن محصولات منتخب فروشگاه --}}
        @if (isset($latestProducts) && $latestProducts->isNotEmpty())
            <section id="latest-products" class="section section--products">
                <div class="container">
                    <div class="section-head">
                        <div>
                            <div class="kicker">🛍️ ویترین فروشگاه</div>
                            <h2 class="section-title">جدیدترین محصولات موجود</h2>
                        </div>
                        <a class="btn btn-secondary" href="{{ route('products.index') }}">مشاهده همه محصولات ←</a>
                    </div>

                    <div class="products-grid">
                        @foreach ($latestProducts as $product)
                            <article class="home-product-card">
                                <div class="home-product-thumb-wrap">
                                    @if ($product->images->isNotEmpty())
                                        <img src="{{ Storage::url($product->images->first()->path) }}"
                                            alt="{{ $product->name }}" class="home-product-thumb">
                                    @else
                                        <div class="home-product-thumb home-product-thumb--empty" aria-hidden="true">🛍️
                                        </div>
                                    @endif

                                    @php
                                        $minPrice = $product->prices->sortBy('final_price')->first();
                                    @endphp

                                    @if ($minPrice && $minPrice->discount > 0)
                                        <span class="home-product-discount-badge">{{ (int) $minPrice->discount }}٪
                                            تخفیف</span>
                                    @endif
                                </div>

                                <div class="home-product-body">
                                    <div class="tags-row">
                                        @forelse ($product->categories as $category)
                                            <span class="tag">{{ $category->full_name }}</span>
                                        @empty
                                            <span class="tag">کالای عمومی</span>
                                        @endforelse
                                    </div>

                                    <h3 class="home-product-title">
                                        <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                    </h3>

                                    <p class="home-product-meta">{{ $product->unit }} · وزن: {{ $product->weight }}</p>

                                    <div class="home-product-bottom">
                                        <div class="home-product-price-box">
                                            @if ($minPrice)
                                                <span class="home-product-price-label">شروع قیمت از:</span>
                                                <strong
                                                    class="home-product-price">{{ number_format($minPrice->final_price) }}
                                                    <small>تومان</small></strong>
                                            @else
                                                <span class="home-product-no-price">به زودی</span>
                                            @endif
                                        </div>
                                        <a class="btn btn-secondary btn-sm"
                                            href="{{ route('products.show', $product) }}">مشاهده جزئیات</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- سکشن اخبار و مقالات روز --}}
        <section id="latest-news" class="section sage">
            <div class="container">
                <div class="section-head">
                    <div>
                        <div class="kicker">خبرهای داغ</div>
                        <h2 class="section-title">تازه‌ترین مقالات و اخبار منتشر شده</h2>
                    </div>
                    <a class="btn btn-secondary" href="{{ route('posts.index') }}">آرشیو کامل اخبار ←</a>
                </div>

                <div class="news-grid">
                    @forelse ($latestPosts as $post)
                        <article class="news-card">
                            @if ($post->images->isNotEmpty())
                                <div class="news-thumb-wrap">
                                    <img src="{{ Storage::url($post->images->first()->path) }}" alt="{{ $post->title }}"
                                        class="news-thumb-img">
                                </div>
                            @endif
                            <div class="news-body">
                                <div class="tags-row">
                                    @forelse ($post->categories as $category)
                                        <span class="tag">{{ $category->full_name }}</span>
                                    @empty
                                        <span class="tag">عمومی</span>
                                    @endforelse
                                </div>
                                <h3><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h3>
                                <p class="news-excerpt">{{ Str::limit($post->description, 130) }}</p>
                                <div class="card-bottom">
                                    <span class="author">✍️ {{ $post->user->first_name ?? 'تحریریه' }}</span>
                                    <a class="read-more" href="{{ route('posts.show', $post) }}">ادامه مطلب ←</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            <p>در حال حاضر خبری برای نمایش وجود ندارد.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- نوار اعتماد و ارزش‌های سیستم --}}
        <section class="section section--trust">
            <div class="container">
                <div class="trust-grid">
                    <div class="trust-card">
                        <span class="trust-icon">🎯</span>
                        <h3>رسانه مستقل و دقیق</h3>
                        <p>پوشش بی‌طرفانه و ۲۴ ساعته اخبار با تکیه بر منابع معتبر و رسمی.</p>
                    </div>
                    <div class="trust-card">
                        <span class="trust-icon">📦</span>
                        <h3>تنوع نامحدود ویژگی‌ها</h3>
                        <p>امکان انتخاب کالاها بر اساس کد رنگ هگز، سایز و ویژگی‌های فنی مجزا.</p>
                    </div>
                    <div class="trust-card">
                        <span class="trust-icon">💳</span>
                        <h3>قیمت‌گذاری شفاف</h3>
                        <p>نمایش قیمت نهایی، درصد تخفیف واقعی و موجودی لحظه‌ای هر کالا.</p>
                    </div>
                    <div class="trust-card">
                        <span class="trust-icon">💬</span>
                        <h3>پشتیبانی و همراهی</h3>
                        <p>پاسخگویی سریع به نظرات کاربران و پیگیری مستمر سفارشات فروشگاه.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
