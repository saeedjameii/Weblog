@extends('layout.master')
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
            <div class="alert alert-success" style="margin-top:20px;">{{ session('success') }}</div>
        @endif

        @error('post')
            <div class="alert alert-danger" style="margin-top:20px;">{{ $message }}</div>
        @enderror

        <div class="container news-grid" style="margin-top:24px; padding:0;">

            @forelse ($posts as $post)

                <article class="news-card">
                    <div class="news-body">

                        <div style="display:flex; gap:5px; flex-wrap:wrap; margin-bottom:10px;">
                            @forelse ($post->categories as $category)
                                <span class="tag">{{ $category->name }}</span>
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
                            action="{{ route('posts.restore', $post->id) }}"
                            method="POST"
                            style="margin-top:12px;"
                            onsubmit="return confirm('این پست بازیابی شود؟');"
                        >
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="button button-secondary" style="width:100%;">بازیابی پست</button>
                        </form>

                    </div>
                </article>

            @empty

                <div class="users-empty" style="grid-column:1/-1;">
                    <span aria-hidden="true">🗑️</span>
                    <p>هیچ پست حذف‌شده‌ای وجود ندارد.</p>
                </div>

            @endforelse

        </div>

    </div>
</main>
@endsection