@extends('layout.master')

@section('header-actions')
    <a class="browse-link" href="{{ route('posts.show', $post) }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        <section class="intro">
            <p class="kicker">مدیریت ابزار</p>
            <h1 class="display-font">ویرایش پست</h1>
        </section>

        <div class="workspace">
            <form action="{{ route('posts.update', $post) }}" method="POST" class="form-card">

                @csrf
                @method('PUT')

                <div class="field-grid">

                    <div class="field full-field">
                        <label>عنوان</label>

                        <input
                            class="form-control @error('title') is-invalid @enderror"
                            type="text"
                            name="title"
                            value="{{ old('title', $post->title) }}"
                        >

                        @error('title')
                            <span class="validation-message" style="display:block;">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="field full-field">
                        <label>توضیحات</label>

                        <textarea class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description', $post->description) }}</textarea>

                        @error('description')
                            <span class="validation-message" style="display:block;">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="field full-field">
                        <label>دسته‌بندی</label>

                        <select class="form-control @error('category_id') is-invalid @enderror" name="category_id">

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('category_id')
                            <span class="validation-message" style="display:block;">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="field full-field">
                        <label for="condition">شرایط ابزار</label>
                        <select class="form-control" id="condition" name="condition" >
                            <option value="{{ old('condition', $post->condition) }}">شرایط ابزار را انتخاب کنید</option>
                            <option>جدید</option>
                            <option>تقریباً جدید</option>
                            <option>خوب</option>
                            <option>معمولی</option>
                        </select>
                        <span class="validation-message" id="condition-error"></span>
                    </div>


                    <div class="field">
                        <label for="edit-province-id">استان</label>
                        <select class="form-control" id="edit-province-id" name="province_id" data-province-select data-city-select="edit-city-id"
                            data-cities-url="{{ route('provinces.cities', ['province' => '__province__'], false) }}" required>
                            <option value="">استان را انتخاب کنید</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->id }}" @selected(old('province_id', $post->province_id) == $province->id)>{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province_id')<span class="validation-message" style="display:block;">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label for="edit-city-id">شهر</label>
                        <select class="form-control" id="edit-city-id" name="city_id" data-selected-city="{{ old('city_id', $post->city_id) }}" disabled required>
                            <option value="">ابتدا استان را انتخاب کنید</option>
                        </select>
                        @error('city_id')<span class="validation-message" style="display:block;">{{ $message }}</span>@enderror
                    </div>


                    <div class="field">
                        <label>قیمت روز اول</label>

                        <div class="currency-wrap toman-wrap">
                            <span class="currency-symbol">تومان</span>
                            <input
                                class="form-control toman-input"
                                id="edit-first-day-price"
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                placeholder="مثلاً ۱۰۰٬۰۰۰"
                            >
                            <input id="edit-first-day-price-value" type="hidden" name="first_day_price" value="{{ old('first_day_price', $post->first_day_price) }}">
                        </div>
                    </div>


                    <div class="field">
                        <label>قیمت هر روز اضافه</label>

                        <div class="currency-wrap toman-wrap">
                            <span class="currency-symbol">تومان</span>
                            <input
                                class="form-control toman-input"
                                id="edit-extra-day-price"
                                type="text"
                                inputmode="numeric"
                                autocomplete="off"
                                placeholder="مثلاً ۵۰٬۰۰۰"
                            >
                            <input id="edit-extra-day-price-value" type="hidden" name="extra_day_price" value="{{ old('extra_day_price', $post->extra_day_price) }}">
                        </div>
                    </div>


                    <div class="field">
                        <label>قابل استفاده از</label>

                        <div class="jalali-date-wrap"><input
                            class="form-control jalali-date-input"
                            id="edit-available-start"
                            type="text"
                            placeholder="۱۴۰۵/۰۶/۲۴"
                            readonly
                            aria-haspopup="dialog"
                            aria-expanded="false"
                        ><button class="jalali-calendar-button" type="button" aria-label="انتخاب تاریخ شروع" data-edit-date-picker-for="edit-available-start">📅</button></div>
                        <input id="edit-available-start-value" type="hidden" name="available_from" value="{{ old('available_from', $post->available_from) }}">
                    </div>


                    <div class="field">
                        <label>قابل استفاده تا</label>

                        <div class="jalali-date-wrap"><input
                            class="form-control jalali-date-input"
                            id="edit-available-end"
                            type="text"
                            placeholder="۱۴۰۵/۰۶/۲۴"
                            readonly
                            aria-haspopup="dialog"
                            aria-expanded="false"
                        ><button class="jalali-calendar-button" type="button" aria-label="انتخاب تاریخ پایان" data-edit-date-picker-for="edit-available-end">📅</button></div>
                        <input id="edit-available-end-value" type="hidden" name="available_untill" value="{{ old('available_untill', $post->available_untill) }}">
                    </div>

                </div>

                <div id="edit-jalali-calendar" class="jalali-calendar" role="dialog" aria-label="تقویم شمسی" hidden></div>

                <div style="display:flex; align-items:center; gap:14px; margin-top:8px;">
                    <button type="submit" class="button button-primary">
                        به‌روزرسانی پست
                    </button>

                    <a href="{{ route('posts.show', $post) }}" class="browse-link">
                        انصراف
                    </a>
                </div>

            </form>
        </div>

    </div>
</main>

@endsection
