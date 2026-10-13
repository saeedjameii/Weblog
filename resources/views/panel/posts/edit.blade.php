@extends('layout.panel')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel.posts.mine') }}">← انصراف و بازگشت</a>
@endsection

@section('content')

    <main class="page-area">
        <div class="main-content">

            <section class="intro">
                <p class="kicker">پنل تحریریه</p>
                <h1 class="display-font">ویرایش خبر</h1>
            </section>

            <div class="workspace" style="grid-template-columns: 1fr; max-width: 800px;">
                <form action="{{ route('panel.posts.update', $post) }}" method="POST" class="form-card"
                    enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="field-grid">

                        <div class="field full-field">
                            <label>تیتر خبر</label>
                            <input class="form-control @error('title') is-invalid @enderror" type="text" name="title"
                                value="{{ old('title', $post->title) }}" required>
                            @error('title')
                                <span class="validation-message" style="display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field full-field">
                            <label>سرویس‌های خبری (دسته‌بندی‌ها)</label>
                            <select class="form-control @error('categories') is-invalid @enderror" name="categories[]"
                                multiple required style="min-height: 120px;">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $category->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categories')
                                <span class="validation-message" style="display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field full-field">
                            <label>متن کامل خبر</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" style="min-height: 300px;"
                                required>{{ old('description', $post->description) }}</textarea>
                            @error('description')
                                <span class="validation-message" style="display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- نمایش تصاویر فعلی --}}
                        @if ($post->images->isNotEmpty())
                            <div class="field full-field">
                                <label>تصاویر فعلی</label>
                                <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:8px;">
                                    @foreach ($post->images as $image)
                                        <div style="position:relative; width:120px;">
                                            <img src="{{ Storage::url($image->path) }}" alt="تصویر پست"
                                                style="width:120px; height:80px; object-fit:cover; border-radius:6px; border:1px solid var(--line);">
                                        </div>
                                    @endforeach
                                </div>
                                <p style="color:var(--muted); font-size:0.85rem; margin-top:8px;">
                                    با آپلود تصاویر جدید، تصاویر فعلی جایگزین می‌شوند.
                                </p>
                            </div>
                        @endif

                        {{-- آپلود تصاویر جدید --}}
                        <div class="field full-field">
                            <label for="images">
                                {{ $post->images->isNotEmpty() ? 'جایگزینی تصاویر' : 'تصاویر خبر' }}
                                <span style="color:var(--muted); font-weight:400;">(اختیاری — حداکثر ۵ تصویر، هر کدام تا ۵
                                    مگابایت)</span>
                            </label>
                            <input class="form-control" type="file" id="images" name="images[]"
                                accept="image/jpeg,image/png,image/webp" multiple>
                            @error('images')
                                <span class="validation-message" style="display:block;">{{ $message }}</span>
                            @enderror
                            @error('images.*')
                                <span class="validation-message" style="display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        @php
                            $jalaliPublishedDate = $post->published_at ? \Morilog\Jalali\Jalalian::fromCarbon($post->published_at)->format('Y/m/d') : '';
                            $jalaliPublishedTime = $post->published_at ? \Morilog\Jalali\Jalalian::fromCarbon($post->published_at)->format('H:i') : '';
                        @endphp

                        <div class="field">
                            <label for="publish-date-display">
                                تاریخ انتشار (شمسی)
                                <span style="color:var(--muted); font-weight:400;">(اختیاری)</span>
                            </label>
                            <div class="jalali-date-wrap">
                                <input class="form-control jalali-date-input @error('published_date') is-invalid @enderror"
                                    id="publish-date-display" type="text"
                                    value="{{ old('published_date', $jalaliPublishedDate) }}"
                                    placeholder="مثال: ۱۴۰۵/۰۷/۲۱" readonly aria-haspopup="dialog" aria-expanded="false"/>
                                <button class="jalali-calendar-button" type="button" aria-label="انتخاب تاریخ انتشار" data-publish-date-picker>📅</button>
                            </div>
                            <input id="published_date" name="published_date" type="hidden"
                                value="{{ old('published_date', $jalaliPublishedDate) }}"/>
                            <div id="post-jalali-calendar" class="jalali-calendar" role="dialog" aria-label="تقویم شمسی تاریخ انتشار" hidden></div>
                            @error('published_date')
                                <span class="validation-message" style="display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="published_time">
                                ساعت انتشار
                                <span style="color:var(--muted); font-weight:400;">(اختیاری)</span>
                            </label>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <input class="form-control @error('published_time') is-invalid @enderror"
                                    type="time" id="published_time" name="published_time"
                                    value="{{ old('published_time', $jalaliPublishedTime) }}"
                                    dir="ltr">
                                <button type="button" data-clear-publish-date class="button button-secondary" style="padding: 0 12px; height: 42px; font-size: 0.82rem; white-space: nowrap;" title="پاک کردن تاریخ و زمان">
                                    انتشار فوری
                                </button>
                            </div>
                            @error('published_time')
                                <span class="validation-message" style="display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field full-field" style="margin-top: -10px;">
                            <span class="field-hint">
                                💡 در صورت تعیین تاریخ و ساعت در آینده، خبر در حالت زمان‌بندی قرار می‌گیرد. با زدن «انتشار فوری» تاریخ خالی شده و خبر در همان لحظه منتشر می‌گردد.
                            </span>
                        </div>

                    </div>

                    <div
                        style="display:flex; align-items:center; gap:14px; margin-top:30px; padding-top:20px; border-top: 1px solid var(--line);">
                        @if ($post->status === 'draft')
                            <button type="submit" name="action" value="draft" class="button button-secondary">
                                ذخیره پیش‌نویس
                            </button>
                            <button type="submit" name="action" value="published" class="button button-primary">
                                انتشار خبر
                            </button>
                        @else
                            <button type="submit" name="action" value="published" class="button button-primary">
                                به‌روزرسانی خبر
                            </button>
                            <button type="submit" name="action" value="draft" class="button button-secondary">
                                تغییر به پیش‌نویس
                            </button>
                        @endif
                    </div>

                </form>
            </div>

        </div>
    </main>

@endsection
