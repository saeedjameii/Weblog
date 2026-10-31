@extends('layout.panel')

@section('title', 'ساخت دسته‌بندی — خبرنامه')


@section('header-actions')
    <a class="browse-link" href="{{ route('panel.categories.index') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        <section class="intro">
            <p class="kicker">مدیریت محتوا</p>
            <h1 class="display-font">دسته‌بندی جدید</h1>
            <p class="intro-copy">یک دسته‌بندی تازه بسازید یا آن را زیرمجموعه‌ی یک دسته‌بندی دیگر قرار دهید.</p>
        </section>

        <div class="workspace">
            <form action="{{ route('panel.categories.store') }}" method="POST" class="form-card">
                @csrf
                <div class="field-grid">
                    <div class="field full-field">
                        <label for="name">نام دسته‌بندی</label>
                        <input
                            class="form-control"
                            type="text"
                            name="name"
                            id="name"
                            maxlength="60"
                            placeholder="مثلاً: ابزار برقی"
                            value="{{ old('name') }}">

                        @error('name')
                            <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field full-field">
                        <label for="type">نوع دسته‌بندی</label>
                        <select class="form-control" name="type" id="type" required>
                            <option value="post" {{ old('type') === 'post' ? 'selected' : '' }}>اخبار و مقالات (پست)</option>
                            <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>فروشگاه (محصول)</option>
                        </select>
                        @error('type')
                            <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field full-field">
                        <label for="parent_id">دسته‌بندی والد</label>

                        <select class="form-control" name="parent_id" id="parent_id">

                            <option value="">بدون والد (دسته‌بندی اصلی)</option>

                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ old('parent_id') == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->full_name }}
                                </option>
                            @endforeach

                        </select>

                        @error('parent_id')
                            <span class="validation-message">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="actions">
                    <a class="button button-secondary" href="{{ route('panel.categories.index') }}">انصراف</a>
                    <button type="submit" class="button button-primary">
                        ثبت دسته‌بندی
                    </button>
                </div>

            </form>
        </div>

    </div>
</main>

@endsection
