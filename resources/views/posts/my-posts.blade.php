@extends('layout.master')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        <section class="panel-header">
            <div>
                <p class="kicker">پنل من</p>
                <h1 class="display-font panel-title">اخبار و مقالات من</h1>
                <p class="intro-copy">لیست تمامی اخباری که شما در سایت منتشر کرده‌اید.</p>
            </div>
            @can('permission', 'create-post')
            <a href="{{ route('create_post') }}" class="button button-primary">
                + انتشار خبر جدید
            </a>
            @endcan
        </section>

        @if (session('success'))
            <div class="alert alert-success mt-20">{{ session('success') }}</div>
        @endif

        <div class="news-grid mt-24">

            @forelse ($posts as $post)

                <article class="news-card">
                    <div class="news-body">

                        <div class="category-tags">
                            @forelse ($post->categories as $category)
                                <span class="tag">{{ $category->name }}</span>
                            @empty
                                <span class="tag">عمومی</span>
                            @endforelse
                        </div>

                        <h3><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h3>
                        <p class="news-excerpt">{{ Str::limit($post->description, 100) }}</p>

                        <div class="card-bottom">
                            <a class="read-more" href="{{ route('posts.show', $post) }}">مشاهده خبر ←</a>
                        </div>

                        <div class="card-actions">
                            <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary action-btn">ویرایش</a>

                            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="action-form" onsubmit="return confirm('آیا از حذف این خبر مطمئن هستید؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger action-btn">حذف</button>
                            </form>
                        </div>

                    </div>
                </article>

            @empty

                <div class="alert alert-info full-width">
                    هنوز هیچ خبری منتشر نکرده‌اید. 
                    @can('permission', 'create-post')
                    <a href="{{ route('create_post') }}" class="fw-bold">همین الان اولین خبر خود را منتشر کنید</a>.
                    @endcan
                </div>

            @endforelse

        </div>

    </div>
</main>

@endsection