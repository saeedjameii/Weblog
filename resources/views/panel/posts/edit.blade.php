@extends('layout.panel')

@section('header-actions')
    <a class="browse-link" href="{{ route('posts.show', $post) }}">← انصراف و بازگشت</a>
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
                                        {{ $category->name }}
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

                    </div>

                    <div
                        style="display:flex; align-items:center; gap:14px; margin-top:30px; padding-top:20px; border-top: 1px solid var(--line);">
                        <button type="submit" class="button button-primary">
                            به‌روزرسانی خبر
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>

@endsection
