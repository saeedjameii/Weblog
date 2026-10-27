@extends('layout.panel')

@section('title', 'مدیریت ویژگی‌ها')
@section('header-actions')@can('permission', 'create-attribute')<a class="button button-primary" href="{{ route('panel.attributes.create') }}">ویژگی جدید</a>@endcan @endsection

@section('content')
<main class="page-area"><div class="main-content">
    <section class="intro intro--header"><div><p class="kicker">مدیریت فروشگاه</p><h1 class="display-font panel-title">ویژگی‌های محصول</h1></div>@can('permission', 'create-attribute')<a class="button button-primary" href="{{ route('panel.attributes.create') }}">افزودن ویژگی</a>@endcan</section>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="attribute-admin-list">
        @forelse ($attributes as $attribute)
            <article class="attribute-admin-card">
                <div class="attribute-admin-heading"><div><h2>{{ $attribute->name }}</h2><p class="color-muted">نوع: {{ $attribute->type === 'color' ? 'رنگ' : 'متنی' }}</p></div>@can('permission', 'update-attribute')<a class="button button-secondary" href="{{ route('panel.attributes.edit', $attribute) }}">مدیریت ویژگی و مقدارها</a>@endcan</div>
                <div class="attribute-value-list">
                    @forelse ($attribute->values as $value)<span class="tag">{{ $value->value }} @if ($attribute->type === 'color' && $value->color_code)<code>{{ $value->color_code }}</code>@endif @if ($value->prices_count) · استفاده‌شده در {{ $value->prices_count }} تنوع @endif</span>
                    @empty<span class="color-muted">هنوز مقداری ثبت نشده است.</span>@endforelse
                </div>
                @can('permission', 'delete-attribute')<form class="attribute-delete-form" action="{{ route('panel.attributes.destroy', $attribute) }}" method="POST" onsubmit="return confirm('ویژگی و مقدارهای استفاده‌نشدهٔ آن حذف شوند؟')">@csrf @method('DELETE')<button class="button button-danger" type="submit">حذف ویژگی</button></form>@endcan
            </article>
        @empty<div class="empty-state"><h2>ویژگی‌ای ثبت نشده است</h2><p class="color-muted">ابتدا ویژگی‌هایی مانند رنگ یا حافظه را ایجاد کنید.</p></div>@endforelse
    </div>
</div></main>
@endsection
