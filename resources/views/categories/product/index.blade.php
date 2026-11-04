@extends('layout.master')

@section('title', 'دسته‌بندی محصولات')

@section('content')

<main>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success flash-wrap" role="status" data-auto-dismiss="6000">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger flash-wrap" role="alert">{{ session('error') }}</div>
        @endif

        <ol class="breadcrumb" aria-label="مسیر صفحه">
            <li><a href="{{ route('home') }}">صفحه اصلی</a></li>
            <li aria-current="page">دسته‌بندی محصولات</li>
        </ol>

        {{-- Page Header --}}
        <section class="sx-pagehead sx-pagehead--shop glass" style="display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between;">
            <div>
                <span class="sx-kicker sx-kicker--shop"><x-icon name="shop" /> دسته‌بندی</span>
                <h1 class="sx-title">دسته‌بندی محصولات</h1>
            </div>
            @can('permission', 'create-category')
                <a href="{{ route('panel.categories.products.create') }}" class="btn-glow">
                    + ساخت دسته‌بندی جدید
                </a>
            @endcan
        </section>

        {{-- Categories Card --}}
        <div class="sx-cat-card glass-strong" style="margin-block-start: var(--space);">

            <div class="sx-head">
                <div>
                    <h2 class="sx-title" style="font-size:1.15rem">لیست دسته‌بندی‌های محصولات</h2>
                    <p class="sx-lead">{{ $categories->count() }} دسته‌بندی‌ها</p>
                </div>
            </div>

            @if ($categories->count())

                <div class="categories-list">

                    @foreach ($categories as $category)

                        <div class="sx-cat-row">
                            @include('categories.partials.product-category-tree', [
                                'category' => $category
                            ])
                        </div>

                    @endforeach

                </div>

            @else

                {{-- Empty State --}}
                <div class="sx-empty">
                    <x-icon name="folder" />
                    <h3>هیچ دسته‌بندی‌ای وجود ندارد</h3>
                    <p>هنوز هیچ دسته‌بندی‌ای ایجاد نشده است.</p>
                </div>

            @endif

        </div>

    </div>
</main>

@endsection
