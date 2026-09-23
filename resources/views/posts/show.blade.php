@extends('layout.master')

@section('content')

<main class="page-area">
    <div class="container" style="padding-top: 40px; padding-bottom: 80px;">

        <!-- Article Header -->
        <div style="max-width: 800px; margin: 0 auto 30px; text-align: center;">
            <div class="spec-row">
                <strong>سرویس‌های خبری:</strong>
                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                    @forelse($post->categories as $category)
                        <span class="tag">{{ $category->name }}</span>
                    @empty
                        <span class="tag">عمومی</span>
                    @endforelse
                </div>
            </div>
            <h1 class="detail-title" style="font-size: 2.5rem; margin-bottom: 20px;">{{ $post->title }}</h1>
            <div class="detail-meta" style="display: flex; justify-content: center; gap: 20px; font-size: 0.95rem;">
                <span>✍️ خبرنگار: {{ $post->user->first_name }} {{ $post->user->last_name }}</span>
                <span>📅 تاریخ انتشار: {{ \Morilog\Jalali\Jalalian::fromCarbon($post->created_at ?? now())->format('d F Y') }}</span>
            </div>
        </div>

        <!-- Article Content -->
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="line-height: 2; font-size: 1.15rem; color: var(--ink); text-align: justify;">
                {!! nl2br(e($post->description)) !!}
            </div>

            <!-- Actions -->
            <div class="owner" style="margin-top:60px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div>
                    <strong>درباره نویسنده:</strong>
                    <p style="margin:6px 0 0;">
                        {{ $post->user->first_name }} {{ $post->user->last_name }}
                    </p>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    @can('update', $post)
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary">ویرایش خبر</a>
                    @endcan
                    @can('delete', $post)
                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary text-danger">حذف</button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>

    </div>
</main>

@endsection