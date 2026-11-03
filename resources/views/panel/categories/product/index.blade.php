@extends('layout.panel')
@section('title', 'دسته‌بندی محصولات — پنل')

@section('content')
<main class="page-area">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="main-content">
        <section class="intro intro--header">
            <div>
                <p class="kicker">پنل مدیریت</p>
                <h1 class="display-font panel-title--categories">دسته‌بندی محصولات</h1>
            </div>
            @can('permission', 'create-category')
                <a href="{{ route('panel.categories.products.create') }}" class="button button-primary">
                    + ساخت دسته‌بندی جدید
                </a>
            @endcan
        </section>

        <div class="form-card categories-card">
            <div class="section-heading section-heading--flush">
                <div>
                    <h2 class="m-0">لیست دسته‌بندی‌های محصولات</h2>
                    <p class="section-description section-description--flush">{{ $categories->count() }} دسته‌بندی اصلی</p>
                </div>
                <span class="field-hint" style="color: var(--forest); font-weight: 600;">
                    💡 برای تغییر ترتیب، آیکون ⋮⋮ را بگیرید و بالا یا پایین بکشید.
                </span>
            </div>

            @if ($categories->count())
                <div class="categories-list sortable-list" data-reorder-url="{{ route('panel.categories.reorder') }}">
                    @foreach ($categories as $category)
                        <div class="spec-row spec-row--top">
                            <div class="category-icon-wrap" style="width: 100%;">
                                <span class="step-number step-number--category">🛍️</span>
                                @include('panel.categories.partials.category-tree', ['category' => $category])
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🛍️</div>
                    <h3 class="empty-state-title">هیچ دسته‌بندی‌ای وجود ندارد</h3>
                    <p class="color-muted">هنوز هیچ دسته‌بندی‌ای برای محصولات ایجاد نشده است.</p>
                    <a href="{{ route('panel.categories.products.create') }}" class="button button-primary mt-14">
                        + ساخت دسته‌بندی جدید
                    </a>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
