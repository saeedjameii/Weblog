@extends('layout.panel')

@section('title', 'ایجاد محصول')
@section('header-actions')
    <a class="browse-link" href="{{ route('panel.products.index') }}">بازگشت به محصولات</a>
@endsection

@section('content')
<main class="page-area"><div class="main-content">
    <section class="intro"><p class="kicker">مدیریت فروشگاه</p><h1 class="display-font">ایجاد محصول</h1><p class="intro-copy">اطلاعات محصول، ویژگی‌ها و تنوع‌های موجود برای فروش را ثبت کنید.</p></section>
    <div class="workspace workspace--single">
        <form action="{{ route('panel.products.store') }}" method="POST" class="form-card" data-product-form>
            @csrf
            @if ($errors->any())<div class="alert alert-danger"><ul class="errors-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <section class="form-section form-section--flat">
                <div class="section-heading"><div><h2>اطلاعات اصلی محصول</h2></div></div>
                <div class="field-grid">
                    <div class="field full-field"><label for="product-name">نام محصول</label><input class="form-control" id="product-name" name="name" value="{{ old('name') }}" maxlength="255" required>@error('name')<span class="validation-message">{{ $message }}</span>@enderror</div>
                    <div class="field full-field"><label for="product-description">توضیحات</label><textarea class="form-control textarea--tall" id="product-description" name="description" required>{{ old('description') }}</textarea>@error('description')<span class="validation-message">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="product-unit">واحد اندازه‌گیری</label><input class="form-control" id="product-unit" name="unit" value="{{ old('unit') }}" maxlength="50" placeholder="مثلاً عدد یا کیلوگرم" required>@error('unit')<span class="validation-message">{{ $message }}</span>@enderror</div>
                    <div class="field"><label for="product-weight">وزن</label><input class="form-control" id="product-weight" name="weight" type="number" min="0" step="0.01" value="{{ old('weight') }}" required>@error('weight')<span class="validation-message">{{ $message }}</span>@enderror</div>
                    <div class="field full-field"><label for="product-categories">دسته‌بندی‌ها</label><select class="form-control select--tall" id="product-categories" name="categories[]" multiple required>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(in_array($category->id, old('categories', [])))>{{ $category->full_name }}</option>@endforeach</select>@error('categories')<span class="validation-message">{{ $message }}</span>@enderror @error('categories.*')<span class="validation-message">{{ $message }}</span>@enderror</div>
                </div>
            </section>
            <section class="form-section">
                <div class="section-heading"><div><h2>ویژگی‌های محصول</h2><p class="section-description">ویژگی‌ها و مقدارهای از قبل ثبت‌شده را انتخاب کنید.</p></div></div>
                @php($selectedAttributeIds = array_map('intval', old('attributes', [])))
                <div class="attribute-option-list">
                    @forelse ($attributes as $attribute)
                        <label class="attribute-option {{ $attribute->values->isEmpty() ? 'is-unavailable' : '' }}">
                            <input type="checkbox" name="attributes[]" value="{{ $attribute->id }}" data-product-attribute @checked(in_array($attribute->id, $selectedAttributeIds)) @disabled($attribute->values->isEmpty())>
                            <span>{{ $attribute->name }} <small>({{ $attribute->type === 'color' ? 'رنگ' : 'متنی' }})</small></span>
                            @if ($attribute->values->isEmpty())<small>مقداری برای انتخاب ندارد</small>@endif
                        </label>
                    @empty<p class="field-hint">هنوز ویژگی‌ای ثبت نشده است. ابتدا از بخش مدیریت ویژگی‌ها، ویژگی و مقدارهای آن را بسازید.</p>@endforelse
                </div>
                @error('attributes')<span class="validation-message">{{ $message }}</span>@enderror
                @error('attributes.*')<span class="validation-message">{{ $message }}</span>@enderror
            </section>
            <section class="form-section">
                <div class="section-heading"><div><h2>تنوع‌های قابل فروش</h2><p class="section-description">برای محصول بدون ویژگی نیز یک ردیف با قیمت و موجودی ثبت کنید.</p></div></div>
                <div class="variant-list" data-variant-list>
                    @php($oldVariants = old('variants', [['price' => '', 'discount' => '', 'inventory' => '', 'attribute_values' => []]]))
                    @foreach ($oldVariants as $index => $variant)
                        <fieldset class="variant-card" data-variant-row>
                            <div class="variant-heading"><legend>تنوع <span data-variant-number>{{ $loop->iteration }}</span></legend><button class="button button-secondary button-small" type="button" data-remove-variant>حذف تنوع</button></div>
                            <div class="field-grid">
                                @foreach ($attributes as $attribute)
                                    <div class="field full-field variant-attribute-field" data-variant-attribute="{{ $attribute->id }}" @if (!in_array($attribute->id, $selectedAttributeIds)) hidden @endif>
                                        <label>{{ $attribute->name }}</label>
                                        <select class="form-control" name="variants[{{ $index }}][attribute_values][{{ $attribute->id }}]" data-variant-value @disabled(!in_array($attribute->id, $selectedAttributeIds))>
                                            <option value="">انتخاب {{ $attribute->name }}</option>
                                            @foreach ($attribute->values as $value)<option value="{{ $value->id }}" @selected((int) data_get($variant, "attribute_values.{$attribute->id}") === $value->id)>{{ $value->value }}{{ $attribute->type === 'color' && $value->color_code ? ' — '.$value->color_code : '' }}</option>@endforeach
                                        </select>
                                        @error("variants.$index.attribute_values")<span class="validation-message">{{ $message }}</span>@enderror
                                    </div>
                                @endforeach
                                <div class="field"><label>قیمت اصلی</label><input class="form-control" type="number" min="0" step="0.01" name="variants[{{ $index }}][price]" value="{{ data_get($variant, 'price') }}" data-variant-price required>@error("variants.$index.price")<span class="validation-message">{{ $message }}</span>@enderror</div>
                                <div class="field"><label>درصد تخفیف</label><input class="form-control" type="number" min="0" max="100" step="0.01" name="variants[{{ $index }}][discount]" value="{{ data_get($variant, 'discount') }}" data-variant-discount>@error("variants.$index.discount")<span class="validation-message">{{ $message }}</span>@enderror</div>
                                <div class="field"><label>موجودی</label><input class="form-control" type="number" min="0" step="1" name="variants[{{ $index }}][inventory]" value="{{ data_get($variant, 'inventory') }}" required>@error("variants.$index.inventory")<span class="validation-message">{{ $message }}</span>@enderror</div>
                                <div class="field variant-final-price" aria-live="polite">قیمت نهایی: <strong data-final-price>—</strong></div>
                            </div>
                        </fieldset>
                    @endforeach
                </div>
                @error('variants')<span class="validation-message">{{ $message }}</span>@enderror
                <button class="button button-secondary mt-14" type="button" data-add-variant>افزودن تنوع</button>
            </section>
            <div class="actions"><a class="button button-secondary" href="{{ route('panel.products.index') }}">انصراف</a><button class="button button-primary" type="submit">ثبت محصول</button></div>
        </form>
    </div>
</div></main>
<template data-variant-template>
    <fieldset class="variant-card" data-variant-row>
        <div class="variant-heading"><legend>تنوع <span data-variant-number></span></legend><button class="button button-secondary button-small" type="button" data-remove-variant>حذف تنوع</button></div>
        <div class="field-grid">
            @foreach ($attributes as $attribute)
                <div class="field full-field variant-attribute-field" data-variant-attribute="{{ $attribute->id }}" hidden>
                    <label>{{ $attribute->name }}</label>
                    <select class="form-control" name="variants[__INDEX__][attribute_values][{{ $attribute->id }}]" data-variant-value disabled>
                        <option value="">انتخاب {{ $attribute->name }}</option>
                        @foreach ($attribute->values as $value)<option value="{{ $value->id }}">{{ $value->value }}{{ $attribute->type === 'color' && $value->color_code ? ' — '.$value->color_code : '' }}</option>@endforeach
                    </select>
                </div>
            @endforeach
            <div class="field"><label>قیمت اصلی</label><input class="form-control" type="number" min="0" step="0.01" name="variants[__INDEX__][price]" data-variant-price required></div>
            <div class="field"><label>درصد تخفیف</label><input class="form-control" type="number" min="0" max="100" step="0.01" name="variants[__INDEX__][discount]" data-variant-discount></div>
            <div class="field"><label>موجودی</label><input class="form-control" type="number" min="0" step="1" name="variants[__INDEX__][inventory]" required></div>
            <div class="field variant-final-price" aria-live="polite">قیمت نهایی: <strong data-final-price>—</strong></div>
        </div>
    </fieldset>
</template>
@endsection
