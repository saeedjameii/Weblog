@extends('layout.panel')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel') }}">← بازگشت به داشبورد</a>
@endsection

@section('content')
<main class="page-area">
    <div class="main-content">
        <section class="panel-header">
            <div>
                <p class="kicker">پنل مدیریت</p>
                <h1 class="display-font panel-title">مدیریت همه پست‌ها</h1>
                <p class="intro-copy">فهرست پست‌های همه نویسندگان؛ امکان ویرایش و حذف بر اساس دسترسی شما نمایش داده می‌شود.</p>
            </div>
        </section>

        @if (session('success'))
            <div class="alert alert-success mt-20">{{ session('success') }}</div>
        @endif

        <div class="news-grid mt-24">
            @forelse ($posts as $post)
                <article class="news-card">
                    @if ($post->images->isNotEmpty())
                        <div class="news-image-link">
                            <img src="{{ Storage::url($post->images->first()->path) }}" alt="{{ $post->title }}" class="news-image-cover news-image-cover--sm">
                        </div>
                    @endif

                    <div class="news-body">
                        <div class="category-tags">
                            @forelse ($post->categories as $category)
                                <span class="tag">{{ $category->name }}</span>
                            @empty
                                <span class="tag">عمومی</span>
                            @endforelse

                            @if ($post->isDraft())
                                <span class="tag tag--draft">پیش‌نویس</span>
                            @elseif ($post->isScheduled())
                                <span class="tag tag--scheduled">زمان‌بندی‌شده</span>
                            @else
                                <span class="tag tag--published">منتشرشده</span>
                            @endif
                        </div>

                        <h3>
                            @can('update', $post)
                                <a href="{{ route('panel.posts.show', $post) }}">{{ $post->title }}</a>
                            @else
                                {{ $post->title }}
                            @endcan
                        </h3>
                        <p class="news-excerpt">{{ Str::limit($post->description, 100) }}</p>
                        <p class="author">نویسنده: {{ $post->user->first_name }} {{ $post->user->last_name }}</p>

                        <div class="card-actions">
                            @can('update', $post)
                                <a href="{{ route('panel.posts.edit', $post) }}" class="btn btn-secondary action-btn">ویرایش</a>
                            @endcan

                            @can('delete', $post)
                                <form action="{{ route('panel.posts.destroy', $post) }}" method="POST" class="action-form" onsubmit="return confirm('آیا از حذف این پست مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger action-btn">حذف</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </article>
            @empty
                <div class="alert alert-info full-width">هنوز پستی ثبت نشده است.</div>
            @endforelse
        </div>

    </div>
</main>
@endsection
