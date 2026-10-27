@extends('layout.panel')

@section('title', 'مدیریت محصولات')
@section('header-actions')@can('permission', 'create-product')<a class="button button-primary" href="{{ route('panel.products.create') }}">محصول جدید</a>@endcan @endsection

@section('content')
<main class="page-area"><div class="main-content">
    <section class="intro intro--header"><div><p class="kicker">مدیریت فروشگاه</p><h1 class="display-font panel-title">محصولات</h1></div>@can('permission', 'create-product')<a class="button button-primary" href="{{ route('panel.products.create') }}">افزودن محصول</a>@endcan</section>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <form class="search-box" method="GET" action="{{ route('panel.products.index') }}">
        <input class="search" type="search" name="search" value="{{ request('search') }}" placeholder="جست‌وجوی محصول">
        <select class="select" name="category_id"><option value="">همهٔ دسته‌بندی‌ها</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->full_name }}</option>@endforeach</select>
        <button class="button button-primary search-submit" type="submit">جست‌وجو</button>
    </form>
    <div class="product-admin-list">
        @forelse ($products as $product)
            <article class="product-admin-card"><div><h2>{{ $product->name }}</h2><p class="color-muted">{{ $product->unit }} · {{ $product->weight }}</p><div class="tags-row">@forelse ($product->categories as $category)<span class="tag">{{ $category->full_name }}</span>@empty<span class="tag">بدون دسته‌بندی</span>@endforelse</div><p>{{ $product->prices->count() }} تنوع فروش</p></div>
                @can('permission', 'delete-product')<form action="{{ route('panel.products.destroy', $product) }}" method="POST" onsubmit="return confirm('محصول حذف شود؟')">@csrf @method('DELETE')<button class="button button-danger" type="submit">حذف محصول</button></form>@endcan
            </article>
        @empty<div class="empty-state"><h2>محصولی ثبت نشده است</h2><p class="color-muted">برای شروع، محصول جدیدی بسازید.</p></div>@endforelse
    </div>
    {{ $products->links('pagination.custom') }}
</div></main>
@endsection
