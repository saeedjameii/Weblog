@extends('layout.panel')

@section('title', 'ساخت دسته‌بندی پست — چی بهتره؟')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel.categories.posts.index') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')
    <main class="page-area">
        <div class="main-content">
            <section class="intro">
                <p class="kicker">مدیریت محتوا</p>
                <h1 class="display-font">دسته‌بندی جدید پست</h1>
                <p class="intro-copy">یک دسته‌بندی تازه بسازید یا آن را زیرمجموعه‌ی یک دسته‌بندی دیگر قرار دهید.</p>
            </section>

            <div class="workspace">
                <form action="{{ route('panel.categories.posts.store') }}" method="POST" class="form-card">
                    @csrf
                    <input type="hidden" name="type" value="post">
                    <div class="field-grid">
                        <div class="field full-field">
                            <label for="name">نام دسته‌بندی</label>
                            <input class="form-control" type="text" name="name" id="name" maxlength="60"
                                placeholder="مثلاً: تکنولوژی و هوش مصنوعی" value="{{ old('name') }}" required>

                            @error('name')
                                <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="parent_id">دسته‌بندی والد</label>
                            <select class="form-control" name="parent_id" id="parent_id">
                                <option value="">بدون والد (دسته‌بندی اصلی)</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="order">اولویت نمایش (ترتیب)</label>
                            <input class="form-control" type="number" name="order" id="order" min="0"
                                placeholder="مثلاً: 0" value="{{ old('order', 0) }}">
                            <span class="field-hint">عدد کمتر = نمایش بالاتر (با Drag & Drop هم قابل تغییر است)</span>
                            @error('order')
                                <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="actions">
                        <a class="button button-secondary" href="{{ route('panel.categories.posts.index') }}">انصراف</a>
                        <button type="submit" class="button button-primary">
                            ثبت دسته‌بندی
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
