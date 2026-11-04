@extends('layout.master')

@section('title', 'محصولات')

@section('content')
@php
    $activeCategory = request()->filled('category_id') ? collect($categories)->firstWhere('id', (int) request('category_id')) : null;
    $hasFilters = request()->filled('search') || request()->filled('category_id');
@endphp
<main class="product-catalog">
    <div class="container">
        <ol class="breadcrumb" aria-label="مسیر صفحه">
            <li><a href="{{ route('home') }}">صفحه اصلی</a></li>
            <li aria-current="page">محصولات</li>
        </ol>

        <section class="sx-pagehead sx-pagehead--shop glass">
            <span class="sx-kicker sx-kicker--shop"><x-icon name="shop" /> فروشگاه</span>
            <h1 class="sx-title">محصولات</h1>
            <p class="sx-lead">محصول مورد نظرتان را جست‌وجو کنید یا از میان دسته‌بندی‌ها انتخاب کنید.</p>
        </section>

        <div class="shop-layout">
            {{-- سایدبار فیلتر (دسکتاپ) / Bottom Sheet (موبایل). فقط فیلترهای واقعی: search و category_id --}}
            <aside id="product-filters" class="filter-panel glass" data-sheet aria-label="فیلتر محصولات">
                <div class="filter-panel__head">
                    <h2>فیلترها</h2>
                    <button type="button" class="btn-soft filter-close-btn" data-sheet-close aria-label="بستن فیلترها"><x-icon name="close" /></button>
                </div>

                <form  method="GET" action="{{ route('products.index') }}">
                    <label class="filter-field">
                        <span>جست‌وجو</span>
                        <input class="input" type="search" name="search" value="{{ request('search') }}" placeholder="نام یا توضیحات محصول...">
                    </label>
                    <label class="filter-field">
                        <span>دسته‌بندی</span>
                        <select class="input" name="category_id">
                            <option value="">همهٔ دسته‌بندی‌ها</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->full_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="btn-glow" type="submit" style="inline-size:100%"><x-icon name="search" /> اعمال فیلتر</button>
                    @if ($hasFilters)
                        <a class="btn-soft" style="inline-size:100%;margin-block-start:.5rem" href="{{ route('products.index') }}">پاک‌کردن فیلترها</a>
                    @endif
                </form>
            </aside>
            <div class="sheet-backdrop" data-sheet-backdrop></div>

            <section class="shop-content" aria-label="فهرست محصولات">
                <div class="toolbar glass">
                    <div class="chips">
                        <span class="toolbar__count">{{ $products->total() }} محصول</span>
                        @if (request()->filled('search'))
                            <a class="chip" href="{{ route('products.index', request()->except('search', 'page')) }}">«{{ request('search') }}» <x-icon name="close" /></a>
                        @endif
                        @if ($activeCategory)
                            <a class="chip" href="{{ route('products.index', request()->except('category_id', 'page')) }}">{{ $activeCategory->full_name }} <x-icon name="close" /></a>
                        @endif
                    </div>
                    <button type="button" class="btn-soft filter-open-btn" data-sheet-open aria-controls="product-filters" aria-expanded="false">
                        <x-icon name="filter" /> فیلترها
                    </button>
                </div>

                <div class="pgrid">
                    @forelse ($products as $product)
                        <x-product-card :product="$product" />
                    @empty
                        <div class="sx-empty glass">
                            <x-icon name="empty" />
                            <h2>محصولی پیدا نشد</h2>
                            <p>عبارت جست‌وجو یا دسته‌بندی را تغییر دهید و دوباره تلاش کنید.</p>
                            @if ($hasFilters)
                                <a class="btn-glow" href="{{ route('products.index') }}">نمایش همهٔ محصولات</a>
                            @endif
                        </div>
                    @endforelse
                </div>

                {{ $products->links('pagination.custom') }}
            </section>
        </div>
    </div>
</main>
@endsection
