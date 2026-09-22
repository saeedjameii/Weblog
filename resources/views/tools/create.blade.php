@extends('layout.master')

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
                <form action="{{ route('create_post.post') }}" method="POST" enctype="multipart/form-data" id="listing-form" class="form-card">
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
                                <label for="tool-title">تیتر خبر</label>
                                <input class="form-control" id="tool-title" name="title" type="text" maxlength="255" value="{{ old('title') }}"
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
                        </div>
                    </section>
                    <div class="actions">
                        <button class="button button-primary" type="submit">منتشر کردن خبر</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
