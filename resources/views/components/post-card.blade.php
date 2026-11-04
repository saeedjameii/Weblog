@props(['post', 'limit' => 120])
@php
    $image = $post->images->first();
    $authorName = trim(($post->user?->first_name ?? '') . ' ' . ($post->user?->last_name ?? '')) ?: 'تحریریه';
@endphp
<article class="pcard pcard--post lift">
    <div class="pcard__media">
        @if ($image)
            <div class="frame frame--cover is-loading">
                <img src="{{ Storage::url($image->path) }}" alt="{{ $post->title }}" width="640" height="360" loading="lazy" decoding="async">
            </div>
        @else
            <div class="frame frame--cover frame--empty" role="img" aria-label="این خبر تصویر ندارد"></div>
        @endif
    </div>
    <div class="pcard__body">
        <div class="badges">
            @forelse ($post->categories->take(2) as $category)
                <span class="badge badge--blog">{{ $category->full_name }}</span>
            @empty
                <span class="badge badge--blog">عمومی</span>
            @endforelse
        </div>
        <h3 class="pcard__title"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h3>
        <p class="pcard__text">{{ \Illuminate\Support\Str::limit($post->description, $limit) }}</p>
        <div class="pcard__meta">
            <span>{{ $authorName }}</span>
            <span class="pcard__more">ادامه مطلب <x-icon name="arrow" /></span>
        </div>
    </div>
</article>
