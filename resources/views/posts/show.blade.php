@extends('layout.master')

@section('content')

<main class="page-area">
    <div class="container article-container">

        <!-- Article Header -->
        <div class="article-header">
            <div class="spec-row">
                <strong>سرویس‌های خبری:</strong>
                <div class="article-tags-row">
                    @forelse($post->categories as $category)
                        <span class="tag">{{ $category->full_name }}</span>
                    @empty
                        <span class="tag">عمومی</span>
                    @endforelse
                </div>
            </div>
            <h1 class="detail-title detail-title--article">{{ $post->title }}</h1>
            <div class="detail-meta detail-meta--centered">
                <span>✍️ خبرنگار: {{ $post->user->first_name }} {{ $post->user->last_name }}</span>
                <span>📅 تاریخ انتشار: {{ \Morilog\Jalali\Jalalian::fromCarbon($post->published_at ?? $post->created_at ?? now())->format('d F Y') }}</span>
            </div>
        </div>

        <div class="article-media">
            @if($post->images->isNotEmpty())
                
                <img
                    id="mainImage"
                    class="detail-image article-main-image"
                    src="{{ Storage::url($post->images->first()->path) }}"
                    alt="{{ $post->title }}"
                >

                @if (count($post->images) > 1)
                    <div class="thumbs">
                        @foreach($post->images as $image)
                            <img
                                class="thumb {{ $loop->first ? 'active' : '' }}"
                                src="{{ Storage::url($image->path) }}"
                                alt="تصویر شاخص خبر"
                                onclick="document.getElementById('mainImage').src = this.src; document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active')); this.classList.add('active');"
                            >
                        @endforeach
                    </div>
                @endif

            @else
                <div class="detail-image detail-image--placeholder">
                    این پست تصویری ندارد.
                </div>
            @endif
        </div>

        <!-- Article Content -->
        <div class="article-body">
            <div class="article-text">
                {!! nl2br(e($post->description)) !!}
            </div>

            <!-- Actions -->
            <div class="owner owner--article">
                <div>
                    <strong>درباره نویسنده:</strong>
                    <p class="owner-note">
                        {{ $post->user->first_name }} {{ $post->user->last_name }}
                    </p>
                </div>
                
            </div>
        </div>

    </div>
</main>

@endsection
