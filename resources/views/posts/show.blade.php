@extends('layout.master')

@section('content')

<div class="read-progress" data-read-progress aria-hidden="true"></div>

<main class="page-area">
    <div class="container article-wrap">
        <ol class="breadcrumb" aria-label="مسیر صفحه">
            <li><a href="{{ route('home') }}">صفحه اصلی</a></li>
            <li><a href="{{ route('posts.index') }}">اخبار</a></li>
            <li aria-current="page">{{ \Illuminate\Support\Str::limit($post->title, 60) }}</li>
        </ol>

        <article class="article glass-strong" data-reading-target>

            <!-- Article Header -->
            <header>
                <div class="badges">
                    @forelse($post->categories as $category)
                        <span class="badge badge--blog">{{ $category->full_name }}</span>
                    @empty
                        <span class="badge badge--blog">عمومی</span>
                    @endforelse
                </div>
                <h1 class="article__title">{{ $post->title }}</h1>
                <div class="article__meta">
                    <span><x-icon name="pen" /> خبرنگار: {{ $post->user->first_name }} {{ $post->user->last_name }}</span>
                    <span><x-icon name="calendar" /> تاریخ انتشار: {{ \Morilog\Jalali\Jalalian::fromCarbon($post->published_at ?? $post->created_at ?? now())->format('d F Y') }}</span>
                </div>
            </header>

            <div class="article__media">
                @if($post->images->isNotEmpty())
                    <div class="frame frame--cover">
                        <img
                            id="mainImage"
                            class="detail-image article-main-image"
                            src="{{ Storage::url($post->images->first()->path) }}"
                            alt="{{ $post->title }}"
                            width="960" height="540" decoding="async"
                        >
                    </div>

                    @if (count($post->images) > 1)
                        <div class="thumbs">
                            @foreach($post->images as $image)
                                <img
                                    class="thumb {{ $loop->first ? 'active' : '' }}"
                                    src="{{ Storage::url($image->path) }}"
                                    alt="تصویر شاخص خبر"
                                    width="72" height="72" loading="lazy" decoding="async"
                                    onclick="document.getElementById('mainImage').src = this.src; document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active')); this.classList.add('active');"
                                >
                            @endforeach
                        </div>
                    @endif

                @else
                    <div class="frame frame--cover frame--empty" role="img" aria-label="این پست تصویری ندارد"></div>
                @endif
            </div>

            <!-- Article Content -->
            <div class="article-body">
                <div class="article__text">
                    {!! nl2br(e($post->description)) !!}
                </div>

                <div class="article__author">
                    <strong>درباره نویسنده:</strong>
                    <p>{{ $post->user->first_name }} {{ $post->user->last_name }}</p>
                </div>
            </div>
        </article>
    </div>
</main>

@endsection
