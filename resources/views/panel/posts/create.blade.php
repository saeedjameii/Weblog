@extends('layout.panel')

@section('header-actions')
    <a class="browse-link" href="{{ route('home') }}">← بازگشت به صفحه اصلی</a>
@endsection

@section('content')
    <main class="page-area">
        <div class="main-content">
            <section class="intro">
                <p class="kicker">پنل تحریریه</p>
                <h1 class="display-font">انتشار خبر جدید</h1>
                <p class="intro-copy">اخبار، مقالات و رویدادهای جدید را از اینجا روی سایت منتشر کنید.</p>
            </section>

            <div class="workspace" style="grid-template-columns: 1fr; max-width: 800px;">
                <form action="{{ route('panel.posts.store') }}" method="POST" enctype="multipart/form-data" id="listing-form" class="form-card">
                    @csrf
                    @if ($errors->any())
                        <div class="validation-message" style="display:block;margin-bottom:16px;">
                            <ul style="margin:0;padding-inline-start:20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <section class="form-section" style="border: none; padding: 0; margin: 0;">
                        <div class="section-heading">
                            <div>
                                <h2>محتوای خبر</h2>
                                <p class="section-description">تیتر و متن کامل خبر را به دقت وارد کنید.</p>
                            </div>
                        </div>
                        <div class="field-grid">

                            <div class="field full-field">
                                <label for="post-title">تیتر خبر</label>
                                <input class="form-control" id="post-title" name="title" type="text" maxlength="255" value="{{ old('title') }}"
                                    placeholder="مثلاً: تکنولوژی‌های جدید در سال ۲۰۲۶..." required>
                            </div>

                            <div class="field full-field">
                                <label for="categories">سرویس‌های خبری (دسته‌بندی‌ها)</label>
                                <select class="form-control" id="categories" name="categories[]" multiple required style="min-height: 120px;">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="field-hint">برای انتخاب چند دسته‌بندی، کلید Ctrl (یا Cmd در مک) را نگه دارید.</span>
                            </div>

                            <div class="field full-field">
                                <label for="description">متن کامل خبر</label>
                                <textarea class="form-control" id="description" name="description" style="min-height: 300px;" 
                                    placeholder="محتوای مقاله یا خبر را اینجا بنویسید..." required>{{ old('description') }}</textarea>
                            </div>

                            <div class="field full-field">
                                <label for="images">تصاویر خبر <span style="color:var(--muted); font-weight:400;">(اختیاری — حداکثر ۵ تصویر، هر کدام تا ۵ مگابایت)</span></label>
                                <input
                                    class="form-control"
                                    type="file"
                                    id="images"
                                    name="images[]"
                                    accept="image/jpeg,image/png,image/webp"
                                    multiple
                                >
                                @error('images')
                                    <span class="validation-message" style="display:block;">{{ $message }}</span>
                                @enderror
                                @error('images.*')
                                    <span class="validation-message" style="display:block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="publish-date-display">
                                    تاریخ انتشار (شمسی)
                                    <span style="color:var(--muted); font-weight:400;">(اختیاری)</span>
                                </label>
                                <div class="jalali-date-wrap">
                                    <input class="form-control jalali-date-input @error('published_date') is-invalid @enderror"
                                        id="publish-date-display" type="text"
                                        value="{{ old('published_date') }}"
                                        placeholder="مثال: ۱۴۰۵/۰۷/۲۱" readonly aria-haspopup="dialog" aria-expanded="false"/>
                                    <button class="jalali-calendar-button" type="button" aria-label="انتخاب تاریخ انتشار" data-publish-date-picker>📅</button>
                                </div>
                                <input id="published_date" name="published_date" type="hidden" value="{{ old('published_date') }}"/>
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
                                        value="{{ old('published_time') }}"
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
                                    💡 در صورت تعیین تاریخ و ساعت در آینده، خبر در حالت زمان‌بندی قرار گرفته و تا فرارسیدن زمان مشخص‌شده منتشر نخواهد شد. در صورت خالی گذاشتن، خبر بلافاصله پس از انتشار نمایش داده می‌شود.
                                </span>
                            </div>

                        </div>
                    </section>

                    <div class="actions">
                        <button class="button button-secondary" type="submit" name="action" value="draft">
                            ذخیره پیش‌نویس
                        </button>

                        <button class="button button-primary" type="submit" name="action" value="published">
                            منتشر کردن خبر
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
@endsection
