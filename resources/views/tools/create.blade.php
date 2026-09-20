@extends('layout.master')

@section('nav-links')
    <p></p>
@endsection

@section('header-actions')
    {{-- <a class="btn btn-secondary" href="{{ route('home') }}">Home</a> --}}
    <a class="browse-link" href="{{ route('home') }}">← بازگشت به صفحه اصلی</a>
@endsection

@section('content')
    <main class="page-area">
        <div class="main-content">
            <section class="intro">
                <p class="kicker">بیشتر به اشتراک بگذار. کمتر بخر.</p>
                <h1 class="display-font">یک ابزار را برای اجاره ثبت کنید</h1>
                <p class="intro-copy">ابزار خود را برای اجاره ثبت کنید تا همسایگان بتوانند از آن استفاده کنند.</p>
            </section>

            <div class="workspace">
                <form action="{{ route('create_post.post') }}" method="POST" enctype="multipart/form-data" id="listing-form">
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
                    <section class="form-section">
                        <div class="section-heading"><span class="step-number">01</span>
                            <div>
                                <h2>مشخصات ابزار</h2>
                                <p class="section-description">اطلاعات دقیق ابزار خود را وارد کنید تا همسایگان بتوانند از آن استفاده کنند.</p>
                            </div>
                        </div>
                        <div class="field-grid">

                            <div class="field full-field">
                                <label for="tool-title">نام ابزار</label>
                                <input class="form-control" id="tool-title" name="title" type="text" maxlength="80" value="{{ old('title') }}"
                                    placeholder="دریل شارژی" required>
                                <span class="validation-message" id="tool-title-error"></span>
                            </div>

                            <div class="field full-field">
                                <label for="description">توضیحات</label>
                                <textarea class="form-control" id="description" name="description" maxlength="1000" value="{{ old('description') }}"
                                    placeholder="توضیحاتی درباره ابزار و قابلیت‌های آن ارائه دهید."></textarea>
                                <div class="counter-line"><span id="character-count">0 / 1000</span></div>
                                <span class="validation-message" id="description-error"></span>
                            </div>

                            <div class="field">
                                <label for="category">دسته‌بندی</label>
                                <select class="form-control" id="category" name="category_id" value="{{ old('category_id') }}">
                                    <option value="">یک دسته‌بندی را انتخاب کنید</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="validation-message" id="category-error"></span>
                            </div>

                            <div class="field">
                                <label for="condition">شرایط ابزار</label>
                                <select class="form-control" id="condition" name="condition" value="{{ old('condition') }}">
                                    <option value="">شرایط ابزار را انتخاب کنید</option>
                                    <option>جدید</option>
                                    <option>تقریباً جدید</option>
                                    <option>خوب</option>
                                    <option>معمولی</option>
                                </select>
                                <span class="validation-message" id="condition-error"></span>
                            </div>

                            <div class="field">
                                <label for="province-id">استان</label>
                                <select class="form-control" id="province-id" name="province_id" data-province-select data-city-select="city-id"
                                    data-cities-url="{{ route('provinces.cities', ['province' => '__province__'], false) }}" required>
                                    <option value="">استان را انتخاب کنید</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}" @selected(old('province_id') == $province->id)>{{ $province->name }}</option>
                                    @endforeach
                                </select>
                                @error('province_id')<span class="validation-message" style="display:block;">{{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label for="city-id">شهر</label>
                                <select class="form-control" id="city-id" name="city_id" data-selected-city="{{ old('city_id') }}" disabled required>
                                    <option value="">ابتدا استان را انتخاب کنید</option>
                                </select>
                                @error('city_id')<span class="validation-message" style="display:block;">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="form-section">
                        <div class="section-heading"><span class="step-number">02</span>
                            <div>
                                <h2>قیمت‌گذاری و در دسترس بودن</h2>
                                <p class="section-description">یک قیمت ساده روزانه تنظیم کنید و زمانی که ابزار شما قابل برداری است را انتخاب کنید.</p>
                            </div>
                        </div>
                        <div class="field-grid">

                            <div class="field"><label for="first-day-price">قیمت روز اول</label>
                                <div class="currency-wrap toman-wrap"><span class="currency-symbol">تومان</span><input class="form-control toman-input"
                                        id="first-day-price" type="text" inputmode="numeric" autocomplete="off" value=""
                                        placeholder="مثلاً ۱۰۰٬۰۰۰" aria-describedby="first-day-price-hint"><input
                                        id="first-day-price-value" name="first_day_price" type="hidden" value="{{ old('first_day_price') }}"></div><span
                                    id="first-day-price-hint" class="field-hint">مبلغ را به تومان وارد کنید.</span><span class="validation-message"
                                    id="first-day-error"></span>
                            </div>
                            <div class="field"><label for="extra-day-price">قیمت هر روز اضافی</label>
                                <div class="currency-wrap toman-wrap"><span class="currency-symbol">تومان</span><input class="form-control toman-input"
                                        id="extra-day-price" type="text" inputmode="numeric" autocomplete="off" value=""
                                        placeholder="مثلاً ۵۰٬۰۰۰" aria-describedby="extra-day-price-hint"><input
                                        id="extra-day-price-value" name="extra_day_price" type="hidden" value="{{ old('extra_day_price') }}"></div><span
                                    id="extra-day-price-hint" class="field-hint">مبلغ را به تومان وارد کنید.</span><span class="validation-message"
                                    id="extra-day-error"></span>
                            </div>
                            <div class="field"><label for="available-start">در دسترس از</label><div class="jalali-date-wrap"><input
                                    class="form-control jalali-date-input" id="available-start" type="text" readonly
                                    placeholder="۱۴۰۵/۰۶/۲۴" aria-describedby="available-start-hint" aria-haspopup="dialog" aria-expanded="false"><button
                                    class="jalali-calendar-button" type="button" aria-label="انتخاب تاریخ شروع" data-date-picker-for="available-start">📅</button></div><input id="available-start-value"
                                    name="available_from" type="hidden" value="{{ old('available_from') }}"><span id="available-start-hint"
                                    class="field-hint">تاریخ را از تقویم شمسی انتخاب کنید.</span>
                            </div>
                            <div class="field"><label for="available-end">در دسترس تا</label><div class="jalali-date-wrap"><input
                                    class="form-control jalali-date-input" id="available-end" type="text" readonly
                                    placeholder="۱۴۰۵/۰۶/۲۴" aria-describedby="available-end-hint" aria-haspopup="dialog" aria-expanded="false"><button
                                    class="jalali-calendar-button" type="button" aria-label="انتخاب تاریخ پایان" data-date-picker-for="available-end">📅</button></div><input id="available-end-value"
                                    name="available_untill" type="hidden" value="{{ old('available_untill') }}"><span id="available-end-hint"
                                    class="field-hint">تاریخ را از تقویم شمسی انتخاب کنید.</span><span
                                    class="validation-message" id="date-error"></span></div>
                        </div>
                        <div id="jalali-calendar" class="jalali-calendar" role="dialog" aria-label="تقویم شمسی" hidden></div>
                    </section>

                    <section class="form-section">
                        <div class="section-heading"><span class="step-number">03</span>
                            <div>
                                <h2>Photos</h2>
                                <p class="section-description">تصاویر شفاف و روشن به لیست شما کمک می‌کنند تا برجسته باشد.</p>
                            </div>
                        </div>
                        <label class="upload-zone" for="tool-photos">
                            <input id="tool-photos" name="images[]" type="file" accept="image/*" multiple>
                            <span>
                            <span class="upload-icon">＋</span>
                            <span class="upload-title">بارگذاری تصاویر</span>
                            <span class="upload-copy">تا 5 تصویر می‌توانید آپلود کنید.</span>
                            </span>
                        </label>
                        <div id="thumbnail-strip" class="thumbnail-strip"></div>
                        <p id="photo-status" class="photo-status"></p>
                    </section>

                    <section class="form-section">
                        <div class="section-heading"><span class="step-number">04</span>
                            <div>
                                <h2>توضیحات اضافی</h2>
                                <p class="section-description">جزئیات اختیاری برای انتقال روان‌تر.</p>
                            </div>
                        </div>
                        <div class="field"><label for="rental-notes">توضیحات اضافی (اختیاری)</label>
                            <textarea class="form-control" id="rental-notes" name="rental-notes" maxlength="400"
                                placeholder="توضیحات اضافی درباره ابزار..."></textarea>
                        </div>
                    </section>

                    <div class="actions">
                        <button class="button button-primary" type="submit">ثبت ابزار</button>
                        <p id="form-status" class="form-status"></p>
                    </div>
                </form>

                <aside class="preview-wrap">
                    <section class="preview-card">
                        <div class="preview-heading">
                            <h2>پیش‌نمایش ابزار</h2><span class="preview-badge">پیش‌نمایش</span>
                        </div>
                        <div class="preview-image-box"><img id="preview-image" class="preview-image" alt="Tool preview"
                                hidden><span class="tool-illustration">🔧</span></div>
                        <div class="preview-body">
                            <span id="preview-category" class="preview-category">دسته‌بندی</span>
                            <h3 id="preview-title" class="preview-title">نام ابزار</h3>
                            <p id="preview-meta" class="preview-meta">شرایط · مکان دریافت</p>
                            <div class="preview-price-box">
                                <p class="preview-price-label">قیمت اجاره</p>
                                <p id="preview-price" class="preview-price">تومان 0 روز اول · تومان 0 روز اضافی</p>
                            </div>
                        </div>
                    </section>
                    <p class="review-note"><span>✓</span><span>پیش‌نمایش شما قبل از انتشار قابل بررسی است.</span>
                    </p>
                </aside>
            </div>
        </div>
    </main>
@endsection
