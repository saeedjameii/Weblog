@extends('layout.panel')

@section('title', 'ویرایش ویژگی')
@section('header-actions')<a class="browse-link" href="{{ route('panel.attributes.index') }}">بازگشت به ویژگی‌ها</a>@endsection

@section('content')
<main class="page-area"><div class="main-content">
    <section class="intro"><p class="kicker">مدیریت فروشگاه</p><h1 class="display-font">مدیریت ویژگی: {{ $attribute->name }}</h1><p class="intro-copy">مقدارهای موجود را ویرایش کنید یا مقدار تازه‌ای برای این ویژگی بسازید.</p></section>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger"><ul class="errors-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="workspace workspace--single">
        <form action="{{ route('panel.attributes.update', $attribute) }}" method="POST" class="form-card">@csrf @method('PUT')
            <h2>اطلاعات ویژگی</h2><div class="field-grid">
                <div class="field"><label for="attribute-name">نام</label><input class="form-control" id="attribute-name" name="name" maxlength="100" value="{{ old('name', $attribute->name) }}" required>@error('name')<span class="validation-message">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="attribute-type">نوع</label><select class="form-control" id="attribute-type" name="type"><option value="text" @selected(old('type', $attribute->type) === 'text')>متنی</option><option value="color" @selected(old('type', $attribute->type) === 'color')>رنگ</option></select>@error('type')<span class="validation-message">{{ $message }}</span>@enderror</div>
            </div><div class="actions"><button class="button button-primary" type="submit">ذخیرهٔ ویژگی</button></div>
        </form>
        <section class="form-card attribute-values-panel">
            <div class="section-heading"><div><h2>مقدارهای ویژگی</h2><p class="section-description">مقدارهای استفاده‌شده در محصولات قابل حذف نیستند.</p></div></div>
            @forelse ($attribute->values as $value)
                <div class="attribute-value-edit-row">
                    <form action="{{ route('panel.attribute-values.update', $value) }}" method="POST" class="attribute-value-edit-form">@csrf @method('PUT')
                        <input type="hidden" name="attribute_id" value="{{ $attribute->id }}">
                        <div class="field"><label for="value-{{ $value->id }}">مقدار</label><input class="form-control" id="value-{{ $value->id }}" name="value" maxlength="100" value="{{ old('value', $value->value) }}" required></div>
                        @if ($attribute->type === 'color')<div class="field"><label for="color-{{ $value->id }}">کد رنگ</label><input class="form-control color-input" id="color-{{ $value->id }}" name="color_code" type="color" value="{{ old('color_code', $value->color_code ?: '#000000') }}" required></div>@else<input type="hidden" name="color_code" value="">@endif
                        <button class="button button-secondary" type="submit">ذخیره</button>
                    </form>
                    @can('permission', 'delete-attribute')
                        @if ($value->prices_count === 0)<form action="{{ route('panel.attribute-values.destroy', $value) }}" method="POST" onsubmit="return confirm('این مقدار حذف شود؟')">@csrf @method('DELETE')<button class="button button-danger" type="submit">حذف</button></form>@else<span class="tag">استفاده‌شده در محصول</span>@endif
                    @endcan
                </div>
            @empty<p class="color-muted">هنوز مقداری برای این ویژگی ثبت نشده است.</p>@endforelse
            @can('permission', 'create-attribute')
                <form action="{{ route('panel.attribute-values.store') }}" method="POST" class="attribute-value-create-form">@csrf
                    <input type="hidden" name="attribute_id" value="{{ $attribute->id }}">
                    <div class="field"><label for="new-value">مقدار جدید</label><input class="form-control" id="new-value" name="value" maxlength="100" value="{{ old('value') }}" required></div>
                    @if ($attribute->type === 'color')<div class="field"><label for="new-color-code">کد رنگ</label><input class="form-control color-input" id="new-color-code" name="color_code" type="color" value="{{ old('color_code', '#000000') }}" required></div>@else<input type="hidden" name="color_code" value="">@endif
                    <button class="button button-primary" type="submit">افزودن مقدار</button>
                </form>
            @endcan
        </section>
    </div>
</div></main>
@endsection
