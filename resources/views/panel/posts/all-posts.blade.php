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
                        <div style="display:block; overflow:hidden; border-top-left-radius:var(--radius); border-top-right-radius:var(--radius);">
                            <img src="{{ Storage::url($post->images->first()->path) }}" alt="{{ $post->title }}" style="width:100%; height:180px; object-fit:cover; display:block; border-bottom:1px solid var(--line);">
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
                                <span class="tag" style="background:#fef3c7; color:#92400e;">پیش‌نویس</span>
                            @elseif ($post->isScheduled())
                                <span class="tag" style="background:#e0f2fe; color:#0369a1;">زمان‌بندی‌شده</span>
                            @else
                                <span class="tag" style="background:#dcfce7; color:#15803d;">منتشرشده</span>
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
