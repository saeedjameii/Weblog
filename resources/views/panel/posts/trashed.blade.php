@extends('layout.panel')

@section('title', 'پست‌های حذف‌شده — خبرنامه')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')
<main class="page-area">
    <div class="main-content">

        <section class="intro">
            <p class="kicker">سطل زباله</p>
            <h1 class="display-font">پست‌های حذف‌شده</h1>
            <p class="intro-copy">پست‌هایی که حذف شده‌اند را از همین‌جا بازیابی کنید.</p>
        </section>

        @if (session('success'))
            <div class="alert alert-success mt-20">{{ session('success') }}</div>
        @endif

        @error('post')
            <div class="alert alert-danger mt-20">{{ $message }}</div>
        @enderror

        <div class="container news-grid mt-24 p-0">

            @forelse ($posts as $post)

                <article class="news-card trashed-card">

                    @if($post->images->isNotEmpty())
                        <div class="news-image-link">
                            <img src="{{ Storage::url($post->images->first()->path) }}" alt="تصویر خبر" class="news-image-cover news-image-cover--sm">
                        </div>
                    @endif

                    <div class="news-body">

                        <div class="tags-row">
                            @forelse ($post->categories as $category)
                                <span class="tag">{{ $category->full_name }}</span>
                            @empty
                                <span class="tag">عمومی</span>
                            @endforelse
                        </div>

                        <h3>{{ $post->title }}</h3>
                        <p class="news-excerpt">{{ Str::limit($post->description, 120) }}</p>

                        <div class="card-bottom">
                            <span class="author">✍️ {{ $post->user->first_name }} {{ $post->user->last_name }}</span>
                            <span>🗑️ حذف‌شده در {{ \Morilog\Jalali\Jalalian::fromCarbon($post->deleted_at)->format('d F Y') }}</span>
                        </div>

                        <form
                            action="{{ route('panel.posts.restore', $post->id) }}"
                            method="POST"
                            class="trashed-form"
                            onsubmit="return confirm('این پست بازیابی شود؟');"
                        >
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="button button-secondary w-full">بازیابی پست</button>
                        </form>

                    </div>
                </article>

            @empty

                <div class="users-empty full-width">
                    <span aria-hidden="true">🗑️</span>
                    <p>هیچ پست حذف‌شده‌ای وجود ندارد.</p>
                </div>

            @endforelse

        </div>

    </div>
</main>
@endsection
