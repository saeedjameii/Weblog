@extends('layout.master')

@section('title', 'Edit Category ToolShare')

@section('header-actions')
    <a class="browse-link" href="{{ route('categories.index') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        <section class="intro">
            <p class="kicker">مدیریت محتوا</p>
            <h1 class="display-font">ویرایش دسته‌بندی</h1>
            <p class="intro-copy">نام یا دسته‌بندی والد را ویرایش کنید</p>
        </section>

        <div class="workspace">
            <form action="{{ route('categories.update', $category) }}" method="POST" class="form-card">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="{{ $category->type }}">
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
                            value="{{ old('name', $category->name) }}"
                        >

                        @error('name')
                            <span class="validation-message" style="display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field full-field">
                        <label for="parent_id">دسته‌بندی والد</label>

                        <select class="form-control" name="parent_id" id="parent_id">

                            <option value="">بدون والد (دسته‌بندی اصلی)</option>

                            @foreach($categories as $parent)
                                <option
                                    value="{{ $parent->id }}"
                                    {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}
                                >
                                    {{ $parent->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('parent_id')
                            <span class="validation-message" style="display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <button type="submit" class="button button-primary" style="margin-top:8px;">
                    ویرایش دسته‌بندی
                </button>

            </form>
        </div>

    </div>
</main>

@endsection