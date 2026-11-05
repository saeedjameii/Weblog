@extends('layout.panel')

@section('title', 'ویرایش دسته‌بندی پست — چی بهتره؟')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel.categories.posts.index') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')
    <main class="page-area">
        <div class="main-content">
            <section class="intro">
                <p class="kicker">مدیریت محتوا</p>
                <h1 class="display-font">ویرایش دسته‌بندی: {{ $category->name }}</h1>
            </section>

            <div class="workspace">
                <form action="{{ route('panel.categories.posts.update', $category) }}" method="POST" class="form-card">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="type" value="{{ $category->type }}">
                    <div class="field-grid">
                        <div class="field full-field">
                            <label for="name">نام دسته‌بندی</label>
                            <input class="form-control" type="text" name="name" id="name" maxlength="60"
                                value="{{ old('name', $category->name) }}" required>

                            @error('name')
                                <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="parent_id">دسته‌بندی والد</label>
                            <select class="form-control" name="parent_id" id="parent_id">
                                <option value="">بدون والد (دسته‌بندی اصلی)</option>
                                @foreach ($categories as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->full_name }}
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
                                value="{{ old('order', $category->order ?? 0) }}">
                            <span class="field-hint">عدد کمتر = اولویت بالاتر</span>
                            @error('order')
                                <span class="validation-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="actions">
                        <a class="button button-secondary" href="{{ route('panel.categories.posts.index') }}">انصراف</a>
                        <button type="submit" class="button button-primary">
                            ذخیره تغییرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
