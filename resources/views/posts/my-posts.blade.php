@extends('layout.master')

@section('header-actions')
    <a class="browse-link" href="{{ route('panel') }}">← بازگشت به صفحه قبل</a>
@endsection

@section('content')

<main class="page-area">
    <div class="main-content">

        <section class="intro" style="max-width:100%;display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;">
            <div>
                <p class="kicker">پنل من</p>
                <h1 class="display-font" style="font-size:clamp(2rem, 3.4vw, 3rem); margin:8px 0 0;">پست‌های من</h1>
                <p class="intro-copy">ابزارهایی که خودتان ثبت کرده‌اید.</p>
            </div>
            @can('permission', 'create-post')
            <a href="{{ route('create_post') }}" class="button button-primary">
                + ثبت ابزار جدید
            </a>
            @endcan
        </section>

        @if (session('success'))
            <div class="alert alert-success" style="margin-top:20px;">{{ session('success') }}</div>
        @endif

        <div class="container browse-grid" style="margin-top:24px; padding:0;">

            @forelse ($posts as $post)

                <article class="tool-card">

                    @if ($post->images->isNotEmpty())
                        <img
                            class="tool-image"
                            src="{{ asset('storage/' . $post->images->first()->path) }}"
                            alt="{{ $post->title }}"
                        />
                    @else
                        <div class="tool-image">
                            <p>تصویر موجود نیست</p>
                        </div>
                    @endif

                    <div class="tool-body">

                        @if ($post->category)
                            <span class="tag">{{ $post->category->name }}</span>
                        @endif

                        <h3>{{ $post->title }}</h3>
                        <p>{{ $post->description }}</p>

                        <div class="card-bottom">
                            <span class="price">{{ \Illuminate\Support\Number::format($post->first_day_price, 0, null, 'fa') }} تومان در روز</span>
                            <a href="{{ route('posts.show', $post) }}">جزئیات</a>
                        </div>

                        <div style="display:flex; gap:8px; margin-top:12px;">
                            <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary" style="flex:1; text-align:center;">ویرایش</a>

                            <form action="{{ route('posts.destroy', $post) }}" method="POST" style="flex:1;" onsubmit="return confirm('حذف شود؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn text-danger" style="width:100%; border:1px solid var(--line); background:none;">حذف</button>
                            </form>
                        </div>

                    </div>

                </article>

            @empty

                <div class="alert alert-info" style="grid-column:1/-1;">
                    هنوز هیچ ابزاری ثبت نکرده‌اید. 
                    @can('permission', 'create-post')
                    <a href="{{ route('create_post') }}">همین الان اولین ابزارت رو ثبت کن</a>.
                    @endcan
                </div>

            @endforelse

        </div>

    </div>
</main>

@endsection
