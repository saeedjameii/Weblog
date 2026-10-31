@extends('layout.panel')

@section('title', 'ایجاد محصول')
@section('header-actions')
    <a class="browse-link" href="{{ route('panel.products.index') }}">بازگشت به محصولات</a>
@endsection

@section('content')
<main class="page-area"><div class="main-content">
    <section class="intro"><p class="kicker">مدیریت فروشگاه</p><h1 class="display-font">ایجاد محصول</h1><p class="intro-copy">اطلاعات محصول و قیمت‌های قابل فروش آن را ثبت کنید.</p></section>
    <div class="workspace workspace--single">
        <form action="{{ route('panel.products.store') }}" method="POST" enctype="multipart/form-data" class="form-card" data-product-form>
            @csrf
            @if ($errors->any())<div class="alert alert-danger"><ul class="errors-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <section class="form-section form-section--flat">
                <div class="section-heading"><div><h2>اطلاعات اصلی محصول</h2></div></div>
                <div class="field-grid">
                    <div class="field full-field"><label for="product-name">نام محصول</label><input class="form-control" id="product-name" name="name" value="{{ old('name') }}" maxlength="255" required>@error('name')<span class="validation-message">{{ $message }}</span>@enderror</div>
                    <div class="field full-field"><label for="product-description">توضیحات</label><textarea class="form-control textarea--tall" id="product-description" name="description" required>{{ old('description') }}</textarea>@error('description')<span class="validation-message">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="product-unit">واحد اندازه‌گیری</label><input class="form-control" id="product-unit" name="unit" value="{{ old('unit') }}" maxlength="50" required>@error('unit')<span class="validation-message">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="product-weight">وزن</label><input class="form-control" id="product-weight" name="weight" type="number" min="0" step="0.01" value="{{ old('weight') }}" required>@error('weight')<span class="validation-message">{{ $message }}</span>@enderror</div>
                    <div class="field full-field"><label for="product-categories">دسته‌بندی‌ها</label><select class="form-control select--tall" id="product-categories" name="categories[]" multiple required>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(in_array($category->id, old('categories', [])))>{{ $category->full_name }}</option>@endforeach</select>@error('categories')<span class="validation-message">{{ $message }}</span>@enderror @error('categories.*')<span class="validation-message">{{ $message }}</span>@enderror</div>
                </div>
            </section>
            <section class="form-section">
                <div class="section-heading">
                    <div>
                        <h2>تصاویر محصول</h2>
                        <p class="section-description">حداکثر ۵ تصویر، هر کدام حداکثر تا ۵ مگابایت (فرمت‌های JPG، PNG، WEBP)</p>
                    </div>
                </div>
                <div class="field full-field">
                    <div class="file-dropzone" data-file-dropzone>
                        <input class="file-dropzone-input" type="file" id="product-images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple data-image-input>
                        <div class="file-dropzone-icon">📷</div>
                        <p class="file-dropzone-title"><strong>انتخاب تصاویر</strong> یا کشیدن و رها کردن فایل‌ها اینجا</p>
                        <p class="file-dropzone-desc">فرمت‌های مجاز: JPG, PNG, WEBP — حداکثر ۵ تصویر</p>
                    </div>
                    @error('images')<span class="validation-message">{{ $message }}</span>@enderror
                    @error('images.*')<span class="validation-message">{{ $message }}</span>@enderror
                    <div class="image-preview-grid" data-image-preview></div>
                </div>
            </section>
            <section class="form-section">
                <div class="section-heading"><div><h2>قیمت‌های محصول</h2><p class="section-description">برای هر قیمت، مقدار ویژگی‌های مربوط به همان قیمت را انتخاب کنید.</p></div></div>
                <div class="price-list" data-price-list>
                    @php($oldPrices = old('prices', [['price' => '', 'discount' => '', 'inventory' => '', 'attribute_values' => []]]))
                    @foreach ($oldPrices as $index => $price)
                        <fieldset class="price-card" data-price-row>
                            <div class="price-heading"><legend>قیمت <span data-price-number>{{ $loop->iteration }}</span></legend><button class="button button-secondary button-small" type="button" data-remove-price>حذف قیمت</button></div>
                            <div class="field-grid">
                                @foreach ($attributes as $attribute)
                                    <div class="field full-field"><label>{{ $attribute->name }}</label><select class="form-control" name="prices[{{ $index }}][attribute_values][]" required><option value="">انتخاب {{ $attribute->name }}</option>@foreach ($attribute->values as $value)<option value="{{ $value->id }}" @selected(in_array($value->id, data_get($price, 'attribute_values', [])))>{{ $value->value }}{{ $attribute->type === 'color' && $value->color_code ? ' — '.$value->color_code : '' }}</option>@endforeach</select></div>
                                @endforeach
                                <div class="field"><label>قیمت اصلی</label><input class="form-control" type="number" min="0" step="0.01" name="prices[{{ $index }}][price]" value="{{ data_get($price, 'price') }}" data-price-amount required></div>
                                <div class="field"><label>درصد تخفیف</label><input class="form-control" type="number" min="0" max="100" step="0.01" name="prices[{{ $index }}][discount]" value="{{ data_get($price, 'discount') }}" data-price-discount></div>
                                <div class="field"><label>موجودی</label><input class="form-control" type="number" min="0" step="1" name="prices[{{ $index }}][inventory]" value="{{ data_get($price, 'inventory') }}" required></div>
                                <div class="field price-final-amount" aria-live="polite">قیمت نهایی: <strong data-final-price>—</strong></div>
                            </div>
                        </fieldset>
                    @endforeach
                </div>
                @error('prices')<span class="validation-message">{{ $message }}</span>@enderror
                @foreach ($errors->getMessages() as $key => $messages) @if (str_starts_with($key, 'prices.'))<span class="validation-message">{{ $messages[0] }}</span>@endif @endforeach
                <button class="button button-secondary mt-14" type="button" data-add-price>افزودن قیمت</button>
            </section>
            <div class="actions"><a class="button button-secondary" href="{{ route('panel.products.index') }}">انصراف</a><button class="button button-primary" type="submit">ثبت محصول</button></div>
        </form>
    </div>
</div></main>
<template data-price-template>
    <fieldset class="price-card" data-price-row>
        <div class="price-heading"><legend>قیمت <span data-price-number></span></legend><button class="button button-secondary button-small" type="button" data-remove-price>حذف قیمت</button></div>
        <div class="field-grid">
            @foreach ($attributes as $attribute)
                <div class="field full-field"><label>{{ $attribute->name }}</label><select class="form-control" name="prices[__INDEX__][attribute_values][]" required><option value="">انتخاب {{ $attribute->name }}</option>@foreach ($attribute->values as $value)<option value="{{ $value->id }}">{{ $value->value }}{{ $attribute->type === 'color' && $value->color_code ? ' — '.$value->color_code : '' }}</option>@endforeach</select></div>
            @endforeach
            <div class="field"><label>قیمت اصلی</label><input class="form-control" type="number" min="0" step="0.01" name="prices[__INDEX__][price]" data-price-amount required></div>
            <div class="field"><label>درصد تخفیف</label><input class="form-control" type="number" min="0" max="100" step="0.01" name="prices[__INDEX__][discount]" data-price-discount></div>
            <div class="field"><label>موجودی</label><input class="form-control" type="number" min="0" step="1" name="prices[__INDEX__][inventory]" required></div>
            <div class="field price-final-amount" aria-live="polite">قیمت نهایی: <strong data-final-price>—</strong></div>
        </div>
    </fieldset>
</template>
@endsection
