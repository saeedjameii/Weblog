@extends('layout.master')

@section('content')
    <main>
      <section class="browse-header">
        <div class="container">
          <div class="kicker">بازار</div>
          <h1 class="section-title">مرور ابزارها</h1>
          <p style="color: var(--muted); max-width: 650px; line-height: 1.7">
            ابزارهای مفید را از افراد جامعه خود پیدا کنید و دقیقاً همان چیزی را
            که نیاز دارید، امانت بگیرید.
          </p>
          <form class="search-box" method="GET" action="{{ route('posts.index') }}">
            <input
              class="search"
              type="search"
              name="search"
              value="{{ request('search') }}"
              placeholder="جستجو برای ابزارها..."/>
            <select class="select" name="category_id" onchange="this.form.submit()">
              <option value="">همه دسته‌بندی‌ها</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="button button-primary search-submit" type="submit">
                جست‌وجو
            </button>
          </form>
        </div>
      </section>
      <section>
        <div class="container browse-grid">
        @forelse ($posts as $post)
          <article class="tool-card">
            @if ($post->images->isNotEmpty())
                <img
                 class="tool-image"
                  src="{{ asset('storage/' . $post->images->first()->path) }}"
                  alt="{{ $post->title }}"
                />
            
            @else
                <div class='tool-image'>
                    <p>تصویر موجود نیست</p>
                </div>
            @endif
            <div class="tool-body">
              <span class="tag">امروز موجود</span>
              <h3>{{ $post->title }}</h3>
              <p>{{ $post->description }}</p>
              <small>مالک: {{ $post->user->first_name }}</small>
              <div class="card-bottom">
                <span class="price">{{ \Illuminate\Support\Number::format($post->first_day_price, 0, null, 'fa') }} تومان در روز</span>
                <a href="{{ route('posts.show', $post) }}">جزئیات بیشتر</a>
              </div>
            </div>
          </article>
        @empty
            <p>هیچ ابزاری برای نمایش وجود ندارد.</p>
        @endforelse
        </div>

        <div class="container">
            {{ $posts->links('pagination.custom') }}
        </div>
      </section>
    </main>
@endsection
