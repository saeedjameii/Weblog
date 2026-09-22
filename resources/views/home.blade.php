@extends('layout.master')

@section('page-actions')
    <a href="#latest-news">اخبار روز</a>
@endsection

@section('content')
<main>
  @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
  @endif
  
  <section class="hero">
    <div class="container hero-grid">
      <div>
        <div class="kicker">پوشش لحظه‌ای و دقیق رویدادها</div>
        <h1>مهم‌ترین اخبار ایران و جهان را اینجا بخوانید.</h1>
        <p>
          جدیدترین تحلیل‌ها، گزارش‌های اختصاصی و اخبار روز در حوزه‌های سیاست، تکنولوژی، اقتصاد و فرهنگ در خبرنامه.
        </p>
        <div class="hero-buttons">
          <a class="btn btn-primary" href="{{ route('posts.index') }}">مرور آخرین اخبار</a>
          @can('permission', 'create-post')
              <a class="btn btn-secondary" href="{{ route('create_post') }}">ارسال خبر جدید</a>
          @endcan
        </div>
      </div>
      <div class="hero-image">
        <img
          src="{{ asset('images/logo.png') }}"
          alt="News Portal"
        />
      </div>
    </div>
  </section>

  <section id="latest-news" class="section sage">
    <div class="container">
      <div class="section-head">
        <div>
          <div class="kicker">خبرهای داغ</div>
          <h2 class="section-title">تازه‌ترین مقالات و اخبار منتشر شده</h2>
        </div>
        <a class="btn btn-secondary" href="{{ route('posts.index') }}">آرشیو کامل اخبار</a>
      </div>
      
      <div class="news-grid">    
        @forelse ($latestPosts as $post)
          <article class="news-card">
          <div class="news-body">
            <div style="display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 10px;">
                @forelse ($post->categories as $category)
                    <span class="tag">{{ $category->name }}</span>
                @empty
                    <span class="tag">عمومی</span>
                @endforelse
            </div>
            <h3><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h3>
            <p class="news-excerpt">{{ Str::limit($post->description, 120) }}</p>
            <div class="card-bottom">
              <span class="author">✍️ {{ $post->user->first_name ?? 'تحریریه' }}</span>
              <a class="read-more" href="{{ route('posts.show', $post) }}">ادامه مطلب ←</a>
            </div>
         </div>
        </article>
        @empty
          <p>در حال حاضر خبری برای نمایش وجود ندارد.</p>
        @endforelse        
      </div>
    </div>
  </section>
</main>
@endsection
