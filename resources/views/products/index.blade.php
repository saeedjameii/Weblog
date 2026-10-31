@extends('layout.master')

@section('title', 'محصولات')

@section('content')
<main class="product-catalog">
    <section class="product-catalog-hero">
        <div class="container">
            <p class="kicker">فروشگاه</p>
            <h1 class="section-title">محصولات</h1>
            <p class="product-catalog-intro">محصول مورد نظرتان را جست‌وجو کنید یا از میان دسته‌بندی‌ها انتخاب کنید.</p>

            <form class="product-search" method="GET" action="{{ route('products.index') }}">
                <label class="product-search-field">
                    <span class="sr-only">جست‌وجوی محصولات</span>
                    <input class="input" type="search" name="search" value="{{ request('search') }}" placeholder="نام یا توضیحات محصول...">
                </label>
                <label class="product-search-field">
                    <span class="sr-only">دسته‌بندی</span>
                    <select class="input" name="category_id">
                        <option value="">همهٔ دسته‌بندی‌ها</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->full_name }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="btn btn-primary" type="submit">جست‌وجو</button>
                @if (request()->filled('search') || request()->filled('category_id'))
                    <a class="product-search-reset" href="{{ route('products.index') }}">پاک‌کردن فیلترها</a>
                @endif
            </form>
        </div>
    </section>

    <section class="container product-catalog-content" aria-label="فهرست محصولات">
        <div class="product-results-heading">
            <h2>فهرست محصولات</h2>
            <span>{{ $products->total() }} محصول</span>
        </div>

        <div class="product-grid">
            @forelse ($products as $product)
                @php($startingPrice = $product->prices->min('final_price'))
                <article class="product-card">
                    <a class="product-card-media" href="{{ route('products.show', $product) }}" aria-label="مشاهدهٔ {{ $product->name }}">
                        @if ($product->images->isNotEmpty())
                            <img src="{{ Storage::url($product->images->first()->path) }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <span class="product-card-placeholder" aria-hidden="true">▧</span>
                        @endif
                        @if ($product->categories->isNotEmpty())
                            <span class="product-card-category">{{ $product->categories->first()->full_name }}</span>
                        @endif
                    </a>
                    <div class="product-card-body">
                        <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
                        <p>{{ \Illuminate\Support\Str::limit($product->description, 105) }}</p>
                        <div class="product-card-bottom">
                            <div class="product-card-price">
                                <span>شروع از</span>
                                <strong>{{ $startingPrice !== null ? number_format((float) $startingPrice) : '—' }}</strong>
                            </div>
                            <a class="product-card-link" href="{{ route('products.show', $product) }}">مشاهدهٔ محصول <span aria-hidden="true">←</span></a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="product-empty">
                    <span aria-hidden="true">⌕</span>
                    <h2>محصولی پیدا نشد</h2>
                    <p>عبارت جست‌وجو یا دسته‌بندی را تغییر دهید و دوباره تلاش کنید.</p>
                    @if (request()->filled('search') || request()->filled('category_id'))
                        <a class="btn btn-secondary" href="{{ route('products.index') }}">نمایش همهٔ محصولات</a>
                    @endif
                </div>
            @endforelse
        </div>

        {{ $products->links('pagination.custom') }}
    </section>
</main>
@endsection
